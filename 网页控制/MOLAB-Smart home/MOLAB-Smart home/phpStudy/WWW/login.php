
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－登陆</title>
<meta name="keywords" content="智能创客－登陆" />
<meta name="description" content="智能创客－登陆" />
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
        <td>智能创客－登陆</td>
        <td><a href="reguser.php"> <button>注册</button> </a></td>
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
	$username=$_SESSION['username'];
	if( $username=="" ) $username=$_COOKIE['username'];
	$username=urldecode($username);
}
	

?>


<?php 
if( $_GET['mode']=="") 
{
?>

<form action="?mode=login" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
    <tr>
		<td width="20%" align="right">邮箱：</td>
		<td width="70%"><input maxlength="32" type="text" class="search_text" id="username" name="username" value="<?php echo $username;?>" /></td>		
		<td width="10%"></td>
	</tr>
	<tr>
		<td width="20%" align="right">密码：</td>
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>	
	<tr>
		<td colspan="3" width="20%" align="right"><a href="forget.php" style="text-decoration:underline;">忘记密码</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>		
	</tr>
    </tbody>
</table>
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >登陆创客</button></div>
</form>

<?php 
}
if( $_GET['mode']=="login") 
{
	$username=$_POST['username'];
	$password=$_POST['password'];	
	
	$sql="SELECT uid,username FROM api_member WHERE username='".$username."' and password='".md5($password)."'";
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
	if($username=="" )
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
	if( $num<=0 )
	{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，用户名或密码错误！</td>            
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
		$uid=$row['uid'];		
		$username=$row['username'];
	}
	
	
	//保存到session	
	$_SESSION['uid'] = $uid;
	$_SESSION['username'] = urlencode($username);
	$_SESSION['password'] = $password;
	
	
	//一个小时,1年
	setcookie("uid", $uid, time()+3600*24*365);
	setcookie("username", urlencode($username), time()+3600*24*365);
	setcookie("password", $password, time()+3600*24*365);
	
	
	if( $username=="znck007" ) {
		//echo $username; 
		$_SESSION['admin'] = $uid;//处理超级管理员
		setcookie("admin", $uid, time()+3600*24*365);//处理超级管理员
	}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">登陆成功，技术宅开始拯救世界吧！</td>            
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