<?php
//error_reporting(~E_WARNING);

$host = "192.168.1.140";//设置主机ip
$port = 8080;//设置端口

set_time_limit(0);//设置超时时间

//创建socket
//第一个参数”AF_INET”用来指定域名;
//第二个参数”SOCK_STREM”告诉函数将创建一个什么类型的Socket(TCP类型SOCK_STREM/UDP类型SOCK_DGRAM)
//因此,如果你想创建一个UDP Socket的话,你可以使用如下的代码:
$sfd = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

//绑定socket到指定地址和端口
socket_bind($sfd, $host, $port) or die("Could not bind to socket\n");

//开始监听连接
socket_listen($sfd, 511) or die("Could not set up socket listener\n");

//设置socket参数
socket_set_option($sfd, SOL_SOCKET, SO_REUSEADDR, 1) or die("Could not set up socket option\n");

//socket设置为非块模式
socket_set_nonblock($sfd) or die("Could not set up socket nonblock\n");

$rfds = array($sfd);//声名数组变量
$wfds = array();//声名数组变量
$bindfd = array();//声名绑定用户数组

do{
	$rs = $rfds; //声名数组变量
    $ws = $wfds;  //声名数组变量
    $es = array();  //声名数组变量
    $ret = socket_select($rs, $ws, $es, 3); //socket多路选择     
 
    //循环读写事件  
    foreach($rs as $fd){ 
 
        if($fd == $sfd){  
            $cfd = socket_accept($sfd);//接受请求链接
            socket_set_nonblock($cfd); //socket设置为非块模式
            $rfds[] = $cfd; //添加客户端到数组
			
			$scount=count($rfds)-1;//统计客户端总数
            echo "new client coming, fd=$cfd count=$scount\n"; 
 			//socket_write($cfd, "Welcome to www.znck007.com tcp server! \n");			
			
								
        }else{ 
  		
            $msg = socket_read($fd, 1024);//读取客户端数据  
			
			//数据小于等于0则已客户端已断开
            if(strlen($msg) <= 0){               
				//socket_close($fd);//关闭客户端sockets
				$searchkey = array_search($fd,$rfds);
				array_splice($rfds, $searchkey,1);
				$scount=count($rfds)-1;//统计客户端总数
				echo "delete_conn_socket count=$scount\n";
				
				//遍历删除断开绑定的客户端
				foreach ($bindfd as $apikey_device => $bind_device)
				{
					foreach ($bind_device as $bind_apikey => $bind_socket)
					{
						//echo $bind_apikey.'=>'.$bind_socket;
						if($bind_socket==$fd){												
							$searchkey = array_search($bind_device,$bindfd);
							array_splice($bindfd, $searchkey,1);
							//unset($bind_device[$bind_apikey]);
							$scount=count($bindfd);//统计绑定客户端总数
							echo "delete_bind_socket count=$scount\n"; 
							
						}
					}			
				}
				
            }else{  
                //接收到客户端的数据  
				$restr=$msg;
                echo "message fd=$fd \ndata=$msg\n"; 
				
 				
				//写入日志，修改下面的路径，默认不打开
				if(false)
				{
					$fp=fopen('/var/www/html/znck/wap/tcp/log/'.date("Y-m-d").'.txt','a');
					fputs($fp,date("Y-m-d H:i:s").$msg."\r\n");
					fclose($fp);
				}
	
				if( strpos($msg, "apikey") ){
					if( strpos($msg, "&data") ){
						
						//拆分字符串
						if( strpos($msg,"&") ){
							$arr = explode("&",$msg);			
							$mode=$arr[0];
							$apikey=$arr[1];
							$data=$arr[2];							
							$mode=str_replace("mode=","",$mode);
							$apikey=str_replace("apikey=","",$apikey);
							$data=str_replace("data=","",$data);
							
							//网关绑定
							if(strpos($mode, "bind")!==false ){						
								$bindfd[]=array($apikey => $fd);//添加到绑定客户端列表	 
								$scount=count($bindfd);//统计绑定客户端总数
								echo "bind client, fd=$fd count=$scount\n";	
								socket_write($fd, "welcome to www.znck007.com, bind success!\n");
								$restr="bind ok";						
							}
							
							//命令转发到绑定的网关
							if(strpos($mode, "exe")!==false ){
								
								send_data($apikey,$bindfd,$data);
								$restr="exe ok";
							}
							
							//网关上传的数据						
							if(strpos($mode, "up")!==false ){
							    up_data($apikey,$data,$bindfd);	
								$restr="up ok";
							}
							//网关心跳的数据						
							if(strpos($mode, "live")!==false ){								
							    up_data($apikey,$data,$bindfd);		
								$restr="live ok";
							}
							
							socket_write($fd, "znck007 $restr\n");
						}
						
					}else{
						echo "return err null data, fd=$fd \nmsg=$msg\n";
					}
				}else{
					echo "return err null apikey, fd=$fd \nmsg=$msg\n";
				}
            } 
 			
        } 
 
    } 
 
     
 
    //遍历群发  
    //foreach($ws as $fd){  
        //socket_write($fd, "data=msg\n");
    //} 
      
 
}while(true);



//遍历发送信息到绑定的客户端
function send_data($apikey,$bindfd,$data) {
	foreach ($bindfd as $apikey_device => $bind_device)
	{
		foreach ($bind_device as $bind_apikey => $bind_socket)
		{
			echo $bind_apikey.'=>'.$bind_socket;
			echo "send_bind_socket\n"; 
			if(strpos($bind_apikey, $apikey)!==false ){
				socket_write($bind_socket, "$data\n");
				
			}
		}			
	}
}



function up_data($apikey,$getinput,$bindfd) {
	require 'ckconn.php';
	echo "\n__________________up_data_start\n";
	if(strpos($getinput, "{ck")!==false )
	{
		$sid=substr($getinput,3,3);
		$nid=substr($getinput,6,3);
		$data=substr($getinput,9);
		$data=str_replace("}","",$data);
		echo "sid=$sid";
		echo "nid=$nid";
		echo "data=$data";
	}else{
		echo 'Error: this is not {ck} format $getinput';
		mysql_close();
		return;
	}
	
	//根据apikey找到用户编号
	$uid=0;
	$username="";
	//获取数据库
	$sql="SELECT uid,username FROM api_member WHERE apikey='".$apikey."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  echo 'Error: ' . mysql_error();
	  mysql_close();
	  return;
	}
	$num=mysql_num_rows($result);
	if($row = mysql_fetch_array($result))
	{
		$uid=$row['uid'];
		$username=$row['username'];
		echo "uid=$uid";
		//echo $username;
	}else{
		echo 'not user';
		mysql_close();
		return;
	}
	
	echo "\n__________________up_data_end\n";
	
	//更新设置数据wordlist（用于判断终端是否响应）
	if( $data=='update') 
	{
		$sql="UPDATE api_worklist SET status=1 WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' and status=0";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		//更新状态为正常
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//,data='".$data."'
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		mysql_close();
		return;
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
		$ip=$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//插入
		$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		//更新状
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}
		
		mysql_close();
		return;
	}
	
	
	
	//更新网关状态
	if( $sid==1 && $nid==0) 
	{
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."' WHERE uid='".$uid."' and sid='1' and nid='1'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}	
				
		
		//定时处理 
		$nowt=time();//-60前一分 60*60是一个小时 
		$btime=date("Y-m-d H:i:s",$nowt);		
		$nowt=time()-120;//后一分 60*60是一个小时
		$etime=date("Y-m-d H:i:s",$nowt);	
		$hourm=date("H:i",$nowt);	//echo $hourm;
		$sql="SELECT sid,nid,type,data,time FROM api_timing WHERE uid='".$uid."' and ((time<'".$btime."' and time>'".$etime."') or (type=2 and DATE_FORMAT(time,'%H:%i') ='".$hourm."') )"; //echo $sql;		
		//sid='".$sid."' and nid='".$nid."' and 
		$result = mysql_query($sql);
		if (!$result)
		{
		 	echo 'Error: ' . mysql_error();
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
			
			$sql="SELECT id FROM api_worklist WHERE type=3 and uid='".$uid."' and sid='".$timing_sid."' and nid='".$timing_nid."' and data='".$timing_data."' and time<'".$btime."' and time>'".$etime."' ORDER BY id DESC"; 
			$result = mysql_query($sql);
			if (!$result)
			{
			  echo 'Error: ' . mysql_error();
			}
			$num=mysql_num_rows($result);
			
			if( $num<=0 ){
				
				$type=3;//1网关2上传3定时
				$uid=$uid;
				$sid=$timing_sid;
				$nid=$timing_nid;
				$data=$timing_data;
				$note="定时TCP";
				$status=1;
				$time="";
				$ip=$_SERVER["REMOTE_ADDR"];//记录IP
				
				$nowt=time();//当前时间 60*60是一个小时 
				$time=date("Y-m-d H:i:s",$nowt);
				
				//插入
				$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
				VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
				if (!mysql_query($sql))
				{
					echo 'Error: ' . mysql_error();
				}
				
				//更新设备
				$nowt=time();//当前时间 60*60是一个小时 
				$time=date("Y-m-d H:i:s",$nowt);		
				$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//lasttime='".$time."',
				if (!mysql_query($sql))
				{
					echo 'Error: ' . mysql_error();
				}
				
				
				//处理tcp协议部份 
				//------------------------------------------------------	
				$tcpdata="{ck".(sprintf("%03d",$sid).sprintf("%03d",$nid).$data)."}";			
				send_data($apikey,$bindfd,$tcpdata);
				//------------------------------------------------------
				
			}
		}
		
		mysql_close();
		return;
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
		$ip=$_SERVER["REMOTE_ADDR"];//记录IP
		
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
			echo 'Error: ' . mysql_error();			
		}
		
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='1' and nid='2'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}
		
		mysql_close();
		return;
		
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
		$ip=$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//插入
		$sql="INSERT INTO api_datalist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		
		//更新设置最后时间
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}
		
		
		mysql_close();
		return;
	}
}

?>