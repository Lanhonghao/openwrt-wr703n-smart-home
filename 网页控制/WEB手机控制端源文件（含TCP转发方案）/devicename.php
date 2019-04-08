
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－设备名称</title>
<meta name="keywords" content="智能创客－设备名称" />
<meta name="description" content="智能创客－设备名称" />
<meta name="copyright" content="www.znck007.com" />
<meta name="viewport" content="user-scalable=0" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />

<link href="css/global.css" rel="stylesheet" type="text/css" />

</head>

<body>

<!--顶部-->
<table class="top" border="0" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="70px" />
        <col />
        <col width="70px" />
    </colgroup>
    <tr>
        <td><button onClick="javascript:history.back()">返回</button></td>
        <td>智能创客－设备名称</td>
        <td><button ><a href="?mode=confirmdel&type=<?php echo (int)$_GET['type'];?>&sid=<?php echo (int)$_GET['sid'];?>&nid=<?php echo (int)$_GET['nid'];?>">删除</a></button></td>
    </tr>
</table>


<?php 
header("Content-Type: text/html; charset=utf-8");
require_once('ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid=="" ){
	header("Location:login.php");
	exit;
}


	
$mode = urldecode($_GET['mode']);
if( $mode=="" ) $mode = urldecode($_POST['mode']);
if( $_GET['mode']=="") 
{
	
	
}

$type=(int)$_GET['type'];
$sid=(int)$_GET['sid'];
$nid=(int)$_GET['nid'];
$addnid=(int)$_GET['addnid'];
?>


<?php 

if( $_GET['mode']=="adddevice") 
{
	
	if( $type=="" || $type=="0" ){
		echo '对不起，参数有异常';
		exit;
	}
?>

<form action="?mode=confirmadd&type=<?php echo $type;?>&sid=<?php echo $sid;?>&nid=<?php echo $nid;?>&addnid=<?php echo $addnid;?>" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
    <tr>
		<td width="20%" align="right">名称：</td>
		<td width="70%"><input maxlength="32" type="text" class="search_text" id="username" name="username" /></td>		
		<td width="10%"></td>
	</tr>
	<!--
	<tr>
		<td width="20%" align="right">密码：</td>
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>	
    </tbody>
	-->
</table>
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >添加设备</button></div>
</form>

<?php 
}
if( $_GET['mode']=="confirmadd") 
{
	$type=$_GET['type'];	
	if( $type=="" || $type=="0" ){
		echo '对不起，参数有异常';
		exit;
	}
	
	
	$username=$_POST['username'];		
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' ORDER BY sid DESC";//and type='".$type."' 
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());
	  //mysql_close();
	  //exit;
	}
	$num=mysql_num_rows($result);
	
	
?>

<?php
	if($username=="" )
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，名称不能为空！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:history.back()" >返回修改</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>


<?php
	if( $num>20 ){
		echo "如果要申请更多，请联系QQ客服610854837";
		mysql_close();
	  	exit;
	}
	if( $num<=0 )
	{		
		$type=$type;
		$uid=$uid;
		$sid=3;
		$nid=1;
		if($type==3 || $type==4) $nid=0;//处理多路开关0，1，2编号问题		
		$data="0";
		$note=$username;
		$status=1;
		$regdate="";
		$lasttime="";	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);
		$lasttime=time();//当前时间 60*60是一个小时 //"2014-01-01 00:00:00";
					
		//插入家居温		
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close();
			//exit;
		}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，添加设备成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/index.php'; if($type==4) echo '/math/switch/index.php'; if($type==5) echo '/math/infrared/index.php'; if($type==6) echo '/math/door/index.php';?>'" >查看列表</button></div>
</div> 
<?php
		mysql_close();
	  	exit;
	}	
?>

<?php
	if($row = mysql_fetch_array($result))
	{	
		
		$type=$type;
		$uid=$uid;
		$sid=$row['sid']+1;		
		$nid=1;
		if($type==3 || $type==4) $nid=0;//处理多路开关0，1，2编号问题
		if($addnid==1){//处理多路编号
			$sql="SELECT nid FROM api_device WHERE uid='".$uid."' and sid='".(int)$_GET['sid']."' ORDER BY nid DESC";
			$result = mysql_query($sql);
			if (!$result)
			{
			  die('Error: ' . mysql_error());
			  //mysql_close();
			  //exit;
			}
			if($row1 = mysql_fetch_array($result))
			{
				$sid=(int)$_GET['sid'];//保持原来的sid类型
				$nid=$row1['nid']+1;//处理多路开关0，1，2编号问 题
			}			
		}
		if($row['sid']<3) $sid=3;//自动生成sid最小为3	
		$data="0";
		$note=$username;
		$status=1;
		$regdate="";
		$lasttime="";	
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);
		$lasttime=time();//当前时间 60*60是一个小时 //"2014-01-01 00:00:00";
					
		//插入家居温		
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close();
			//exit;
		}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，添加设备成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/index.php'; if($type==4) echo '/math/switch/index.php'; if($type==5) echo '/math/infrared/index.php'; if($type==6) echo '/math/door/index.php';?>'" >查看列表</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>





<?php 
}
if( $_GET['mode']=="editdevice") 
{
	
	if( $sid=="" || $sid=="0" ){ 
		echo '对不起，参数有异常';
		exit;
	}
			
	
	$sql="SELECT type,sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' ORDER BY nid DESC";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());
	  //mysql_close();
	  //exit;
	}
	$num=mysql_num_rows($result);
	if( $num<=0 )
	{
		echo '对不起，未查到该设备！';
		mysql_close();
	  	exit;
	}
	if($row = mysql_fetch_array($result))
	{
		//$sid=sprintf("%03d",$row['sid']);
		//$nid=sprintf("%03d",$row['nid']);		
		$data=$row['data'];
		$status=$row['status'];
		$regdate=$row['regdate'];
		$lasttime=$row['lasttime'];
		$note=$row['note'];
		$type=$row['type'];
	}
?>

<form action="?mode=confirmedit&type=<?php echo $type;?>&sid=<?php echo $sid;?>&nid=<?php echo $nid;?>" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
    <tr>
		<td width="20%" align="right">名称：</td>
		<td width="70%"><input maxlength="32" type="text" class="search_text" id="username" name="username" value="<?php echo $note;?>"/></td>		
		<td width="10%"></td>
	</tr>
	<!--
	<tr>
		<td width="20%" align="right">密码：</td>
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>	
    </tbody>
	-->
</table>
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >修改名称</button></div>
</form>

<?php 
}
if( $_GET['mode']=="confirmedit") 
{
	$type=$_GET['type'];	
	if( $type=="" || $type=="0" ){
		echo '对不起，参数有异常';
		exit;
	}
	
	$type=$_GET['type'];
	$sid=$_GET['sid'];
	$nid=$_GET['nid'];
	$username=$_POST['username'];		
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' ORDER BY nid DESC";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());
	  //mysql_close();
	  //exit;
	}
	$num=mysql_num_rows($result);
	
	
?>

<?php
	if($username=="" )
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，名称不能为空！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:history.back()" >返回修改</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>


<?php
	if( $num<=0 )
	{				
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，未找到该设备！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/index.php'; if($type==4) echo '/math/switch/index.php'; if($type==5) echo '/math/infrared/index.php'; if($type==6) echo '/math/door/index.php';?>'" >查看列表</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>

<?php
	if($row = mysql_fetch_array($result))
	{	
	
		$sql="UPDATE api_device SET note='".$username."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，设备修改成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/index.php'; if($type==4) echo '/math/switch/index.php'; if($type==5) echo '/math/infrared/index.php'; if($type==6) echo '/math/door/index.php';?>'" >查看列表</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>


<?php 	
}
if( $_GET['mode']=="confirmdel") 
{
	$type=$_GET['type'];	
	if( $type=="" || $type=="0" ){
		echo '对不起，参数有异常';
		exit;
	}
	
	$type=$_GET['type'];
	$sid=$_GET['sid'];
	$nid=$_GET['nid'];
			
	{	
	
		$sql="Delete FROM api_device WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."'";
		$result = mysql_query($sql);
		if (!$result)
		{
		  die('Error: ' . mysql_error());
		  //mysql_close();
		  //exit;
		}	
		
		$sql="Delete FROM api_timing WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."'";
		$result = mysql_query($sql);
		if (!$result)
		{
		  die('Error: ' . mysql_error());
		  //mysql_close();
		  //exit;
		}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，设删除成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/index.php'; if($type==4) echo '/math/switch/index.php'; if($type==5) echo '/math/infrared/index.php'; if($type==6) echo '/math/door/index.php';?>'" >查看列表</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	
?>


<?php 	
}
?>	
	

    <!--底部--> 
<div class="clear2"></div>
<div data-role="footer" style="text-align:center;">   
   
</div>
</body>
</html>

<?php 

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