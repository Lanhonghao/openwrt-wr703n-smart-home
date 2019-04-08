
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－忘记密码</title>
<meta name="keywords" content="智能创客－忘记密码" />
<meta name="description" content="智能创客－忘记密码" />
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
        <td>智能创客－忘记密码</td>
        <td><a href="login.php"> <button>登陆</button> </a></td>
    </tr>
</table>


<?php 
header("Content-Type: text/html; charset=utf-8");
require_once('ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid!="" ){
	//header("Location:user.php");
	//exit;
}

$mode = urldecode($_GET['mode']);
if( $mode=="" ) $m_type = urldecode($_POST['mode']);
if( $_GET['mode']=="") 
{
	
	
}
	

?>


<?php 
if( $_GET['mode']=="") 
{
?>

<form action="?mode=forget" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
    <tr>
		<td width="20%" align="right">邮箱：</td>
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
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >找回密码</button></div>
</form>

<?php 
}
if( $_GET['mode']=="forget") 
{
	$username=$_POST['username'];
	$password=$_POST['password'];	
	
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
			<td align="center" width="100%" style="color:#00AA00">对不起，此邮箱不存在！</td>            
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
		
		
		$t=date("Y-m-d H:i:s");
		$tkey=md5($t.$uid."16151ffgbdr");
		
		$tixing='密码找回！';
		//$Email="";
		$sto=$username;
		$sfrom="智能创客<admin@znck007.com>";
		$stitle='《智能创客》'.preg_replace("(<.*?>)", "", $tixing).'';//<img src="'.$hostking.'/img/preview1.png" />
		
		$sneirong='<font color=black><B>'.$username.'</B></font> <font color=red>'.$tixing.'</font>。<BR><BR>马上重设密码<a href="http://m.znck007.com/forget.php?mode=editpass&t='.urlencode($t).'&tkey='.$tkey.'&uid='.$uid.'">http://m.znck007.com/forget.php?mode=editpass&t='.urlencode($t).'&tkey='.$tkey.'&uid='.$uid.'</a><font color=blue><BR>如果无法点击请复制到浏览器打开。</font>。<BR><BR>本邮件是系统发送，请不要回复哦。';
				
		include("smail.php"); 
		// 只需要把这部分改成你的信息就行 
		$sm = new smail( "admin@znck007.com", "*********", "smtp.exmail.qq.com" );
		$end = $sm->send( $sto, $sfrom, $stitle, $sneirong );		
		if( $end ) echo $end; 
		
		//echo '<table width="100%"><tr><td align="center"><BR>恭喜，密码已经发送到您的邮件！<BR>请登陆您的邮件查看并重新设置吧！<BR><BR><a href="javascript:history.back()">[点击这里返回]</a><a href="user.php" style="text-decoration:none;"></a><td><tr></table>';//javascript:history.back()	
		//mysql_close($con);
		//header("Location:user.php");	
		//exit;	
		
	//}
		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，密码已经发送到您的邮件！<BR>请登陆您的邮件查看并重新设置吧！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='login.php'" >前往登陆</button></div>
</div>	
<?php 
	}
}
if( $_GET['mode']=="editpass") 
{
	$t=urldecode($_GET['t']);
	$tkey=$_GET['tkey'];
	$uid=$_GET['uid'];
	if( md5($t.$uid."16151ffgbdr")!=$tkey){
		echo '<table width="100%"><tr><td align="center"><BR>对不起，非法修改密码！<a href="user.php" style="text-decoration:none;"></a><td><tr></table>';
		exit;	
	}
	
	
?>    

<form action="?mode=editpassyes&t=<?php echo $_GET['t'];?>&tkey=<?php echo $_GET['tkey'];?>&uid=<?php echo $_GET['uid'];?>" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody>
	<!--
    <tr>
		<td width="20%" align="right">邮箱：</td>
		<td width="70%"><input maxlength="32" type="text" class="search_text" id="username" name="username" /></td>		
		<td width="10%"></td>
	</tr>
	-->
	<tr>
		<td width="20%" align="right">密码：</td>
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>	
	
	<tr>
		<td width="20%" align="right">确认：</td>
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password1" name="password1" /></td>		
		<td width="10%"></td>
	</tr>
    </tbody>
	
</table>
<div style="padding: 0 8px"><button type="submit" style="width: 100%" class="button2" >确定修改</button></div> 
</form>


<?php 	
}

if( $_GET['mode']=="editpassyes") 
{
	$t=urldecode($_GET['t']);
	$tkey=$_GET['tkey'];
	$uid=$_GET['uid'];
	if( md5($t.$uid."16151ffgbdr")!=$tkey){
		echo '<table width="100%"><tr><td align="center"><BR>对不起，非法修改密码！<a href="user.php" style="text-decoration:none;"></a><td><tr></table>';
		exit;	
	}
	
	$password=$_POST['password'];
	$password1=$_POST['password1'];
	
	$sql="SELECT uid,username FROM api_member WHERE uid='".$uid."'";
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
	{}else{		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">对不起，邮箱不存在！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:history.back()" >返回修改</button></div></div>
</div>
<?php
		mysql_close($con);
	  	exit;
	}
	
	if( $password!="" ){
		$sql="UPDATE api_member SET password='".md5($password)."' WHERE uid='".$uid."'";		
		$result = mysql_query($sql);
		if( !$result ){
			die('Error: ' . mysql_error());
		}
	}	
?>


<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，修改密码成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='login.php'" >前往登陆</button></div>
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