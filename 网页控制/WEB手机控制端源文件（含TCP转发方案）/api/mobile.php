<?php 
header("Content-Type: text/html; charset=utf-8");//设置utf-8编码
require_once('ckconn.php');//连接数据库

$apikey=$_POST['apikey'];//获取apikey
$mode=$_POST['mode'];//获取模式

//获取数据库
$sql="SELECT uid,username FROM api_member WHERE apikey='".$apikey."'";//查找用户数据库SQL
$result = mysql_query($sql);//执行SQL语句
if (!$result)//异常
{
  die('Error: ' . mysql_error());//打印错误
  mysql_close();//关闭数据库
  exit;//关闭
}
$num=mysql_num_rows($result);//统计记录数
if($row = mysql_fetch_array($result))//获取记录集
{
	$uid=$row['uid'];//读取字段
	$username=$row['username'];//读取字段
	//echo $uid;//打印
	//echo $username;//打印
}else{
	echo "no user";//没有记录
	exit;//关闭
}


//语音设置数据 
if( $mode=="yysetdata") 
{
	
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	$data=urldecode($_POST['data']);//获取POST数据

	$sid=str_replace(hexToStr("EFBBBF"),"",$sid);	
	$nid=trim($nid);
	
	$type=2;//1网关2上传
	$uid=$uid;//设置uid
	$sid=$sid;//设置sid
	$nid=$nid;//设置nid
	$data=$data;//设置data
	$note="语音设置";//记录说明
	$status=0;//未处理
	$time="";//时间
	$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
	
	$nowt=time();//当前时间
	$time=date("Y-m-d H:i:s",$nowt);//调置时间
	
	//插入数据库
	$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
	VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
	if (!mysql_query($sql))//执行SQL语句
	{
		die('Error: ' . mysql_error());//打印错误
		mysql_close();//关闭数据库
		exit;//关闭
	}
	
	//更新设置最后时间
	$nowt=time();//当前时间 
	$time=date("Y-m-d H:i:s",$nowt);//时间格式化	
	$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//更新SQL语句
	if (!mysql_query($sql))//执行SQL语句
	{
		die('Error: ' . mysql_error());//打印错误
		mysql_close();//关闭数据库
		exit;//关闭
	}
	
	//获取apikey，处理tcp协议部份 
	//------------------------------------------------------
	$sql="SELECT username,apikey FROM api_member WHERE uid='".$uid."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());	
	}
	$num=mysql_num_rows($result);
	if($row = mysql_fetch_array($result))
	{		
		$apikey=$row['apikey'];
		$username=$row['username'];
	}
	
	$tcpdata="mode=exe&";
	$tcpdata=$tcpdata."apikey=".$apikey."&";
	$tcpdata=$tcpdata."data={ck".(sprintf("%03d",$sid).sprintf("%03d",$nid).$data)."}";
	$url="http://".$_SERVER['HTTP_HOST']."/tcp/tcpclient.php?data=".urlencode($tcpdata);
	$tcptext = file_get_contents($url);
	//echo $tcptext;
	//------------------------------------------------------
	
		
	echo "语音控制成功！\nsid=".$sid."/nid=".$nid."/data=".$data."";//返回数据
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取网关信息
if( $mode=="GetWanguan") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
	}
	else//否则添加一条记录
	{
		$type=1;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="网关设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间		
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 
	$servertime=date("Y-m-d H:i:s",$nowt);//格式化时间
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|".$apikey."|";
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取温湿度信息
if( $mode=="GetWengShidu") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
		
		//拆分湿度和温度
		if( strpos($data,".") ){//如何包含.则拆分
			$arr = explode(".",$data);//拆分成数组			
			$humidity=$arr[0];//生成湿度
			$temperature=$arr[1].'.'.$arr[2];//生成温度			
		}
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="温度设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|".$temperature."|".$humidity."|";
	mysql_close();//关闭数据库
	exit;//关闭
}



//获取温湿度信息
if( $mode=="GetPM25") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
				
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="空气设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取PM2.5信息
if( $mode=="GetPM25") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
				
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="空气设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}



//获取人体红外检测信息
if( $mode=="GetRenti") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
				
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="人体设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取烟雾火警信息
if( $mode=="GetYanwu") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
				
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="烟雾设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取水滴信息
if( $mode=="GetShuidi") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
		
		
				
		
	}
	else//否则添加一条记录
	{
		$type=2;//1网关2上传
		$uid=$uid;//设置uid
		$sid=(int)$sid;//设置sid
		$nid=(int)$nid;//设置nid
		$data=$data;//设置data
		$note="水滴设备";//设置note
		$status=1;//设置status
		$regdate="";//设置注册时间
		$lasttime="";//设置最后时间	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);//格式化时间
		$lasttime="2014-01-01 00:00:00";//最后时间
					
		//插入数据库
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))//执行SQL
		{
			die('Error: ' . mysql_error());//打印错误
			mysql_close();//关闭数据库
			exit;//关闭
		}
	} 
	
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}



//获取终端列表信息
if( $mode=="GetZDList") 
{
	$type=$_POST['type'];//获取POST数据	
	
	//查找插座列表
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and type='".$type."' ORDER BY sid ASC";
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);
	while($row = mysql_fetch_array($result))
	{
		$sid=$row['sid'];//读取字段并格式化
		$nid=$row['nid'];//读取字段并格式化		
		$data=$row['data'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段
		$note=$row['note'];//读取字段
		
		$data=str_replace(",","_",$data);//替换,成_，防止转义问题
		
		//返回数据
		echo $sid.",". $nid.",".$data.",".$note."|";
	}	
	
	mysql_close();//关闭数据库
	exit;//关闭
}


//获取终端信息
if( $mode=="GetZDData") 
{
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
		
	//去掉特殊字符\0
	$tsid='';
	for ($x=0; $x<strlen($sid); $x++) {
	  $si=substr($sid,$x,1);//获取单个字符
	  //echo $si.'-';//打印字符
	  if(hexdec($si)!=0) $tsid=$tsid.$si;//组成新字符
	}
	$sid=$tsid;//重新赋值sid
		
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
	
	$result = mysql_query($sql);//执行SQL语句
	if (!$result)
	{
	  die('Error: ' . mysql_error());//打印错误
	  mysql_close();//关闭数据库
	  exit;//关闭
	}
	$num=mysql_num_rows($result);//统计记录数
	if($row = mysql_fetch_array($result))//获取记录集
	{
		$sid=sprintf("%03d",$row['sid']);//读取字段并格式化
		$nid=sprintf("%03d",$row['nid']);//读取字段并格式化
		$data=$row['data'];//读取字段
		$note=$row['note'];//读取字段
		$status=$row['status'];//读取字段
		$regdate=$row['regdate'];//读取字段
		$lasttime=$row['lasttime'];//读取字段		
				
		
	}else
	{
		echo "对不起，该设备不存在！";
		mysql_close();//关闭数据库
		exit;//关闭
	}
	$nowt=time();//当前时间 60*60是一个小时 
	$servertime=date("Y-m-d H:i:s",$nowt);
	
	//返回数据
	echo "ok|".$uid."|".$sid."|".$nid."|".$data."|".$note."|".$status."|".$regdate."|".$lasttime."|".$servertime."|";
	mysql_close();//关闭数据库
	exit;//关闭
}

//设置终端信息 
if( $mode=="SetZDData") 
{
	
	$sid=$_POST['sid'];//获取POST数据
	$nid=$_POST['nid'];//获取POST数据
	$data=urldecode($_POST['data']);//获取POST数据

	$sid=str_replace(hexToStr("EFBBBF"),"",$sid);	
	$nid=trim($nid);
	
	$type=2;//1网关2上传
	$uid=$uid;//设置uid
	$sid=$sid;//设置sid
	$nid=$nid;//设置nid
	$data=$data;//设置data
	$note="客户端设置";//记录说明
	$status=0;//未处理
	$time="";//时间
	$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
	
	$nowt=time();//当前时间
	$time=date("Y-m-d H:i:s",$nowt);//调置时间
	
	//插入数据库
	$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
	VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
	if (!mysql_query($sql))//执行SQL语句
	{
		die('Error: ' . mysql_error());//打印错误
		mysql_close();//关闭数据库
		exit;//关闭
	}
	
	//更新设置最后时间
	$nowt=time();//当前时间 
	$time=date("Y-m-d H:i:s",$nowt);//时间格式化	
	$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//更新SQL语句
	if (!mysql_query($sql))//执行SQL语句
	{
		die('Error: ' . mysql_error());//打印错误
		mysql_close();//关闭数据库
		exit;//关闭
	}
	
	//获取apikey，处理tcp协议部份 
	//------------------------------------------------------
	$sql="SELECT username,apikey FROM api_member WHERE uid='".$uid."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());	
	}
	$num=mysql_num_rows($result);
	if($row = mysql_fetch_array($result))
	{		
		$apikey=$row['apikey'];
		$username=$row['username'];
	}
	
	$tcpdata="mode=exe&";
	$tcpdata=$tcpdata."apikey=".$apikey."&";
	$tcpdata=$tcpdata."data={ck".(sprintf("%03d",$sid).sprintf("%03d",$nid).$data)."}";
	$url="http://".$_SERVER['HTTP_HOST']."/tcp/tcpclient.php?data=".urlencode($tcpdata); 	
	$tcptext = file_get_contents($url);
	//echo $tcptext;
	//------------------------------------------------------
	
	echo "终端控制成功！\nsid=".$sid."/nid=".$nid."/data=".$data."";//返回数据
	mysql_close();//关闭数据库
	exit;//关闭
}


mysql_close();//关闭数据库
exit;//关闭
?>


<?php 

function strToHex($string)//字符串转十六进制
{ 
	$hex="";
	for($i=0;$i<strlen($string);$i++)
	$hex.=dechex(ord($string[$i]));
	$hex=strtoupper($hex);
	return $hex;
}

function hexToStr($hex)//十六进制转字符串
{   
	$string=""; 
	for($i=0;$i<strlen($hex)-1;$i+=2)
	$string.=chr(hexdec($hex[$i].$hex[$i+1]));
	return  $string;
}


function get_onlineip() {
    $onlineip = '';
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    return $onlineip;
}

function getTimeDifference($time_two) {//$time_one,
    $stamp_one=time();//strtotime($time_one)
    $stamp_two=strtotime($time_two);
	$diff_time=($stamp_one-$stamp_two);
    $day=intval($diff_time/86400);
    $hour=intval(($diff_time-$day*86400)/3600); $minutes=intval(($diff_time-$day*86400-$hour*3600)/60);
    $seconds=$diff_time-$day*86400-$hour*3600-$minutes*60;
    $mess=$seconds."秒前";
    if ($minutes>0){$mess=$minutes."分钟前"; }
    if ($hour>0){$mess=$hour."小时前"; }
    if ($day>0){$mess=$day."天前"; }
	if ($day>5){$mess=$time_two; }
	if ($mess<0){$mess=""; }
    return $mess;
}
?>