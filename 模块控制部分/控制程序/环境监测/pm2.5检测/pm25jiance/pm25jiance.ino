/*
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD
 
 *GP2Y1050AU灰尘传感器针脚连接线
 GND -> GND
 VCC -> VCC
 NC
 NC
 RXD  -> A1
 TXD  -> A0 
*/
#include <SPI.h> //引用SPI库文件
#include <SoftwareSerial.h> //引用软串口库文件

int sid=1;//模块类型
int nid=3;//模块编号

//声名变量
const unsigned long interval = 1000;//时间间隔
unsigned long last_sent;//最后发送时间

float pm25=0;//声明pm2.5变量

int dustPin=0;
float dustVal=0;
int ledPower=2;
int delayTime=280;
int delayTime2=40;
float offTime=9680;


void setup()
{
    Serial.begin(115200); //设置串口波特率
    pinMode(ledPower,OUTPUT);
    pinMode(dustPin, INPUT);
    Serial.println("228_home_pm25jiance");//打印
}
 
void loop()
{
  
  unsigned long now = millis();//获取现在的时间
  if ( now - last_sent >= interval  ) //如果超过时间间隔
  {
    last_sent = now;//保存到最后时间
    
    pm25=0.00;//初始化0
    run_pm25();//调用函数pm2.5获取数据
    if(pm25>0){//如果pm2.5大于0
    char pmstr[10]={0};//声明字符
    dtostrf(pm25,4,2,pmstr);//格式化字符
    
    char data[10]={0};//声明数据字符
    sprintf(data,"%s",pmstr);//格式化字符
    send_data(data);//调用函数发送数据到网关
      
    }
    
  }
  
  
}


//获取pm2.5传感器的数据函数
void run_pm25()
{
// ledPower is any digital pin on the arduino connected to Pin 3 on the sensor
digitalWrite(ledPower,LOW); 
delayMicroseconds(delayTime);
dustVal=analogRead(dustPin); 
delayMicroseconds(delayTime2);
digitalWrite(ledPower,HIGH); 
delayMicroseconds(offTime);
delay(1000);
//if (dustVal>36.455)
Serial.println((float(dustVal/1024)-0.0356)*120000*0.035);
pm25=dustVal;

}

//发送数据到网关的函数
void send_data(char *data){
     
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
    char client[10]={0};//声明客户端
    sprintf(client,"clie%d",sid);//赋值客户端名称          
}
