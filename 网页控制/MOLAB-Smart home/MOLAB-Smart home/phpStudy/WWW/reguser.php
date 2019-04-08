
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－注册</title>
<meta name="keywords" content="智能创客－注册" />
<meta name="description" content="智能创客－注册" />
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
        <td>智能创客－注册</td>
        <td><a href="login.php"> <button>登陆</button> </a></td>
    </tr>
</table>


<?php 
header("Content-Type: text/html; charset=utf-8");
require_once('ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid!="" ){
	header("Location:user.php");
	exit;
}

if( $_GET['mode']=="") 
{
	
	
}
	

?>


<?php 
if( $_GET['mode']=="") 
{
?>

<form action="?mode=reguser" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
    <tr>
		<td width="20%" align="right">邮箱：</td>
		<td width="70%"><input maxlength="32" type="text" class="search_text" id="username" name="username" /></td>		
		<td width="10%"></td>
	</tr>
	<tr>
		<td width="20%" align="right">密码：</td>
		<td width="70%"><input maxlength="16" type="text" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>
	<tr>
		<td width="20%" align="right">确认：</td>
		<td width="70%"><input maxlength="16" type="text" class="search_text" id="password1" name="password1" /></td>		
		<td width="10%"></td>
	</tr>
    </tbody>
</table>
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >提交注册</button></div>
</form>

<?php 
}
if( $_GET['mode']=="reguser") 
{
	$username=$_POST['username'];
	$password=$_POST['password'];
	$password1=$_POST['password1'];
	
	$sql="SELECT uid FROM api_member WHERE username='".$username."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());
	  //mysql_close($con);
	  //exit;
	}
	$num=mysql_num_rows($result);
?>

<?php
	if($username=="" || (!strpos($username, "@") & !strpos($username, ".")))
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，邮箱格式有误！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:history.back()" >返回修改</button></div>
</div>
<?php
		mysql_close($con);
	  	exit;
	}	
?>

<?php
	if($password!=$password1 || $password=="")
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，密码不一致或为空！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:history.back()" >返回修改</button></div>
</div>
<?php
		mysql_close($con);
	  	exit;
	}	
?>


<?php
	if($row = mysql_fetch_array($result))
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，用户名已经存在！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='login.php'" >前往登陆</button></div>
</div>
<?php
		mysql_close($con);
	  	exit;
	}	
?>



<?php	
	
	$uid=$uid;
	$username=$username;
	$password=md5($password);
	$subpass="";
	$level=0;
	$status=1;
	$subname="";
	$mobile="";
	$qq="";
	$question="";
	$answer="";
	$address="";
	$regdate="";	
	$lasttime=""; 
	$apikey=substr(md5($username),8,16);//echo md5($username)."|";echo $apikey;
	$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
	
	$nowt=time();//当前时间 60*60是一个小时 
	$regdate=date("Y-m-d H:i:s",$nowt);
	
	//插入家居温湿度		
	$sql="INSERT INTO api_member(username,password,subpass,level,status,subname,mobile,qq,question,answer,address,regdate,lasttime,apikey) 
	VALUES ('".$username."', '".$password."', '".$subpass."', '".$level."', '".$status."', '".$subname."', '".$mobile."', '".$qq."', '".$question."', '".$answer."', '".$address."', '".$regdate."', '".$lasttime."', '".$apikey."')";	
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
		//mysql_close($con);
		//exit;
	}
	
	$sql="SELECT uid FROM api_member WHERE username='".$username."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());
	  //mysql_close($con);
	  //exit;
	}
	if($row = mysql_fetch_array($result))
	{		
		$uid=$row['uid'];		
	}
	
	//保存到session	
	$_SESSION['uid'] = $uid;
	$_SESSION['username'] = urlencode($username);
	$_SESSION['password'] = $password;
	
	
	//一个小时,1年
	setcookie("uid", $uid, time()+3600*24*365);
	setcookie("username", urlencode($username), time()+3600*24*365);
	setcookie("password", $password, time()+3600*24*365);
		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">注册成功，技术宅开始拯救世界吧！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='user.php'" >前往用户中心</button></div>
</div>	
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