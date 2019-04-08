
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－设置定时</title>
<meta name="keywords" content="智能创客－设置定时" />
<meta name="description" content="智能创客－设置定时" />
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
        <td>智能创客－设置定时</td>
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

$type=(int)$_GET['type'];
$sid=(int)$_GET['sid'];
$nid=(int)$_GET['nid'];

if( $sid=="" || $sid=="0"  ){//$nid=="" || $nid=="0" || 
	echo '对不起，参数有异常';
	exit;
}	
	
	
$mode = urldecode($_GET['mode']);
if( $mode=="" ) $mode = urldecode($_POST['mode']);
if( $_GET['mode']=="") 
{
	
	
}
	

?>


<?php 
if( $_GET['mode']=="") 
{
?>

<form action="?mode=settime&type=<?php echo $type;?>&sid=<?php echo $sid;?>&nid=<?php echo $nid;?>" method="post" onSubmit="return CheckSearch();">
<table border="0" cellpadding="0" cellspacing="0" class="search_btn">
    <colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody> 
    <tr height="50">
		<td width="20%" align="right">类型：</td>
		<td width="70%">
		<label>
		<select name="type" class="search_text">
				<option value="1" selected=&#1>定时</option>
				<option value="2" >每天</option>
				<!--
				<option value="3" >工作日</option>
				<option value="4" >周未</option>
				-->
		</select>
		</label>					
		<td width="10%"></td>
	</tr>
	
	<tr height="50">
		<td width="20%" align="right">时间：</td>
		<td width="70%">		
		<label>
		<select name="month" >
			<?php 
				/*
				$m=date("m",time());//当时月 Y-m-d H:i:s
				for( $i=0;$i<8;$i++ )
				{
					if( $i==0 ) echo '<option value="'.($m+$i).'" selected=&#1>'.($m+$i).'</option>';					
					if( $i>0 ) echo '<option value="'.($m+$i).'" >'.($m+$i).'</option>';
				}
				*/
			?>
			<?php 
				$m=date("m",time());//当时日 Y-m-d H:i:s
				echo '<option value="'.($m).'" selected=&#1>'.($m).'</option>';
				for( $i=1;$i<13;$i++ )
				{					
					echo '<option value="'.($i).'" >'.($i).'</option>';
				}
			?>	
		</select>
		</label>
		<label>
		<select name="day" >
			<?php 
				$m=date("d",time());//当时日 Y-m-d H:i:s
				echo '<option value="'.($m).'" selected=&#1>'.($m).'</option>';
				for( $i=1;$i<32;$i++ )
				{					
					echo '<option value="'.($i).'" >'.($i).'</option>';
				}
			?>			
		</select>
		</label>&nbsp;&nbsp;
		<label>
		<select name="hour" >
			<?php 
				$m=date("H",time());//当时时 Y-m-d H:i:s
				echo '<option value="'.($m).'" selected=&#1>'.($m).'</option>';
				for( $i=0;$i<24;$i++ )
				{					
					echo '<option value="'.($i).'" >'.($i).'</option>';
				}
			?>	
		</select>
		</label>
		<label>
		<select name="minute" >
				<?php 
				$m=date("i",time());//当时时 Y-m-d H:i:s
				echo '<option value="'.($m).'" selected=&#1>'.($m).'</option>';
				for( $i=0;$i<60;$i++ )
				{					
					echo '<option value="'.($i).'" >'.($i).'</option>';
				}
			?>	
		</select>
		</label>
		<td width="10%"></td>
	</tr>
			
	
	<tr height="60">
		<td width="20%" align="right">开关：</td>
		<td width="70%">
		<label>
		<select name="switch" class="search_text"><br>
			<?php if($_GET['type']==3 ||$_GET['type']==4 ||$_GET['type']==6 ) {?>
				<option value="0" selected=&#1>关闭</option>
				<option value="1" >打开</option>	
			<?php }?>
			<?php if($_GET['type']==5) {?>
				<option value="1" selected=&#1>点击</option>			
			<?php }?>				
		</select>
		</label>					
		<td width="10%"></td>
	</tr>
	
	
	<tr height="50">
		<td width="20%" align="right">快捷：</td>
		<td width="70%">
		<label>
		<select name="shortcut" class="search_text">
				<option value="0" selected=&#1>选择多少时间后执行</option>
				<option value="6" >6秒后执行</option>
				<option value="60" >1分钟后执行</option>
				<option value="600" >10分钟后执行</option>
				<option value="1200" >20分钟后执行</option>
				<option value="1800" >30分钟后执行</option>
				<option value="3600" >1小时后执行</option>
				<option value="7200" >2小时后执行</option>
				<option value="14400" >4小时后执行</option>
				<option value="28800" >8小时后执行</option>
				<option value="86400" >24小时后执行</option>
		</select>
		</label>					
		<td width="10%"></td>
	</tr>
	
	<!--
	<tr>
		<td width="20%" align="right">密码：</td>	<input maxlength="32" type="text" class="search_text" id="username" name="username" /></td>		
		<td width="70%"><input maxlength="16" type="password" class="search_text" id="password" name="password" /></td>		
		<td width="10%"></td>
	</tr>	
    </tbody>
	-->
</table>
<div style="padding: 0 8px"><BR><BR><BR><BR><button type="submit" style="width: 100%" class="button2" >设置</button></div>
</form>

<?php 
}
if( $_GET['mode']=="settime") 
{
	$sid=$_GET['sid'];
	$nid=$_GET['nid'];
	
	$type=$_POST['type'];
	$shortcut=$_POST['shortcut'];
	$switch=$_POST['switch'];	
	$month=$_POST['month'];	
	$day=$_POST['day'];	
	$hour=$_POST['hour'];	
	$minute=$_POST['minute'];
	
	$year=date("Y",time());//当时时 Y-m-d H:i:s
	
	if($shortcut>0) $type=1;
	
	$sql="SELECT id FROM api_timing WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' and data='".$switch."'";
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
		$type=$type;
		$uid=$uid;
		$sid=$sid;
		$nid=$nid;
		$data=$switch;
		$note="定时";
		$status=1;
		$time="";		
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		if($shortcut>0) $type=1;
		
		$nowt=time()+$shortcut;//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);		
			
		if($shortcut==0)
		{	
			$nowt=strtotime($year."-".$month."-".$day." ".$hour.":".$minute);
			$time=date("Y-m-d H:i:s",$nowt);
		}
				
		//插入
		$sql="INSERT INTO api_timing(type,uid,sid,nid,data,note,status,time,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close();
			//exit;
		}	
		
		$type=(int)$_GET['type'];//获取设置类型
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，设置成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==4) echo '/math/switch/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==5) echo '/math/infrared/kaiguan.php?sid='.$sid.'&nid='.$nid;?>'" >查看设置</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	}	

	
	if($row = mysql_fetch_array($result))
	{
		if($shortcut>0) $type=1;
		
		$data=$switch;
		$nowt=time()+$shortcut;//当前时间 60*60是一个小时 
		$time=date("Y-m-d H:i:s",$nowt);
		
		
		if($shortcut==0)
		{	
			$nowt=strtotime($year."-".$month."-".$day." ".$hour.":".$minute);
			$time=date("Y-m-d H:i:s",$nowt);
		}
		
		$sql="UPDATE api_timing SET type='".$type."',data='".$data."',time='".$time."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."' and data='".$data."'";
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
		}
		
		/*
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
		$sm = new smail( "admin@znck007.com", "znck710155", "smtp.exmail.qq.com" );
		$end = $sm->send( $sto, $sfrom, $stitle, $sneirong );		
		if( $end ) echo $end; 
		
		//echo '<table width="100%"><tr><td align="center"><BR>恭喜，密码已经发送到您的邮件！<BR>请登陆您的邮件查看并重新设置吧！<BR><BR><a href="javascript:history.back()">[点击这里返回]</a><a href="user.php" style="text-decoration:none;"></a><td><tr></table>';//javascript:history.back()	
		//mysql_close();
		//header("Location:user.php");	
		//exit;	
		
	//}
	*/
	
	$type=(int)$_GET['type'];//获取设置类型
?>
<div class="sxst">
 	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">恭喜，更新成功了！</td>            
		</tr>
	</table>
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==4) echo '/math/switch/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==5) echo '/math/infrared/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==6) echo '/math/door/kaiguan.php?sid='.$sid.'&nid='.$nid;?>'" >查看设置</button></div>
</div>	
<?php 
	}
}
?> 

 
 
<?php 	

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
	<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='<?php if($type==3) echo '/math/power/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==4) echo '/math/switch/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==5) echo '/math/infrared/kaiguan.php?sid='.$sid.'&nid='.$nid; if($type==6) echo '/math/door/kaiguan.php?sid='.$sid.'&nid='.$nid;?>'" >查看设置</button></div>
</div>
<?php
		mysql_close();
	  	exit;
	} 	
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