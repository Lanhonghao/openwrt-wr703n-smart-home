
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－水滴检测</title>
<meta name="keywords" content="智能创客－水滴检测" />
<meta name="description" content="智能创客－水滴检测" />
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
        <td>智能创客－水滴检测</td>
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


$sid="1";
$nid="6";


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
		$data="0";
		$note="水滴设备";
		$status=1;
		$regdate="";
		$lasttime="";		
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);
		$lasttime="2014-01-01 00:00:00";
					
		//插入家居温		
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
				功能介绍：
                <label style="color:#00AA00">                   
				   <?php 
				   	$nowt=time()-60*30;//30分钟 60*60是一个小时 
					if( strtotime($lasttime)<$nowt )					
					{
						echo '<label style="color:#00AA00">';
						echo '水滴检测';
						echo ' </label>';
					}
					else
					{						
						echo '<label style="color:#00AA00">';
						echo '可以在api目录index.php添加自定义推送邮件/手机提醒或功能';
						echo ' </label>';
					}
					
				   ?>
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				报警时间：
                <label style="color:#00CC00">                   
				   <?php echo getTimeDifference($lasttime);?>
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				sid:
                <label style="color:#FF9900">                   
				   <?php echo $sid;?> 
                </label>
				nid:
                <label style="color:#FF9900">                   
				   <?php echo $nid;?> 
                </label>
            </td>
        </tr>		
       
	   <tr>
            <td width="100%" align="center" >
                <label>                   
				  	提示：sid,nid在刷固件时使用！					
                </label> 
            </td>
        </tr>
	   
    </table>
</div>	
<?php 
}
if( $_GET['mode']=="list") 
{
		
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">
			<?php 
				
				$sid=sprintf("%03d",$sid);
				$nid=sprintf("%03d",$nid);		
				$sql="select time,data from api_datalist WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' group by time order by time desc limit 0,30";//5分钟				
				$result = mysql_query($sql);
				if (!$result)
				{
				  die('Error: ' . mysql_error());
				  //mysql_close($con);
				  //exit;
				}
				$num=mysql_num_rows($result);
				while($row = mysql_fetch_array($result)) 
				{
					/*
					$sid=sprintf("%03d",$row['sid']);
					$nid=sprintf("%03d",$row['nid']);
					$data=$row['data'];
					$status=$row['status'];
					$temperature=$row['temperature'];
					$humidity=$row['humidity'];
					$time=$row['time'];
					*/
					echo "水滴检测：".$row[1]." 时间：".getTimeDifference($row[0])."<BR>";			
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
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='?mode=list'" >查看水滴检测记录</button></div><BR>
<div style="padding: 0 8px"><button style="width: 100%" class="button3" onClick="javascript:window.location.href='http://www.znck007.com/forum.php?mod=viewthread&tid=28010'" >水滴检测DIY教程</button></div> 

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