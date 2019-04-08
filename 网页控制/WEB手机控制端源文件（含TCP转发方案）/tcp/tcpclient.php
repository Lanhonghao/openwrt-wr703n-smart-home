<?php
error_reporting(E_ALL & ~E_NOTICE);

set_time_limit(10);//设置超时时间

$server="192.168.253.1";
$port=8080;



$getinput= $_POST['data'];
if($getinput=="")
{
	$getinput= $_GET['data'];
	if($getinput=="")
	{
		$getinput=file_get_contents("php://input"); 
	}
}
if($getinput==""){
	die("Error: Send message is null\n");
	exit;
}


$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Error: Could not create socket\n");
$con=socket_connect($socket,$server,$port) or die("Error: Could not socket_connect server\n");
if(!$con){	
	socket_close($socket);
	exit;
}



//while($con)
{        

        $words=urldecode($getinput);//urlencode($getinput);//处理中文问题
        socket_write($socket,$words);
		
		$msg=socket_read($socket,1024);
        echo $msg;
        //if($msg=="bye\r\n"){break;}
}

socket_shutdown($socket);
socket_close($socket);
exit;
?>