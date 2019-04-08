/*
 *ZIGBEE针脚连接线
 TX -> RXD
 RX -> TXD
*/
#include <SPI.h> //引用SPI库文件
#include <EEPROM.h> //引用EEPROM库文件

int sid=3;//模块类型
int nid=0;//模块编号


//无线串口通信处理(zigbee/bluetooth等)
unsigned long serial1nowlast; //串口检测间隔时间
char serial1buff[129]={0}; //串口缓存字符
char serial1Data; //串口单个字符
int serial1i=0; //串口字符数

//NRF24L01
//unsigned long nrf24l01nowlast; //NRF24L01检测间隔时间
//char nrf24l01buff[33]={0}; //NRF24L01缓存字符
//char nrf24l01Data; //NRF24L01单个字符
//int nrf24l01i=0; //NRF24L01字符数


//声名变量
int pinIn0=3; //手动开关针脚1
int pinIn1=4; //手动开关针脚2
int pinIn2=5; //手动开关针脚3

int val0; //当前开关值1
int val1; //当前开关值2
int val2; //当前开关值3

int pinOut0=A0; //开关针脚1
int pinOut1=A1; //开关针脚2
int pinOut2=A2; //开关针脚3

int state0=0; //开关状态1
int state1=0; //开关状态2
int state2=0; //开关状态3


void setup()
{
    Serial.begin(115200); //设置串口波特率115200

    //初始化Mirf，用于NRF24L01收发
//    char client[10]={0}; //客户端名称
//    sprintf(client,"clie%d",sid); //设置客户端名称
//    Mirf_Init(0,client,sid); //初始化NRF24L01收发
    
    pinMode(pinIn0,INPUT); //设置输入模式
    pinMode(pinIn1,INPUT); //设置输入模式
    pinMode(pinIn2,INPUT); //设置输入模式
    
    pinMode(pinOut0,OUTPUT); //设置输出输式
    pinMode(pinOut1,OUTPUT); //设置输出输式
    pinMode(pinOut2,OUTPUT); //设置输出输式
    
    pinMode(pinIn0,INPUT_PULLUP); //将管脚设置为输入并且内部上拉模式
    pinMode(pinIn1,INPUT_PULLUP); //将管脚设置为输入并且内部上拉模式
    pinMode(pinIn2,INPUT_PULLUP); //将管脚设置为输入并且内部上拉模式 
    
    digitalWrite(pinIn0, HIGH); //设置默认高电位
    digitalWrite(pinIn1, HIGH); //设置默认高电位
    digitalWrite(pinIn2, HIGH); //设置默认高电位
    
    state0 = EEPROM.read(0); //读取rom开关电位
    state1 = EEPROM.read(1); //读取rom开关电位
    state2 = EEPROM.read(2); //读取rom开关电位
    
    Serial.println("228wifi_kaiguan"); //打印
    
    Serial.print("state0="); 
    Serial.print(state0); //打印当前电位状态1
    Serial.print("/state1=");
    Serial.print(state1); //打印当前电位状态2
    Serial.print("/state2=");
    Serial.println(state2); //打印当前电位状态3
    
    if(state0==255) state0=0; //默认rom则设为关状态
    if(state1==255) state1=0; //默认rom则设为关状态
    if(state2==255) state2=0; //默认rom则设为关状态
    
}
 
void loop()
{
  
  //检测无线串口数据处理 (zigbee/bluetooth等)
  {  
      unsigned long serial1now = millis();//获取现在的时间
      if(serial1now - serial1nowlast >= 5000)//如果数据间隔超过5秒而清空字符（为了防止数据错乱）
      { 
        serial1nowlast = millis();//记录当前时间
        memset(serial1buff, 0, 129);//清空缓存字符
        serial1i=0;//初始数组数
      } 
      
      while( Serial.available() )//如果无线串口有数据
      {
        if(serial1i==0)
        {
          Serial.println("serial->");//打印出来方便调试
        }       
        serial1Data=(char)Serial.read();//读取串口数据
        //Serial.print(serial1Data);//这里不打印，否则检测到{ckxxxx}就认为是命令
        serial1buff[serial1i]=serial1Data;////保存到数组
        serial1i++;//数组长度+1
        if(serial1Data=='}' || serial1i>=129)//如果发现}而说明命令结束（并少于129个字符，太长会出错）
        {                
          serial1nowlast = millis(); //记录当前时间，不然5秒就超时了
          
          char body[129]={0};//声明新字符数组
          get_znck_body(serial1buff,body);//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
          //serial.println(body); //这里不打印，否则检测到{ckxxxx}就认为是命令
                    
          //如果命令格式正确则做处理
          if(strstr(body,"{ck") && strstr(body,"}") )
          { 
            //Serial.println(body);//这里不打印，否则检测到{ckxxxx}就认为是命令
            
            if(strlen(body)>10) //字符数需大于10
            {
              set_onoff(body);//设置开或关              
              
            }

          }
          serial1i=0;//字符数组长度初始为0
          Serial.println("-------------------");//打印结束标示
          
          delay(100);//延时
        }
      }
      
  }
  
//  unsigned long nrf24l01now = millis();//获取现在的时间
//  if(nrf24l01now - nrf24l01nowlast >= 5000)//如果数据间隔超过5秒而清空字符（为了防止数据错乱）
//  { 
//     nrf24l01nowlast = millis();//记录当前时间
//     memset(nrf24l01buff, 0, 33);//清空缓存字符
//     nrf24l01i=0;//初始数组数
//   }
//      
//   byte data[Mirf.payload];//声明字节
//   if(Mirf.dataReady()){//如果有数据
//   
//    Mirf.getData(data);//获取数据
//    Mirf.rxFifoEmpty();//清理NRF24L01援存
//    //Serial.println((char)*data);//这里不打印，否则检测到{ckxxxx}就认为是命令
//   
//    for (int i = 0; i < Mirf.payload; i++) //把收到的信息拼起来
//    {          
//      if(nrf24l01i==0)
//      {
//        Serial.println("nrf24l01->");//打印出来方便调试
//      }
            
//      nrf24l01Data=(char)data[i];//读取NRF24L01数据
//      if( nrf24l01Data=='{') nrf24l01i=0;//如果发现{则设长度为0开始
//      nrf24l01buff[nrf24l01i]=nrf24l01Data;//保存到数组
//      nrf24l01i++;//数组长度+1
//      if(nrf24l01Data=='}' || nrf24l01i>=33)//如果发现}而说明命令结束（并少于33个字符，太长会出错）
//      {                
//            nrf24l01nowlast = millis(); //更新当前时间，不然5秒就超时了
//            
//            char body[33]={0};//声明新数组
//            get_znck_body(nrf24l01buff,body);//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
//            //Serial.println(body);//这里不打印，否则检测到{ckxxxx}就认为是命令
//                      
//            //如果命令格式正确则做处理
//            if(strstr(body,"{ck") && strstr(body,"}") )
//            { 
//              //Serial.println(body);//这里不打印，否则检测到{ckxxxx}就认为是命令
//              
//              if(strlen(body)>10) //字符数需大于10
//              {
//                  set_onoff(body);//设置开或关
//                
//              }
//              
//            }
//            memset(nrf24l01buff, 0, 33);//字符数组长度初始为0
//            nrf24l01i=0;//字符数组长度初始为0
//            Serial.println("-------------------");//打印结束标示
            
            delay(100);//延时
//      }
//    }
//   }    
  
  val0=digitalRead(pinIn0);//读取输入数据
  if(val0==LOW)//检测按键是否按下
  {
    delay(200);//延时，防抖处理
    val0=digitalRead(pinIn0);//读取数据
    if(val0==LOW)//检测按键是否按下
    {
      Serial.print("val0 state0=");
      Serial.println(state0);//打印
      if(state0==0)//如果原来是关状态
      {  
         state0=1;//则状态值为开
         digitalWrite(pinOut0,HIGH);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d000%d}",sid,state0);//生成手动开关字符         
         set_onoff(handonoff);//设置开或关         
         delay(2000);//延时2000毫秒
      }
      else//如果原来是开状态
      {          
         state0=0;//则状态值为关
         digitalWrite(pinOut0,LOW);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d000%d}",sid,state0);//生成手动开关字符         
         set_onoff(handonoff);//设置开或关
         delay(2000);//延时2000毫秒
      }
    }
  }
  
  val1=digitalRead(pinIn1);//读取数字  
  if(val1==LOW)//检测按键是否按下
  {
    delay(200);//延时，防抖处理
    val1=digitalRead(pinIn1);//读取数字
    if(val1==LOW)//检测按键是否按下
    {
      Serial.print("val1 state1=");
      Serial.println(state1);//打印
      if(state1==0)//如果原来是关状态
      {             
         state1=1;//则状态值为开
         digitalWrite(pinOut1,HIGH);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d001%d}",sid,state1);//生成手动开关字符         
         set_onoff(handonoff);//设置开或关
         delay(2000);//延时2000毫秒
      }
      else//如果原来是开状态
      {          
         state1=0;//则状态值为关
         digitalWrite(pinOut1,LOW);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d001%d}",sid,state1);//生成手动开关字符
         set_onoff(handonoff);//设置开或关
         delay(2000);//延时2000毫秒
      }
    }
  }
  
  val2=digitalRead(pinIn2);//读取数字
  if(val2==LOW)//检测按键是否按下
  {
    delay(200);//延时，防抖处理
    val2=digitalRead(pinIn2);//读取数字
    if(val2==LOW)//检测按键是否按下
    {
      Serial.print("val2 state2=");
      Serial.println(state2);//打印
      if(state2==0)//如果原来是关状态
      {             
         state2=1;//则状态值为开
         digitalWrite(pinOut2,HIGH);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d002%d}",sid,state2);//生成手动开关字符         
         set_onoff(handonoff);//设置开或关  
         delay(2000);//延时2000毫秒
      }
      else//如果原来是开状态
      {          
         state2=0;//则状态值为关
         digitalWrite(pinOut2,LOW);//则设为高电位
         char handonoff[33]={0};//声明手动开关字符       
         sprintf(handonoff,"{ck%03d002%d}",sid,state2);//生成手动开关字符         
         set_onoff(handonoff);//设置开或关
         delay(2000);//延时2000毫秒
      }
    }
  }
  
    
  
  //根据状态设置开或关（防漏）
  if(state0==1)
  {
    digitalWrite(pinOut0,HIGH);//则设为高电位
  }
  else
  {      
    digitalWrite(pinOut0,LOW);//则设为低电位
  }
  
  //ON OFF SET
  if(state1==1)
  {
    digitalWrite(pinOut1,HIGH);//则设为高电位
  }
  else
  {      
    digitalWrite(pinOut1,LOW);//则设为低电位   
  }
  
  //ON OFF SET
  if(state2==1)
  {
    digitalWrite(pinOut2,HIGH);//则设为高电位
  }
  else
  {      
    digitalWrite(pinOut2,LOW);//则设为低电位    
  }
  
}

//初始化Mirf 0初始化1为接收2为发送
//void Mirf_Init(int txrx,char *server,int channel){
//    //初始化Mirf        
//    if(txrx==0)  {
//      Mirf.spi = &MirfHardwareSpi;//设置spi
//      Mirf.init();//初始Mirf
//      Mirf.setRADDR((byte *)server);//设置接收地址
//    }
//    
//    //设置接收模式
//    if(txrx==1)  {     
//      Mirf.setRADDR((byte *)server);//设置接收地址
//    }
//    
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
//  Mirf_Init(2,server,channel);//设置发送模式
//  int bufi=0;//声明变量
//  for(bufi=0;bufi<strlen(str);bufi++){//循环发送
//    char words=str[bufi];//发送的字符
//    Mirf.send((byte *)&words);//发送命令
//    while(Mirf.isSending()){//等待发送完闭
//    }
//    delay(50);//延时，否则可能出现发送丢失现象
//    //Serial.print(words);
//  }
//  //Serial.println(""); 
//}


//获取只是{ckxxxxxx}的字符,因为这是我们的命令格式 
void get_znck_body(char *p,char *s){
  
  char rechar[33]={0};//声明数组
  int bufi=0;//当前长度
  
  bool isend=false;//开始和结束
  int charnum=0;//字符长度   
  
  for(bufi=0;bufi<strlen(p);bufi++){//循环截取指定字符
    //Serial.print(p[bufi]);
    
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
  //Serial.println(""); 
  //memcpy(s,rechar,33);
  sprintf(s,"%s",rechar);//赋值返回
}


//设置开或关函数
void set_onoff(char *body){
    
    int s=get_sid(body);//获取sid
    int n=get_nid(body);//获取nid
    int d=get_data(body);//获取data
    
    if(  s==sid && n==0 )//如果是1路开关
    {
      if( d==1 )//如果数据是开
      {
        state0=1;//设置状态为开
        EEPROM.write(0, state0);//写入rom记录状态
      }
      if( d==0 )//如果数据是关
      {
        state0=0;//设置状态为关
        EEPROM.write(0, state0);//写入rom记录状态       
      }
    }
    
    if(  s==sid && n==1 )//如果是2路开关
    {
      if( d==1 )//如果数据是开
      {
        state1=1;//设置状态为开
        EEPROM.write(1, state1);//写入rom记录状态     
      }
      if( d==0 )//如果数据是关
      {
        state1=0;//设置状态为关
        EEPROM.write(1, state1);//写入rom记录状态        
      }
    }
    
    if(  s==sid && n==2 )//如果是3路开关
    {
      if( d==1 )//如果数据是开
      {
        state2=1;//设置状态为开
        EEPROM.write(2, state2);//写入rom记录状态       
      }
      if( d==0 )//如果数据是关
      {
        state2=0;//设置状态为开
        EEPROM.write(2, state2);//写入rom记录状态        
      }
    }
    
    //更新反馈数据到网关
    if(  s==sid ){//如果是本设备
      char server[10]={0};//声明网关接收名称
      sprintf(server,"serv%d",1);//赋值网关接收名称
      //Serial.println(server);//打印
      
      char updateData[33]={0};//声明更新数组
      char front[10]={0};//声明更新前缀
      //memcpy(front,body,9);
      sprintf(front," {ck%03d%03d",s,n);//赋值前缀
      sprintf(updateData,"%supdate}",front);//生成反馈数组
      Serial.println(updateData);//发送到串口（zigbee会发给网关）
      
//      Mirf_Send(1,server,updateData);//发送给NRF24L01（NRF24L01会发给网关）
      
      char client[10]={0};//声明客户端
      sprintf(client,"clie%d",sid);//赋值客户端名称
//      Mirf_Init(1,client,sid);//设置为接收模式
    }
    
                
}

//获取sid函数
int get_sid(char *buff){
    
  if( strstr(buff,"{ck") && strstr(buff,"}") && strlen(buff)>10)//如果含{ck和}，并长度大于10
    {
      char charSid[4]={0};//声明数组
      memcpy(charSid,buff+3,3);//赋值截取第3位后面的3个字符
      Serial.println(charSid);//打印
      int intSid=atoi(charSid);//转成int
      Serial.println(intSid);//打印
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
      Serial.println(charNid);//打印
      int intNid=atoi(charNid);//转成int
      Serial.println(intNid);//打印
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
      Serial.println(charData);//打印
      int intData=atoi(charData);//转成int
      Serial.println(intData);//打印
      return intData;//返回data      
    }
    else
    {
      return 0;//格式不对返回0
    }
  
}
