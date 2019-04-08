/*
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD
 
 *MQ-2传感器针脚连接线 
 VCC -> VCC 
 GND -> GND
 DO 
 AO  -> A3
 
 *HC-SR501人体检测针脚连接线 
 VCC -> VCC 
 DO  -> A2
 GND -> GND
 
  *DS18B20温度传感器针脚连接线
 GND -> GND
 DQ -> A5
 VDD -> VCC
 
 *DHT11数字温湿度传感器针脚连接线
 VDD -> VCC
 DATA -> A4
 NC -> 空脚
 GND -> GND
 
 *GP2Y1050AU灰尘传感器针脚连接线
 GND -> GND
 VCC -> VCC
 NC
 NC
 RXD  -> A1
 TXD  -> A0 
*/
#include <SPI.h> //引用SPI库文件
#include <OneWire.h> //引用DS18B20库文件
#include <SoftwareSerial.h> //引用软串口库文件

//声名变量
const unsigned long interval = 1000;//间隔时间
unsigned long last_sent;//最后发送时间

//MQ-2
int mq_2alarm=0;//报警值
int mq_2val=0;//MQ-2数值
int mq_2_PIN=A3;//MQ-2针脚

//HC-SR501传感
int rentival=0;//HC-SR501传感数值
int renti_PIN=A2;//HC-SR501传感针脚

//湿度温度
OneWire  ds(A5); //声明ds变量
float celsius=0.0; //摄氏温度
float fahrenheit=0.0; //华氏温度
int DHT11_PIN=4; //DHT11针脚
int humdity=0; //湿度
int temperature=0; //温度

//GP2Y1050AU灰尘传感器
SoftwareSerial mySerial(A0, A1); //定义软串口，对应RX,TX

float pm25=0.00;//声明pm2.5变量
int incomeByte[6];//声明pm2.5字节
int data;//当前数据
int z=0;//当前字符数
int sum;//数据总和
unsigned long error;//错误


void setup()
{
    Serial.begin(115200);//设置串口波特率115200
    
    pinMode(renti_PIN, INPUT);//设置输入模式
    
    ini_dht11(); //初始化dht11
    
     mySerial.begin(2400);//设置软串口的波特率
    
    Serial.println("228_smart home_huanjingjiance_zong");//打印
}
 
void loop()
{
  
  unsigned long now = millis();//获取现在的时间
  if ( now - last_sent >= interval  ) //如果超过时间间隔
  {
    last_sent = now;//保存到最后时间
    
    //湿度温度   
    wenshidu();
    delay(1000);//延时
    //GP2Y1050AU灰尘传感器
    pm_25();
    delay(1000);//延时 
    //HC-SR501红外传感
    hongwai();
    delay(1000);//延时
    //MQ-2
    mq_2();
    delay(1000);//延时 
  }
}


//GP2Y1050AU灰尘传感器
void pm_25()
{
    pm25=0.00;//初始化0
    run_pm25();//调用函数pm2.5获取数据
    if(pm25>0){//如果pm2.5大于0
      char pmstr[10]={0};//声明字符
      dtostrf(pm25,4,2,pmstr);//格式化字符
      
      char data[10]={0};//声明数据字符
      sprintf(data,"%s",pmstr);//格式化字符
      send_data(data,1,3);//调用函数发送数据到网关
      
    }
}

//湿度温度   
void wenshidu()
{
    celsius=0;//初始化0
    humdity=0;//初始化0
    run_ds18();//调用函数DS18B20获取温度
    if(celsius>0){//如果温度大于0
       run_dht11();//调用函数dht11获取湿度
    }
    if(humdity>0){//如果湿度大于0
      char celstr[10]={0};//声明字符
      dtostrf(celsius,4,2,celstr);//格式化字符
      char data[10]={0};//声明数据字符
      sprintf(data,"%d.%s",humdity,celstr);//格式化字符 湿度.温度   
      send_data(data,1,2);//调用函数发送数据到网关
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

//获取HC-SR501红外感器的数据函数
void run_renti()
{    
    rentival=digitalRead(renti_PIN);//读取
    //Serial.println(rentival,DEC);//打印
    delay(100);//延时
}

//获取DS18S20传感器的温度函数
void run_ds18()
{
  byte i;
  byte present = 0;
  byte type_s;
  byte data[12];
  byte addr[8];
  //float celsius, fahrenheit;
  
  if ( !ds.search(addr)) {
    //Serial.println("No more addresses.");
    //Serial.println();
    ds.reset_search();
    delay(250);
    return;
  }
  
  //Serial.print("ROM =");
  for( i = 0; i < 8; i++) {
    //Serial.write(' ');
    //Serial.print(addr[i], HEX);
  }

  if (OneWire::crc8(addr, 7) != addr[7]) {
      Serial.println("CRC is not valid!");
      return;
  }
  //Serial.println();
 
  // the first ROM byte indicates which chip
  switch (addr[0]) {
    case 0x10:
      //Serial.print("Chip = DS18S20");  // or old DS1820
      type_s = 1;
      break;
    case 0x28:
      //Serial.print("Chip = DS18B20");
      type_s = 0;
      break;
    case 0x22:
      //Serial.print("Chip = DS1822");
      type_s = 0;
      break;
    default:
      Serial.print("Device is not a DS18x20 family device.");
      return;
  } 

  ds.reset();
  ds.select(addr);
  ds.write(0x44,1);         // start conversion, with parasite power on at the end
  
  delay(1000);     // maybe 750ms is enough, maybe not
  // we might do a ds.depower() here, but the reset will take care of it.
  
  present = ds.reset();
  ds.select(addr);    
  ds.write(0xBE);         // Read Scratchpad
  
  //Serial.print("  Data = ");
  //Serial.print(present,HEX);
  //Serial.print(" ");
  for ( i = 0; i < 9; i++) {           // we need 9 bytes
    data[i] = ds.read();
    //Serial.print(data[i], HEX);
    //Serial.print(" ");
  }
  //Serial.print(" CRC=");
  //Serial.print(OneWire::crc8(data, 8), HEX);
  //Serial.println();
  
  // convert the data to actual temperature

  unsigned int raw = (data[1] << 8) | data[0];
  if (type_s) {
    raw = raw << 3; // 9 bit resolution default
    if (data[7] == 0x10) {
      // count remain gives full 12 bit resolution
      raw = (raw & 0xFFF0) + 12 - data[6];
    }
  } else {
    byte cfg = (data[4] & 0x60);
    if (cfg == 0x00) raw = raw << 3;  // 9 bit resolution, 93.75 ms
    else if (cfg == 0x20) raw = raw << 2; // 10 bit res, 187.5 ms
    else if (cfg == 0x40) raw = raw << 1; // 11 bit res, 375 ms
    // default is 12 bit resolution, 750 ms conversion time
  }
  celsius = (float)raw / 16.0;
  fahrenheit = celsius * 1.8 + 32.0;
  //Serial.print("  Temperature = ");
  //Serial.print(celsius);
  //Serial.print(" Celsius, ");
  //Serial.print(fahrenheit);
 // Serial.println(" Fahrenheit");     
}

//初始化dht11函数
void ini_dht11()
{
  DDRC|=_BV(DHT11_PIN);
  PORTC|=_BV(DHT11_PIN);
}

//读获dht11数据函数
byte read_dht11_dat()
{
  byte i = 0;
  byte result = 0;
  for(i=0;i<8;i++)
  {
  while(!(PINC&_BV(DHT11_PIN)));
  delayMicroseconds(30);
  if(PINC&_BV(DHT11_PIN))
  result|=(1<<(7-i));
  while((PINC&_BV(DHT11_PIN)));
  }
  return result;
}

//获取dht11传感器湿度的函数
void run_dht11()
{
  byte dht11_dat[5];
  byte dht11_in;
  byte i;
  PORTC &= ~_BV(DHT11_PIN);
  delay(18);
  PORTC|=_BV(DHT11_PIN);
  delayMicroseconds(40);
  DDRC &= ~_BV(DHT11_PIN);
  delayMicroseconds(40);
  dht11_in = PINC & _BV(DHT11_PIN);
  if(dht11_in)
  {
  Serial.println("dht11 start condition 1 not met");
  return;
  }
  delayMicroseconds(80);
  dht11_in=PINC & _BV(DHT11_PIN);
  if(!dht11_in)
  {
    Serial.println("dht11 start condition 2 not met");
    return;
  }
  delayMicroseconds(80);
  for(i=0;i<5;i++)
  dht11_dat[i]=read_dht11_dat();
  DDRC|=_BV(DHT11_PIN);
  PORTC|=_BV(DHT11_PIN);
  byte dht11_check_sum = dht11_dat[0]+dht11_dat[1]+dht11_dat[2]+dht11_dat[3];
  if(dht11_dat[4]!=dht11_check_sum)
  {
  Serial.println("DHT11 checksum error");
  }
  //Serial.print("Chip = DHT11  Current humdity= ");
//  Serial.print(dht11_dat[0],DEC);
//  Serial.print(".");
//  Serial.print(dht11_dat[1],DEC);
//  Serial.print("%");
//  Serial.print("temperature = ");
//  Serial.print(dht11_dat[2],DEC);
//  Serial.print(".");
//  Serial.print(dht11_dat[3],DEC);
//  Serial.println("C"); 
  humdity=(int)dht11_dat[0];
  temperature=(int)dht11_dat[2];
}

//获取pm2.5传感器的数据函数
void run_pm25()
{
    while (mySerial.available()){//如果软串口存在数据
  
       data=mySerial.read();//读取软串口数据
       if(data == 170){//如果数据是170则是开始
         z=0;//当前字符数
         incomeByte[z]=data;//保存到当前字节
       }else{//如果不是开始
         z++;//当前字符数+1
         incomeByte[z]=data;//保存到当前字节
       } 
       if(z==6)//如果满6个字符
       {
            sum=incomeByte[1]+ incomeByte[2]+ incomeByte[3] + incomeByte[4];//字节数相加，为了效验数据
        
            if(incomeByte[5]==sum && incomeByte[6]==255 )//效验数据格式
            {
               Serial.print("Data OK! ");
               Serial.print(" | Vo=");
               float vo=(incomeByte[1]*256.0+incomeByte[2])/1024.0*5.00;//计算pm2.5算法
               Serial.print(vo,2);
               Serial.print("V  ");
               float v1=analogRead(A0)/1024.0*5.00;
               Serial.print("/ Ao=");
               Serial.print(v1,2);
               Serial.print("V  | ");
               float c=vo*200; 
               pm25=c;//保存pm2.5变量
               Serial.print(c,2);
               Serial.print("ug/m3 | ");
               for(int k=0;k<7;k++)
                {
                  Serial.print(incomeByte[k]);//打印原始数据
                  Serial.print("|");
                 } 
                 Serial.println();                
                 break;
             }else{
                 z=0;
                 Serial.flush();
                 data='/0';
                 for(int m=0;m<7;m++){incomeByte[m]=0;}
                 //error++;
                 //Serial.print(" ### This is ");
                 //Serial.print(error);
                 //Serial.println(" Error ###");
             }
             z=0;
       }
    }
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
