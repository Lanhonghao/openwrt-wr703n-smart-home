
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－用户中心</title>
<meta name="keywords" content="智能创客－用户中心" />
<meta name="description" content="智能创客－用户中心" />
<meta name="copyright" content="www.znck007.com" />
<meta name="viewport" content="user-scalable=0" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />

<link href="css/global.css" rel="stylesheet" type="text/css" />

</head>

<body>

<!--顶部 -->
<table class="top" border="0" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="70px" />
        <col />
        <col width="70px" />
    </colgroup>
    <tr>
        <td><button onClick="javascript:history.back()">返回</button></td>
        <td>智能创客－用户中心</td>
        <td><a href="index.php"> <button>首页</button></a></td>
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

$admin=$_SESSION['admin'];
if( $admin=="" ) $admin=$_COOKIE['admin'];
//echo $admin;
if( $_GET['mode']=="") 
{
	$sql="SELECT username,regdate,lasttime,apikey FROM api_member WHERE uid='".$uid."'";
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
		$apikey=$row['apikey'];
		$username=$row['username'];
		$regdate=$row['regdate'];
		$lasttime=$row['lasttime'];
	}	
	
}
?>


<?php 
if( $_GET['mode']=="") 
{
?>
<div class="sxst">    
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
		<tr>
			<td width="100%" align="center" >
				
				<label style="color:#666666">                   
				   <?php echo $username.''.$no.'';?>
				</label>
			</td>
		</tr>	
		<tr>
            <td width="100%" align="center" >
				当前头衔：
                <label style="color:#00AA00">                   
				   <?php 
				   	$nowt=time()-60*2;//2分钟 60*60是一个小时 
					if( strtotime($lasttime)<$nowt )
					{
						echo "入门创客";
					}
					else
					{
						echo "普通创客";
					}
					
				   ?>
                </label>
            </td>
        </tr>
				
		
		<tr>
            <td width="100%" align="center" >
				注册时间:
                <label style="color:#FF9900">                   
				   <?php echo $regdate;?> 
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				apikey:
                <label style="color:#FF9900">                   
				   <?php echo $apikey;?> 
                </label>
            </td>
        </tr>
		<tr>
            <td width="100%" align="center" >
                <label>                   
				  	提示：apikey是您的身份密码！					
                </label> 
            </td>
        </tr>
       
	   
    </table>
</div>	
<?php 
}
if( $_GET['mode']=="quit") 
{
		//保存到session	
	$_SESSION['uid'] = "";
	//$_SESSION['username'] = "";
	//$_SESSION['password'] = "";
	$_SESSION['admin'] = "";
	
	//一个小时,1年
	setcookie("uid", "", time()+3600*24*365);
	//setcookie("username", "", time()+3600*24*365);
	//setcookie("password", "", time()+3600*24*365);
	setcookie("admin", "", time()+3600*24*365);
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">退出成功！</td>            
		</tr>
	</table>
</div>	
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='index.php'" >前往首页<?php echo $username?></button></div>
<?php 
mysql_close();
exit;
}
?>





<?php 
if( $_GET['mode']=="jiehuan") 
{
	//保存到session	
	$_SESSION['uid'] = $_GET['uid'];
	$_SESSION['username'] = $_GET['username'];
	$_SESSION['password'] = $_GET['password'];
	
	
	//一个小时,1年
	setcookie("uid", $_GET['uid'], time()+3600*24*365);
	setcookie("username", $_GET['username'], time()+3600*24*365);
	setcookie("password", $_GET['password'], time()+3600*24*365);
	
	//更新数据库
	$nowt=time();//当前时间 60*60是一个小时 
	$lasttime=date("Y-m-d H:i:s",$nowt);		
	$sql="UPDATE api_member SET lasttime='".$lasttime."' WHERE uid='".$_GET['uid']."'";
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">切换成功！<?php echo $_GET['username'];?></td>            
		</tr>
	</table>
</div>	
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='index.php'" >前往首页<?php echo $username?></button></div>
<?php 
mysql_close();
exit;
}
?>
<?php 
if( $admin>0 ) 
{
		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">
			<?php 				
				$sql="select `uid`, `username`, `password`, `subpass`, `level`, `status`, `subname`, `mobile`, `qq`, `question`, `answer`, `address`, `regdate`, `lasttime`, `apikey` from api_member WHERE uid>0 order by lasttime desc,uid desc limit 0,60";// and sid='".$sid."' and nid='".$nid."'
				$result = mysql_query($sql);
				if (!$result)
				{
				  die('Error: ' . mysql_error());
				  //mysql_close($con);
				  //exit;
				}
				$num=mysql_num_rows($result);
				echo "数量：".$num."<BR>";
				while($row = mysql_fetch_array($result)) 
				{
					
					$uid=$row['uid'];
					$username=$row['username'];					
					$lasttime=$row['lasttime'];					
					$apikey=$row['apikey'];
					
					echo "".$uid." ".$username." ".$apikey." ".$lasttime." <a href=?mode=jiehuan&uid=".$uid."&username=".$username."&password=".$password.">切换</a><BR>";			
				}
			?>
			</td>            
		</tr>
	</table>
</div>	
<?php 
}
?>

<div class="clear2"></div>
<div style="padding: 0 8px"><button style="width: 100%" class="button4" onClick="javascript:window.location.href='?mode=quit'" >退出<?php echo $username?></button></div>

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