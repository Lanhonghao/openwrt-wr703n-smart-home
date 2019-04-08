<?php 
//header("Content-Type: text/html; charset=utf-8");
require 'conn.php';



//获取POST值
$getinput=$_POST['data'];
//echo $getinput;
if( $getinput=="" ){
	$getinput=file_get_contents("php://input");
	//echo $getinput;
}


//判断数据格式是否正确
if(strpos($getinput, "{ck")!==false )
{
	$uid=0;
	$sid=substr($getinput,3,3);
	$nid=substr($getinput,6,3);
	$data=substr($getinput,9);
	$data=str_replace("}","",$data);	
	//echo $sid;
	//echo $nid;
	//echo $data;
	
	//$sid为1、网关，2、其它（可以自己定义等）
	
	//获取数据wordlist
	if( $sid==1 && $nid==0) 
	{
		$sql="SELECT sid,nid,data,num FROM api_worklist WHERE status=0 ORDER BY id DESC limit 0,1";
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
			
			//输出数据（这里就可以输出各种命令开关等）
			echo "{ck";
			echo $sid;
			echo $nid;
			echo $data; 
			echo "}";	
			//结束输出数据
			
			if( $row['num']>5 ) {
				//处理成超次数
				$sql="UPDATE api_worklist SET status=3 WHERE sid='".$sid."' and nid='".$nid."'";
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}				
		
			}else{
				//更新次数
				$sql="UPDATE api_worklist SET num=num+1 WHERE sid='".$sid."' and nid='".$nid."'";
				if (!mysql_query($sql))
				{
					die('Error: ' . mysql_error());
				}	
			}
				
		}		
		
		//更新超过10分钟
		$nowt=time()-60*10;//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_worklist SET status=4 WHERE time<'".$time."' and status=0 ";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}				
		
	}
	else		
	{//否则插入数据到worklist表	
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
		$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
		
		echo "ok,insert data.";
	}
	
	
	//更新设置数据wordlist
	if( $data=='update') 
	{
		$sql="UPDATE api_worklist SET status=1 WHERE sid='".$sid."' and nid='".$nid."' and status=0";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}		
		
		echo "ok,update data.";
	}
	
	
}else{	
	echo "sorry,data is not correct.";
}



mysql_close();


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