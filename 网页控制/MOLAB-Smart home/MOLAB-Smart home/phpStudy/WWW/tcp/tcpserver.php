<?php
//error_reporting(~E_WARNING);

$host = "192.168.1.140";//��������ip
$port = 8080;//���ö˿�

set_time_limit(0);//���ó�ʱʱ��

//����socket
//��һ��������AF_INET������ָ������;
//�ڶ���������SOCK_STREM�����ߺ���������һ��ʲô���͵�Socket(TCP����SOCK_STREM/UDP����SOCK_DGRAM)
//���,������봴��һ��UDP Socket�Ļ�,�����ʹ�����µĴ���:
$sfd = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

//��socket��ָ����ַ�Ͷ˿�
socket_bind($sfd, $host, $port) or die("Could not bind to socket\n");

//��ʼ��������
socket_listen($sfd, 511) or die("Could not set up socket listener\n");

//����socket����
socket_set_option($sfd, SOL_SOCKET, SO_REUSEADDR, 1) or die("Could not set up socket option\n");

//socket����Ϊ�ǿ�ģʽ
socket_set_nonblock($sfd) or die("Could not set up socket nonblock\n");

$rfds = array($sfd);//�����������
$wfds = array();//�����������
$bindfd = array();//�������û�����

do{
	$rs = $rfds; //�����������
    $ws = $wfds;  //�����������
    $es = array();  //�����������
    $ret = socket_select($rs, $ws, $es, 3); //socket��·ѡ��     
 
    //ѭ����д�¼�  
    foreach($rs as $fd){ 
 
        if($fd == $sfd){  
            $cfd = socket_accept($sfd);//������������
            socket_set_nonblock($cfd); //socket����Ϊ�ǿ�ģʽ
            $rfds[] = $cfd; //��ӿͻ��˵�����
			
			$scount=count($rfds)-1;//ͳ�ƿͻ�������
            echo "new client coming, fd=$cfd count=$scount\n"; 
 			//socket_write($cfd, "Welcome to www.znck007.com tcp server! \n");			
			
								
        }else{ 
  		
            $msg = socket_read($fd, 1024);//��ȡ�ͻ�������  
			
			//����С�ڵ���0���ѿͻ����ѶϿ�
            if(strlen($msg) <= 0){               
				//socket_close($fd);//�رտͻ���sockets
				$searchkey = array_search($fd,$rfds);
				array_splice($rfds, $searchkey,1);
				$scount=count($rfds)-1;//ͳ�ƿͻ�������
				echo "delete_conn_socket count=$scount\n";
				
				//����ɾ���Ͽ��󶨵Ŀͻ���
				foreach ($bindfd as $apikey_device => $bind_device)
				{
					foreach ($bind_device as $bind_apikey => $bind_socket)
					{
						//echo $bind_apikey.'=>'.$bind_socket;
						if($bind_socket==$fd){												
							$searchkey = array_search($bind_device,$bindfd);
							array_splice($bindfd, $searchkey,1);
							//unset($bind_device[$bind_apikey]);
							$scount=count($bindfd);//ͳ�ư󶨿ͻ�������
							echo "delete_bind_socket count=$scount\n"; 
							
						}
					}			
				}
				
            }else{  
                //���յ��ͻ��˵�����  
				$restr=$msg;
                echo "message fd=$fd \ndata=$msg\n"; 
				
 				
				//д����־���޸������·����Ĭ�ϲ���
				if(false)
				{
					$fp=fopen('/var/www/html/znck/wap/tcp/log/'.date("Y-m-d").'.txt','a');
					fputs($fp,date("Y-m-d H:i:s").$msg."\r\n");
					fclose($fp);
				}
	
				if( strpos($msg, "apikey") ){
					if( strpos($msg, "&data") ){
						
						//����ַ���
						if( strpos($msg,"&") ){
							$arr = explode("&",$msg);			
							$mode=$arr[0];
							$apikey=$arr[1];
							$data=$arr[2];							
							$mode=str_replace("mode=","",$mode);
							$apikey=str_replace("apikey=","",$apikey);
							$data=str_replace("data=","",$data);
							
							//���ذ�
							if(strpos($mode, "bind")!==false ){						
								$bindfd[]=array($apikey => $fd);//��ӵ��󶨿ͻ����б�	 
								$scount=count($bindfd);//ͳ�ư󶨿ͻ�������
								echo "bind client, fd=$fd count=$scount\n";	
								socket_write($fd, "welcome to www.znck007.com, bind success!\n");
								$restr="bind ok";						
							}
							
							//����ת�����󶨵�����
							if(strpos($mode, "exe")!==false ){
								
								send_data($apikey,$bindfd,$data);
								$restr="exe ok";
							}
							
							//�����ϴ�������						
							if(strpos($mode, "up")!==false ){
							    up_data($apikey,$data,$bindfd);	
								$restr="up ok";
							}
							//��������������						
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
 
     
 
    //����Ⱥ��  
    //foreach($ws as $fd){  
        //socket_write($fd, "data=msg\n");
    //} 
      
 
}while(true);



//����������Ϣ���󶨵Ŀͻ���
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
	
	//����apikey�ҵ��û����
	$uid=0;
	$username="";
	//��ȡ���ݿ�
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
	
	//������������wordlist�������ж��ն��Ƿ���Ӧ��
	if( $data=='update') 
	{
		$sql="UPDATE api_worklist SET status=1 WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' and status=0";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		//����״̬Ϊ����
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//,data='".$data."'
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		mysql_close();
		return;
	}
	
	//ͬ���豸״̬
	if(strpos($data, "sta")!==false )
	{		
		$data=str_replace("sta","",$data);
		
		$type=2;//1����2�ϴ�
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$data;
		$note="����ͬ��";
		$status=1;
		$time="";
		$ip=$_SERVER["REMOTE_ADDR"];//��¼IP
		
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//����
		$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		//����״
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET status=1,lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}
		
		mysql_close();
		return;
	}
	
	
	
	//��������״̬
	if( $sid==1 && $nid==0) 
	{
		//�����������ʱ��
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."' WHERE uid='".$uid."' and sid='1' and nid='1'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}	
				
		
		//��ʱ���� 
		$nowt=time();//-60ǰһ�� 60*60��һ��Сʱ 
		$btime=date("Y-m-d H:i:s",$nowt);		
		$nowt=time()-120;//��һ�� 60*60��һ��Сʱ
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
				
				$type=3;//1����2�ϴ�3��ʱ
				$uid=$uid;
				$sid=$timing_sid;
				$nid=$timing_nid;
				$data=$timing_data;
				$note="��ʱTCP";
				$status=1;
				$time="";
				$ip=$_SERVER["REMOTE_ADDR"];//��¼IP
				
				$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
				$time=date("Y-m-d H:i:s",$nowt);
				
				//����
				$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
				VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
				if (!mysql_query($sql))
				{
					echo 'Error: ' . mysql_error();
				}
				
				//�����豸
				$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
				$time=date("Y-m-d H:i:s",$nowt);		
				$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//lasttime='".$time."',
				if (!mysql_query($sql))
				{
					echo 'Error: ' . mysql_error();
				}
				
				
				//����tcpЭ�鲿�� 
				//------------------------------------------------------	
				$tcpdata="{ck".(sprintf("%03d",$sid).sprintf("%03d",$nid).$data)."}";			
				send_data($apikey,$bindfd,$tcpdata);
				//------------------------------------------------------
				
			}
		}
		
		mysql_close();
		return;
	}
	
	
	//�������ݵ���ʪ�ȱ�����������Զ�����ֹ���Ч����
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
		$ip=$_SERVER["REMOTE_ADDR"];//��¼IP
		
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//����¶� ʪ��
		if( strpos($data,".") ){
			$arr = explode(".",$data);			
			$temperature=$arr[1];
			$humidity=$arr[0];			
		}
	
		//����Ҿ���ʪ��		
		$sql="INSERT INTO api_temperature(type,uid,sid,nid,data,temperature,humidity,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$temperature."', '".$humidity."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		//�����������ʱ��
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);		
		$sql="UPDATE api_device SET lasttime='".$time."',data='".$data."' WHERE uid='".$uid."' and sid='1' and nid='2'";
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();
		}
		
		mysql_close();
		return;
		
	}
		
	
	
	//�Զ�����ִ��жˡ����ƶˣ�������������Ӵ��봦������ʪ�ȣ�
	
	
	
	//�������ݵ�datalist�������ݼ�¼��
	if( $sid>0 && $nid>0) 
	{
		$type=2;//1����2�ϴ�
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$data;
		$note="�����ϴ�";
		$status=1;
		$time="";
		$ip=$_SERVER["REMOTE_ADDR"];//��¼IP
		
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//����
		$sql="INSERT INTO api_datalist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			echo 'Error: ' . mysql_error();			
		}
		
		
		//�����������ʱ��
		$nowt=time();//��ǰʱ�� 60*60��һ��Сʱ 
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