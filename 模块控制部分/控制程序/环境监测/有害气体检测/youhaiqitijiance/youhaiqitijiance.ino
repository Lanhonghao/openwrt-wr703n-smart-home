/*
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD
 
 *MQ-2传感器针脚连接线 
 VCC -> VCC 
 GND -> GND
 DO 
 AO  -> A3
*/

//声名变量
const unsigned long interval = 1000;//间隔时间
unsigned long last_sent;//最后发送时间

//MQ-2
int mq_2alarm=0;//报警值
int mq_2val=0;//MQ-2数值
int mq_2_PIN=A3;//MQ-2针脚

void setup()
{
    Serial.begin(115200);//设置串口波特率115200   
    Serial.println("228_smart home_youhaiqitijiance");//打印
}
 
void loop()
{
  unsigned long now = millis();//获取现在的时间
  if ( now - last_sent >= interval  ) //如果超过时间间隔
  {
    last_sent = now;//保存到最后时间
    mq_2(); //MQ-2
    delay(1000);//延时 
  }
}

 //MQ-2
void mq_2()
{
    mq_2val=0;//初始化0
    run_mq_2();//调用函数获取mq-2数据
    if(mq_2val>mq_2alarm){//如果值大于警报值则发送警报
      char data[10]={0};//声明字符
      sprintf(data,"%d",mq_2val);//格式化字符 
      send_data(data,1,5);//调用函数发送数据到网关
    }
}

//获取MQ-2传感器的数据函数
void run_mq_2()
{
    mq_2val=analogRead(mq_2_PIN);//读取
    //Serial.println(mq_2val,DEC);//打印
    delay(100);//延时
}

//发送数据到网关的函数
void send_data(char *data,int sid,int nid)
{
    char server[10]={0};//声明网关接收名称
    sprintf(server,"serv%d",1);//赋值网关接收名称
    //Serial.println(server);//打印
    
    char updateData[33]={0};//声明更新数组
    char front[10]={0};//声明更新前缀
    //memcpy(front,body,9);
    sprintf(front," {ck%03d%03d",sid,nid);//赋值前缀
    sprintf(updateData,"%s%s}",front,data);//生成反馈数组 
    Serial.println(updateData);//发送到串口（zigbee会发给网关）
    Serial.println();//换行            
}
