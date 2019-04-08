
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－网关</title>
<meta name="keywords" content="智能创客－网关" />
<meta name="description" content="智能创客－网关" />
<meta name="copyright" content="www.znck007.com" />
<meta name="viewport" content="user-scalable=0" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />

<link href="../../css/global.css" rel="stylesheet" type="text/css" />

</head>

<body>

<!--顶部 <button>首页</button> -->
<table class="top" border="0" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="70px" />
        <col />
        <col width="70px" />
    </colgroup>
    <tr>
        <td><button onClick="javascript:history.back()">返回</button></td>
        <td>智能创客－网关</td>
        <td><a href="../../index.php"></a></td>
    </tr>
</table>


<?php 
header("Content-Type: text/html; charset=utf-8");
require_once('../../ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid=="" ){
	header("Location:../../login.php");
	exit;
}

//$uid="1";
$sid="1";
$nid="1";


if( $_GET['mode']=="") 
{
	$sql="SELECT username,apikey FROM api_member WHERE uid='".$uid."'";
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
	}
	
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."'";
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
		$status=$row['status'];
		$regdate=$row['regdate'];
		$lasttime=$row['lasttime'];
	}
	else
	{
		$type=1;
		$uid=$uid;
		$sid=(int)$sid;
		$nid=(int)$nid;
		$data=$data;
		$note="网关设备";
		$status=1;
		$regdate="";
		$lasttime="";		
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);
		$lasttime="2014-01-01 00:00:00";
					
		//插入家居温湿度		
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
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
				当前状态：                                   
				   <?php 
				   	$nowt=time()-60*2;//2分钟 60*60是一个小时 
					if( strtotime($lasttime)<$nowt )
					{
						echo '<label style="color:#FF0000">';
						echo '网关未配置或异常';
						echo ' </label>';
					}
					else
					{						
						echo '<label style="color:#00AA00">';
						echo '网关正常运行中...';
						echo ' </label>';
					}
					
				   ?>
               
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				最近活动：
                <label style="color:#00CC00">                   
				   <?php echo getTimeDifference($lasttime);?>
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
					<!--
					<tr>
						<td width="100%" align="center" >
							
							<label style="color:#666666">                   
							   <?php echo $username;?>
							</label>
						</td>
					</tr>
					<BR>________________________________
					<BR>刷网关的固件时必须设置正确！
					-->
                </label> 
            </td>
        </tr>
       
	   
    </table>
</div>	
<?php 
}
if( $_GET['mode']=="test") 
{
		$type=1;//1网关2上传
		$uid=$uid;
		$sid=2;
		$nid=1;
		$data="webset";
		$note="网关设置";
		$status=0;
		$time="";
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		//插入家居温湿度		
		$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">刷新成功了，亲！</td>            
		</tr>
	</table>
</div>	
<?php 
}
?> 

<div class="clear2"></div>
<div style="padding: 0 8px"><button style="width: 100%" class="button3" onClick="javascript:window.location.href='http://www.znck007.com/forum.php?mod=viewthread&tid=24216'" >网关DIY教程</button></div>

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