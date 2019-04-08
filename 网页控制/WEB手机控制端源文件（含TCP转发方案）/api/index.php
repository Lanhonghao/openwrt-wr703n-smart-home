<?php 
header("Content-Type: text/html; charset=utf-8");
require 'ckconn.php';


//获取到apikey
$apikey="";
//此函数仅在PHP作为Apache模块安装时才可使用。
$headers = apache_request_headers();
foreach ($headers as $header => $value) {
	if(strpos($header, "apikey")!==false)
	{
		$apikey= "$value"; //"$header: $value\n";
		//echo $apikey;
	}
}
if($apikey=="")
{
	$apikey= $_POST['apikey'];
	if($apikey=="")
	{
		$apikey= $_GET['apikey'];
	}
}
//echo $apikey;


//根据apikey找到用户编号
$uid=0;
$username="";
//获取数据库
$sql="SELECT uid,username FROM api_member WHERE apikey='".$apikey."'";
$result = mysql_query($sql);
if (!$result)
{
  die('Error: ' . mysql_error());
  //mysql_close($con);
  //exit;
}
$num=mysql_num_rows($result);
if($row = mysql_fetch_array($result))
{
	$uid=$row['uid'];
	$username=$row['username'];
	//echo $uid;
	//echo $username;
}else{
	echo 'not user';
}


//获取POST值
$getinput= $_POST['data'];
if($getinput=="")
{
	$getinput= $_GET['data'];
	if($getinput=="")
	{
		$getinput=file_get_contents("php://input"); 
	}
}
//echo $getinput;

//写入日志，修改下面的路径
{
	$fp=fopen('/var/www/html/znck/wap/api/log/'.date("Y-m-d").'_'.$uid.'.txt','a');
    fputs($fp,date("Y-m-d H:i:s").$getinput."\r\n");
    fclose($fp);
}

//数据格式是否正确{ck00x00xxxxxxx}
if(strpos($getinput, "{ck")!==false )
{
	$sid=substr($getinput,3,3);
	$nid=substr($getinput,6,3);
	$data=substr($getinput,9);
	$data=str_replace("}","",$data);
	echo $sid;
	echo $nid;
	echo $data;
	
		
	//获取设置数据wordlist
	if( $sid==1 && $nid==0) 
	{
		$sql="SELECT sid,nid,data,num FROM api_worklist WHERE uid='".$uid."' and status=0 ORDER BY id DESC";
		$result = mysql_query($sql);
		if (!$result)
		{
		  die('Error: ' . mysql_error());
		  //mysql_close($con);
		  //exit;
		}
		$num=mysql_num_rows($result);
		if($row = mysql_fetch_array($result))
		{
			$sid=sprintf("%03d",$row['sid']);
			$nid=sprintf("%03d",$row['nid']);
			$data=$row['data'];
			echo "{ck";
			echo $sid;
			echo $nid;
			echo $data; 
			echo "}";	
			
			if( $row['num']>5 ) {
				//处理成超次数
				$sql="UPDATE api_worklist SET status=3 WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."'";
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}
				
				//更新为超次数
				$nowt=time();//当前时间 60*60是一个小时
				$time=date("Y-m-d H:i:s",$nowt);		
				$sql="UPDATE api_device SET status=2 WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}
		
			}else{
				//更新次数
				$sql="UPDATE api_worklist SET num=num+1 WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."'";
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}	
			}
				
		}
			
		
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."' WHERE uid='".$uid."' and sid='1' and nid='1'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}		
		
		//更新超过10分钟
		$nowt=time()-60*10;//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_worklist SET status=4 WHERE time<'".$time."' and status=0 ";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		
		//定时处理
		$nowt=time();//-60前一分 60*60是一个小时
		$btime=date("Y-m-d H:i:s",$nowt);		
		$nowt=time()-60;//后一分 60*60是一个小时
		$etime=date("Y-m-d H:i:s",$nowt);	
		$hourm=date("H:i",$nowt);	//echo $hourm;
		$sql="SELECT sid,nid,type,data,time FROM api_timing WHERE uid='".$uid."' and ((time<'".$btime."' and time>'".$etime."') or (type=2 and DATE_FORMAT(time,'%H:%i') ='".$hourm."') )"; //echo $sql;		
		//sid='".$sid."' and nid='".$nid."' and 
		$result = mysql_query($sql);
		if (!$result)
		{
		  die('Error: ' . mysql_error());
		  //mysql_close($con);
		  //exit;
		}
		$num=mysql_num_rows($result);
		if($row = mysql_fetch_array($result))
		{			
			$timing_type=$row['type'];
			$timing_data=$row['data'];
			$timing_time=$row['time'];
			$timing_sid=$row['sid'];
			$timing_nid=$row['nid'];
			$timing_sid=sprintf("%03d",$row['sid']);
			$timing_nid=sprintf("%03d",$row['nid']);
			
			//$sql="SELECT id FROM api_device WHERE uid='".$uid."' and sid='".(int)$timing_sid."' and nid='".(int)$timing_nid."' and data='".$timing_data."' ORDER BY sid DESC";
			$sql="SELECT id FROM api_worklist WHERE type=3 and uid='".$uid."' and sid='".$timing_sid."' and nid='".$timing_nid."' and data='".$timing_data."' and time<'".$btime."' and time>'".$etime."' ORDER BY id DESC"; 
			$result = mysql_query($sql);
			if (!$result)
			{
			  die('Error: ' . mysql_error());
			  //mysql_close($con);
			  //exit;
			}
			$num=mysql_num_rows($result);
			
			if( $num<=0 ){
			
				$type=3;//1网关2上传3定时
				$uid=$uid;
				$sid=$timing_sid;
				$nid=$timing_nid;
				$data=$timing_data;
				$note="定时设置";
				$status=0;
				$time="";
				$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
				
				$nowt=time();//当前时间 60*60是一个小时 
				$time=date("Y-m-d H:i:s",$nowt);
				
				//插入
				$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
				VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
					//mysql_close($con);
					//exit;
				}
				
				//更新设备
				$nowt=time();//当前时间 60*60是一个小时 
				$time=date("Y-m-d H:i:s",$nowt);		
				$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//lasttime='".$time."',
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}
			}
		}
		
		mysql_close();
		exit;
		
	}
	
		
	
	//更新设置数据wordlist（用于判断终端是否响应）
	if( $data=='update') 
	{
		$sql="UPDATE api_worklist SET status=1 WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' and status=0";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		//更新状态为正常
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//,data='".$data."'
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		mysql_close();
		exit;
	}
	
	//同步设备状态
	if(strpos($data, "sta")!==false )
	{		
		$data=str_replace("sta","",$data);
		
		$type=2;//1网关2上传
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$data;
		$note="数据同步";
		$status=1;
		$time="";
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//插入
		$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
		
		//更新状
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		mysql_close();
		exit;
	}
	
		
	//插入数据到温湿度表（仿这里可以自定义各种功能效果）
	if( $sid==1 && $nid==2) 
	{
		$type=1;
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$data;
		$temperature=0;
		$humidity=0;
		$status=1;
		$time="";
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//拆分温度 湿度
		if( strpos($data,".") ){
			$arr = explode(".",$data);			
			$temperature=$arr[1];
			$humidity=$arr[0];			
		}
	
		//插入家居温湿度		
		$sql="INSERT INTO api_temperature(type,uid,sid,nid,data,temperature,humidity,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$temperature."', '".$humidity."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
		
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='1' and nid='2'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		mysql_close();
		exit;
		
	}
		
	
	
	//自定义各种传感端、控制端（可以在这里添加代码处理，仿温湿度）
	
	
	
	
	
	
	
	//插入数据到datalist表（总数据记录表）
	if( $sid>0 && $nid>0) 
	{
		$type=2;//1网关2上传
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$data;
		$note="数据上传";
		$status=1;
		$time="";
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//插入
		$sql="INSERT INTO api_datalist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
		
		
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		
		mysql_close();
		exit;
	}
}



//if($getinput!="")
//echo "{ck002001000002}";
//echo $getinput;
//echo "\n";



/*
$input = array(&$_GET,&$_POST);
foreach($input as $k){
 foreach($k as $name=>$value){
 	echo "$name: $value\n"; 
 }
} 
*/


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

?>