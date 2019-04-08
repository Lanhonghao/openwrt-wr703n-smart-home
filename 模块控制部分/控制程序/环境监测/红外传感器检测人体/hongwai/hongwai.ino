/*
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD

 *HC-SR501人体检测针脚连接线 
 VCC -> VCC 
 DO  -> A2
 GND -> GND
*/

//声名变量
const unsigned long interval = 1000;//间隔时间
unsigned long last_sent;//最后发送时间

//HC-SR501传感
int rentival=0;//HC-SR501传感数值
int renti_PIN=A2;//HC-SR501传感针脚

void setup()
{
    Serial.begin(115200);//设置串口波特率115200
    pinMode(renti_PIN, INPUT);//设置输入模式
    Serial.println("228_smart home_hongwai");//打印
}
 
void loop()
{
  
  unsigned long now = millis();//获取现在的时间
  if ( now - last_sent >= interval  ) //如果超过时间间隔
  {
    last_sent = now;//保存到最后时间
   
    //HC-SR501红外传感
    hongwai();
    delay(1000);//延时
  }
}

//红外传感
void hongwai()
{
    rentival=0;//初始化0   
    run_renti();//调用函数获取HC-SR501红外传感数据
    if(rentival==HIGH){//如果是警报值则发送警报，这里高电平是警报
      char data[10]={0};//声明字符
      sprintf(data,"%d",rentival);//声明字符
      send_data(data,1,4);//调用函数发送数据到网关
    }
}

//获取HC-SR501红外感器的数据函数
void run_renti()
{    
    rentival=digitalRead(renti_PIN);//读取
    //Serial.println(rentival,DEC);//打印
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
