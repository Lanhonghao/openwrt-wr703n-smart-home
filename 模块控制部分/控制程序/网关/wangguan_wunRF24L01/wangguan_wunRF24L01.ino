/*
 *NRF24l01针脚连接线
 * MISO -> 12
 * MOSI -> 11
 * SCK -> 13
 * Configurable:
 * CE -> 8
 * CSN -> 7
 
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD
*/
#include <SPI.h> //引用SPI库文件
//#include <Mirf.h> //引用Mirf库文件
//#include <nRF24L01.h> //引用NRF24L01库文件
//#include <MirfHardwareSpiDriver.h> //引用Mirf驱动库文件

int sid=1;//区别设备 1为网关
int nid=1;//设备编号 默认1

//声名变量
//openwrt串口通信处理
unsigned long Serial2nowlast;
char Serial2buff[129]={0};
char Serial2Data;
int Serial2i=0;

//无线串口通信处理(zigbee/bluetooth等)
unsigned long Serial1nowlast; //串口检测间隔时间
char Serial1buff[129]={0}; //串口缓存字符
char Serial1Data; //串口单个字符
int Serial1i=0; //串口字符数

//NRF24l01通信处理
//unsigned long nrf24l01nowlast; //NRF24L01检测间隔时间
//char nrf24l01buff[129]={0}; //NRF24L01缓存字符
//char nrf24l01Data; //NRF24L01单个字符
//int nrf24l01i=0; //NRF24L01字符数

void setup()
{
    Serial2.begin(115200);//openwrt串口通信处理
    Serial1.begin(115200);//无线串口通信处理(zigbee/bluetooth等)
    
    //NRF24l01设置
//    char server[10]={0};//服务端名称
//    sprintf(server,"serv%d",sid);
//    //初始化Mirf，用于NRF24l01收发
//    Mirf_Init(0,server,sid);
   
    Serial2.println("zwifi_wangguan");//启动串口打印
}
 
void loop()
{
  
  //检测openwrt串口数据处理 
  {  
      unsigned long Serial2now = millis();//获取现在的时间
      if(Serial2now - Serial2nowlast >= 5000)//如果数据间隔超过5秒而清空字符（为了防止数据错乱）
      { 
        Serial2nowlast = millis(); //记录当前时间
        memset(Serial2buff, 0, 129);//清空缓存字符
        Serial2i=0;//初始数组数
      } 
           
      while( Serial2.available() )//如果openwrt串口有数据
      {
        if(Serial2i==0)
        {
          Serial2.println("Serial2->");//打印出来方便调试
        }       
        Serial2Data=(char)Serial2.read();//读取串口数据
        //Serial2.print(Serial2Data);////这里不打印，否则检测到{ckxxxx}就认为是命令
        Serial2buff[Serial2i]=Serial2Data;////保存到数组
        Serial2i++;////数组长度+1
        if(Serial2Data=='}' || Serial2i>=129)//如果发现}而说明命令结束（并少于129个字符，太长会出错）
        {                
          Serial2nowlast = millis(); //更新当前时间，不然5秒就超时了
          
          char body[129]={0};//声明新字符数组
          get_znck_body(Serial2buff,body);//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
          //Serial2.println(body); //这里不打印，否则检测到{ckxxxx}就认为是命令
                    
          //如果命令格式真确则发送到无线串口
          if(strstr(body,"{ck") && strstr(body,"}") )
          {
            
            if(strlen(body)>10)
            {
              Serial1.println(body);//发送到无线串口
              
              //发送到NRF24l01
//              int sendsid=get_sid(body);//获取sid
//              char client[10]={0};//设置接收地址
//              sprintf(client,"clie%d",sendsid);//赋值客户端名称
//              Serial2.println(client);//打印
//             Mirf_Send(sendsid,client,body);//发送给NRF24L01
//              
//              char server[10]={0};//服务端名称
//              sprintf(server,"serv%d",sid);//赋值服务端名称
//              Mirf_Init(1,server,sid);//设置为接收模式
              
            }
            
          }
          Serial2i=0;//字符长度为0
//          Serial2.println("-------------------");
          
          delay(100);
        }
      }
      
  }

  //检测无线串口数据处理 (zigbee/bluetooth等)
  {  
      unsigned long Serial1now = millis();//获取现在的时间
      if(Serial1now - Serial1nowlast >= 5000)//如果数据间隔超过5秒而清空字符（为了防止数据错乱）
      { 
        Serial1nowlast = millis();//记录当前时间
        memset(Serial1buff, 0, 129);//清空缓存字符
        Serial1i=0;//初始数组数
      } 
           
      while( Serial1.available() )//如果无线串口有数据
      {
        if(Serial1i==0)
        {
          Serial1.println("Serial1->");//打印出来方便调试
        }       
        Serial1Data=(char)Serial1.read();//读取串口数据
        //Serial1.print(Serial1Data);////这里不打印，否则检测到{ckxxxx}就认为是命令
        Serial1buff[Serial1i]=Serial1Data;////保存到数组
        Serial1i++;////数组长度+1
        if(Serial1Data=='}' || Serial1i>=129)//如果发现}而说明命令结束（并少于129个字符，太长会出错）
        {                
          Serial1nowlast = millis(); //更新当前时间，不然5秒就超时了
          
          char body[129]={0};
          get_znck_body(Serial1buff,body);//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
          //Serial1.println(body);//这里不打印，否则检测到{ckxxxx}就认为是命令
                    
          //如果命令格式真确则发送到无线串口
          if(strstr(body,"{ck") && strstr(body,"}") )
          { 
            Serial2.println(body);//发送到openwrt串口
          }
          Serial1i=0;//字符长度为0
//          Serial1.println("-------------------");
          
          delay(100);
        }
      }
      
  }
  
  
  //NRF24l01数据收发
//  unsigned long nrf24l01now = millis();//获取现在的时间
//  if(nrf24l01now - nrf24l01nowlast >= 5000)//如果数据间隔超过5秒而清空字符（为了防止数据错乱）
//  { 
//     nrf24l01nowlast = millis();//记录当前时间
//     memset(nrf24l01buff, 0, 129);//清空缓存字符
//     nrf24l01i=0;//初始数组数
//   }
//      
//  byte data[Mirf.payload];//声明字节
//  if(Mirf.dataReady()){ //如果NRF24l01有数据
//       
//    Mirf.getData(data);   //获取数据
//    Mirf.rxFifoEmpty();   //清理24L01援存
//    //Serial2.print((char)*data);//这里不打印，否则检测到{ckxxxx}就认为是命令
//      
//    for (int i = 0; i < Mirf.payload; i++) //把收到的信息拼起来
//    {            
//      if(nrf24l01i==0)
//      {
//        Serial2.println("nrf24l01->");//打印出来方便调试
//      }      
//      
//      nrf24l01Data=(char)data[i];//转成字符
//      Serial2.print(nrf24l01Data);
//      if( nrf24l01Data=='{') nrf24l01i=0;//设置开始字符
//      nrf24l01buff[nrf24l01i]=nrf24l01Data;////保存到数组
//      nrf24l01i++;//数组长度+1
//      if(nrf24l01Data=='}' || nrf24l01i>=129 )//如果发现}而说明命令结束（并少于129个字符，太长会出错）
//      {
//            nrf24l01nowlast = millis(); //更新当前时间，不然5秒就超时了
//            
//            char body[129]={0};
//            get_znck_body(nrf24l01buff,body);//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
//            //Serial2.println(body); 
//           
//            //如果命令格式真确则发送到无线串口
//            if(strstr(body,"{ck") && strstr(body,"}") )
//            { 
//              Serial2.println(body);//发送到openwrt串口
//            }
//            memset(nrf24l01buff, 0, 129);//初始化数组
//            nrf24l01i=0;//字符长度为0
//            Serial2.println("-------------------");
//            
//            delay(100);
//      }
//    }  
//  }
  
  
}

//初始化Mirf 0初始化1为接收2为发送
//void Mirf_Init(int txrx,char *server,int channel){
//    //初始化Mirf，用于NRF24l01收发        
//    if(txrx==0)  {     
//      Mirf.spi = &MirfHardwareSpi;//设置spi
//      Mirf.init();//初始Mirf
//      Mirf.setRADDR((byte *)server);//设置接收地址
//    }
//    //设置接收模式
//    if(txrx==1)  {     
//      Mirf.setRADDR((byte *)server);//设置接收地址
//    }
//    //设置发送模式
//    if(txrx==2)  {
//      Mirf.setTADDR((byte *)server);//设置发送地址
//    }
//    
//    Mirf.payload = sizeof(char);//收发字节
//    Mirf.channel = channel;//设置频道
//    Mirf.config();//生效配置
//}

//NRF24l01发送函数
//void Mirf_Send(int channel,char *server,char *str){
//  Mirf_Init(2,server,channel);
//  int bufi=0;
//  for(bufi=0;bufi<strlen(str);bufi++){//循环发送
//    char words=str[bufi];//发送的字符
//    Mirf.send((byte *)&words);//发送命令
//    while(Mirf.isSending()){//等待发送完闭
//    }
//    delay(50);//延时，否则可能出现发送丢失现象
//    //Serial2.print(words);
//  }
//  //Serial2.println(""); 
//}


//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
void get_znck_body(char *p,char *s){
  
  char rechar[33]={0};//声明数组
  int bufi=0;//当前长度
  
  bool isend=false;//开始和结束
  int charnum=0;//字符长度   
  
  for(bufi=0;bufi<strlen(p);bufi++){//循环截取指定字符
    //Serial2.print(p[bufi]);
    
    if(p[bufi]=='{'){//如果是{则开始截取
      isend=true;//设为ture
    }
    if(p[bufi]=='}' && isend==true){//如果是}则结束截取
      isend=false;//设为false
      rechar[charnum]=p[bufi];//赋值到新数组
      break;
    }
    if(isend){//如果为true则赋值
      if(charnum<33)//小于指定长度
      {
        rechar[charnum]=p[bufi];//赋值到新数组
        charnum++;//字符长度加1
      }
    }
    
  }
  //Serial2.println(""); 
  //memcpy(s,rechar,33);
  sprintf(s,"%s",rechar);//赋值返回
}


//获取sid函数
int get_sid(char *buff){
    
  if( strstr(buff,"{ck") && strstr(buff,"}") && strlen(buff)>10)//如果含{ck和}，并长度大于10
    {
      char charSid[4]={0};//声明数组
      memcpy(charSid,buff+3,3);//赋值截取第3位后面的3个字符
      Serial2.println(charSid);//打印
      int intSid=atoi(charSid);//转成int
      Serial2.println(intSid);//打印
      return intSid;//返回sid
    }
    else
    {
      return 0;//格式不对返回0
    }
  
}

//获取nid函数
int get_nid(char *buff){
    
  if( strstr(buff,"{ck") && strstr(buff,"}") && strlen(buff)>10)//如果含{ck和}，并长度大于10
    {
      char charNid[4]={0};//声明数组
      memcpy(charNid,buff+6,3);//赋值截取第6位后面的3个字符
      Serial2.println(charNid);//打印
      int intNid=atoi(charNid);//转成int
      Serial2.println(intNid);//打印
      return intNid;//返回nid  
    }
    else
    {
      return 0;//格式不对返回0
    }
  
}

//获取data函数
int get_data(char *buff){
    
  if( strstr(buff,"{ck") && strstr(buff,"}") && strlen(buff)>10)//如果含{ck和}，并长度大于10
    {
      char charData[4]={0};//声明数组
      memcpy(charData,buff+9,1);//赋值截取第9位后面的1个字符
      Serial2.println(charData);//打印
      int intData=atoi(charData);//转成int
      Serial2.println(intData);//打印
      return intData;//返回data      
    }
    else
    {
      return 0;//格式不对返回0
    }
  
}

