<?php 
header("Content-Type: text/html; charset=utf-8");
require_once('../../ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid=="" ){
	header("Location:../../login.php");
	exit;
}


if( $_GET['mode']=="getonoff") 
{
		
	$sid=$_GET['sid'];
	$nid=$_GET['nid'];
	$data=$_GET['data'];
		
		
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
	
	
	//查找最后一个电灯开关编号
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."' ORDER BY sid DESC";
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
		$note=$row['note'];
		
		//查找网关状态
		$sql="SELECT status,lasttime FROM api_device WHERE uid='".$uid."' and sid='1' and nid='1'";
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
			//$status=$row['status'];		
			$wangguan_lasttime=$row['lasttime'];//网关最后时间
			
			$nowt=time()-60*2;//2分钟 60*60是一个小时 
			if( strtotime($wangguan_lasttime)<$nowt )
			{				
				$status=-1;
			}
		}
		
		echo "ok|";
		echo $data."onoff|";
		echo $lasttime."|";
		echo $status."|";
	}
	
	
	
	mysql_close();
	exit;
}


if( $_GET['mode']=="onoff") 
{

	$type=1;//1网关2上传
	$uid=$uid;
	$sid=sprintf("%03d",$_GET['sid']);
	$nid=sprintf("%03d",$_GET['nid']);	
	$data=$_GET['data'];
	$note="电灯开关设置";
	$status=0;
	$time="";
	$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
	
	$nowt=time();//当前时间 60*60是一个小时 
	$time=date("Y-m-d H:i:s",$nowt);
	
	//插入
	$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
	VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
		//mysql_close($con);
		//exit;
	}
	
	//更新设置最后时间
	$nowt=time();//当前时间 60*60是一个小时 
	$time=date("Y-m-d H:i:s",$nowt);		
	$sql="UPDATE api_device SET data='".$data."' WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."'";//lasttime='".$time."',
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}	
	
	
	
	//获取apikey，处理tcp协议部份 
	//------------------------------------------------------
	$sql="SELECT username,apikey FROM api_member WHERE uid='".$uid."'";
	$result = mysql_query($sql);
	if (!$result)
	{
	  die('Error: ' . mysql_error());	
	}
	$num=mysql_num_rows($result);
	if($row = mysql_fetch_array($result))
	{		
		$apikey=$row['apikey'];
		$username=$row['username'];
	}
	
	$tcpdata="mode=exe&";
	$tcpdata=$tcpdata."apikey=".$apikey."&";
	$tcpdata=$tcpdata."data={ck".(sprintf("%03d",$sid).sprintf("%03d",$nid).$data)."}";
	$url="http://".$_SERVER['HTTP_HOST']."/tcp/tcpclient.php?data=".urlencode($tcpdata);	
	$tcptext = file_get_contents($url);
	//echo $tcptext;
	//------------------------------------------------------
	
	
	echo "ok";
	mysql_close();
	exit;
}


?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－电灯开关</title>
<meta name="keywords" content="智能创客－电灯开关" />
<meta name="description" content="智能创客－电灯开关" />
<meta name="copyright" content="www.znck007.com" />
<meta name="viewport" content="user-scalable=0" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />

<link href="../../css/global.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript">

function onoff(sid,nid){
	var img = document.getElementById("imgonoff");  //定义一个img对象
	
	var sid = sid;
	var nid = nid;
	var data = "0";		
	
	if( img.src.indexOf("index38on_")>0 )
	{
		data = "1";
	}else
	{
		data = "0";
	}
	
	var url = "?mode=onoff&sid="+sid+"&nid="+nid+"&data="+data+"";		
	loadCheck(url);
	
	
	//document.getElementById("Request").innerText=url;
	
	if(data == "0"){
		//var img = document.getElementById("imgonoff");  //定义一个img对象
		img.src = '../../img/index38on_.png'; //img.src + '?'
	}			
	if(data == "1"){
		//var img = document.getElementById("imgonoff");  //定义一个img对象
		img.src = '../../img/index38on.png'; //img.src + '?'
	}
}

<!-- ------------------------------------------------------------------- ajax ------------------------------------------------------------------------ -->
var xmlobj;                                      //定义XMLHttpRequest对象 
function CreateXMLHttpRequest() 
{ 
     if(window.ActiveXObject)            //如果当前浏览器支持Active Xobject，则创建ActiveXObject对象 
     {
         xmlobj = new ActiveXObject("Microsoft.XMLHTTP"); 
     }
     else if(window.XMLHttpRequest)     //如果当前浏览器支持XMLHttp Request，则创建XMLHttpRequest对象 
     {
         xmlobj = new XMLHttpRequest(); 
     }
} 
function loadCheck(url){
	CreateXMLHttpRequest();                      //创建对象 
	xmlobj.open("GET", url, false);             //调用php
	xmlobj.onreadystatechange = StatHandler;     //判断URL调用的状态值并处理 
	xmlobj.send(null);                           //设置为不发送给服务器任何数
}
function StatHandler()                           //用于处理状态的函数 
{ 
     if(xmlobj.readyState == 4 && xmlobj.status == 200)                                   //如果URL成功访问，则输出网页 
     { 
	 	var result = xmlobj.responseText;
			
		document.getElementById("Request").innerText=result;	
     } 
	 else
	 {
	 	
	 }
}
<!-- -------------------------------------------------------------------- Request 发送请求 ------------------------------------------------------------------------ -->
//实时刷新
function makeRequest(url){
	http_request = false;
	if(window.XMLHttpRequest){
		http_request = new XMLHttpRequest();
		if(http_request.overrideMimeType){
			http_request.overrideMimeType('text/xml');
		} 
	}else if(window.ActiveXObject){
		try{
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			try{
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			}catch(e){
			}
		}
	}
	if(!http_request){
		alert("您的浏览器不支持当前操作，请使用 IE 5.0 以上版本!");
		return false;
	}
	//定义页面调用的方法init,不是init();没有();
	http_request.onreadystatechange = init;
	http_request.open('GET', url, true);
	//禁止IE缓存
	http_request.setRequestHeader("If-Modified-Since","0");
	//发送数据
	http_request.send(null);
	//每秒刷新一次页面
	setTimeout("makeRequest('"+url+"')", 2000);
}

function init(){
	if(http_request.readyState == 4){
		if(http_request.status == 0 || http_request.status == 200){
			var result = http_request.responseText;
			if(result==""){
				result = "获取数据失败";
			}
			//document.getElementById("Request").innerText=result;
			
			var str= new Array();       
			str=result.split("|");       
			for (i=0;i<str.length ;i++ )    
			{    
				
			}  
			if(str.length>2){
				//echo "ok|";
				//echo $data."onoff|";
				//echo $lasttime."|";
				//echo $status."|";
				document.getElementById("Request").innerText=str[2];
								      
				//echo '<label style="color:#FF0000">';
				//echo '网关未配置或异常';
				//echo ' </label>';
				//if( $status==0 ) echo '已经停用'; 
				//if( $status==1 ) echo '当前正常'; 
				//if( $status==2 ) echo '<label style="color:#FF0000">异常(检查红外线/sid和nid)</label>';
				if( str[3]=="-1" ){
					document.getElementById("device_status").innerHTML="<label style=\"color:#FF0000\">网关未配置或异常</label>";
				}
				if( str[3]=="0" ){
					document.getElementById("device_status").innerHTML="已经停用";
				}
				if( str[3]=="1" ){
					document.getElementById("device_status").innerHTML="当前正常";
				}
				if( str[3]=="2" ){
					document.getElementById("device_status").innerHTML="<label style=\"color:#FF0000\">异常-(检查电灯开关/sid和nid)</label>";
				}
				
			}  
			

			if(result.indexOf("0onoff")>0){
				var img = document.getElementById("imgonoff");  //定义一个img对象
				img.src = '../../img/index38on_.png'; //img.src + '?'
			}			
			if(result.indexOf("1onoff")>0){
				var img = document.getElementById("imgonoff");  //定义一个img对象
				img.src = '../../img/index38on.png'; //img.src + '?'
			}
		
			
		}else{//http_request.status != 200
			//document.getElementById("Request").innerHTML="请求失败！";
		}
	}
}
</script>
</head>



<?php 

$type=$_GET['type'];
$sid=$_GET['sid'];//"3";//类型
$nid=$_GET['nid'];//"1";//编号


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
	//查找最后一个电灯开关编号
	$sql="SELECT type,sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' ORDER BY sid DESC";
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
		$type=$row['type'];
		$sid=sprintf("%03d",$row['sid']);
		$nid=sprintf("%03d",$row['nid']);		
		$data=$row['data'];
		$status=$row['status'];
		$regdate=$row['regdate'];
		$lasttime=$row['lasttime'];
		$note=$row['note'];
	}
	else
	{
		/*
		$type=3;
		$uid=$uid;
		$sid=(int)$sid;
		$nid=(int)$nid;
		$data="0";
		$note="电灯开关名称";
		$status=1;
		$regdate="";
		$lasttime="";		
		$ip=get_onlineip();//$_SERVER["REMOTE_ADDR"];//记录IP
		
		$nowt=time();//当前时间 60*60是一个小时 
		$regdate=date("Y-m-d H:i:s",$nowt);
		$lasttime=time();//当前时间 60*60是一个小时 //"2014-01-01 00:00:00";
					
		//插入
		$sql="INSERT INTO api_device(type,uid,sid,nid,data,note,status,regdate,lasttime,ip) 
		VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$regdate."', '".$lasttime."', '".$ip."')";	
		if (!mysql_query($sql))
		{
			die('Error: ' . mysql_error());
			//mysql_close($con);
			//exit;
		}
		*/
		
		echo "对不起，该设备不存在！";
		mysql_close($con);
		exit;
	}
	
	//查找网关状态
	$sql="SELECT status,lasttime FROM api_device WHERE uid='".$uid."' and sid='1' and nid='1'";
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
		//$status=$row['status'];		
		$wangguan_lasttime=$row['lasttime'];//网关最后时间
		
		$nowt=time()-60*2;//2分钟 60*60是一个小时 
		if( strtotime($wangguan_lasttime)<$nowt )
		{
			$wangguan_off=1;
		}
	}
	
			
	
	
}
?>



<body onLoad="makeRequest('?mode=getonoff&sid=<?php echo $sid?>&nid=<?php echo $nid?>&data=<?php echo $data?>')">

<!--顶部 <button>首页</button> -->
<table class="top" border="0" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="70px" />
        <col />
        <col width="70px" />
    </colgroup>
    <tr>
        <td><button onClick="javascript:history.back()">返回</button></td>
        <td>智能创客－电灯开关</td>
        <td><a href="../switch/index.php"><button>列表</button></a></td>
    </tr>
</table>


<?php 
if( $_GET['mode']=="") 
{
?>
<div class="sxst">    
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
		
		 <tr>
            <td width="100%" align="center" >
                <label style="color:#FF9900">                   
				  	<a href="../../../devicename.php?mode=editdevice&type=<?php echo $type?>&sid=<?php echo $sid?>&nid=<?php echo $nid?>" style="text-decoration:underline; color:#33CC33;">
					<?php echo $note;?>
					</a>
					<BR>
					<a href="../../../devicename.php?mode=adddevice&type=<?php echo $type?>&sid=<?php echo $sid?>&nid=<?php echo $nid?>&addnid=1" style="text-decoration:underline; color:#33CC33;">
					添加子开关
					</a>
                </label> 
            </td>
        </tr>
			
		<tr>
            <td width="100%" align="center" >
				
				   <?php 
				   	//$nowt=time()-60*2;//2分钟 60*60是一个小时 
					//if( strtotime($lasttime)<$nowt )
					if( $data=="0" )
					{
						echo '<a onclick="onoff('.$sid.','.$nid.')"> <img src="../../img/index38on_.png" id="imgonoff" /> </a>';						
					}
					else
					{						
						echo '<a onclick="onoff('.$sid.','.$nid.')"> <img src="../../img/index38on.png" id="imgonoff" /> </a>';		
					}
					
				   ?>
               
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				状态：
                <label style="color:#00CC00" id="device_status"> 
				   <?php 
				   	if( $wangguan_off==1 )
					{
						echo '<label style="color:#FF0000">';
						echo '网关未配置或异常';
						echo ' </label>';
					}
					else
					{						
						if( $status==0 ) echo '已经停用'; 
					   	if( $status==1 ) echo '当前正常'; 
					   	if( $status==2 ) echo '<label style="color:#FF0000">异常(检查电灯开关/sid和nid)</label>';
					   //echo getTimeDifference($lasttime);
					}				   
				   ?>
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >				
					
				
                <label style="color:#00CC00" id="Timing"> 
				<a href="../../../devicetime.php?mode=&type=4&sid=<?php echo $sid?>&nid=<?php echo $nid?>" style="color:#33CC33;">				                  
				   <?php 				  
				   
					//获取定时设置信息
					$sql="SELECT type,data,time FROM api_timing WHERE uid='".$uid."' and sid='".(int)$sid."' and nid='".(int)$nid."' ORDER BY data DESC";	
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
						$timing_type=$row['type'];
						$timing_data=$row['data'];
						$timing_time=$row['time'];
						
						if($timing_type==1) echo '定时';//定时
						if($timing_type==2) echo '每天';
						if($timing_type==3) echo '工作日';
						if($timing_type==4) echo '周未';
						echo substr($timing_time,5,11);
						if($timing_data==0) echo '关';
						if($timing_data==1) echo '开';
						//echo getTimeDifference($timing_time);
						echo ' <img src="../../../img/index39tedit.png" /><br>';
					}
					
					if($timing_time=="") 
					{
						echo "未设置计划";
					}				  			  
				  
				   ?>
				  
				   </a>
                </label>
				
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				sid:
                <label style="color:#FF9900">                   
				   <?php echo (int)$sid;?> 
                </label>
				nid:
                <label style="color:#FF9900">                   
				   <?php echo (int)$nid;?> 
                </label>
            </td>
        </tr>	
       
	   <tr>
            <td width="100%" align="center" >
                <label>                   
				  	提示：刷固件时sid,nid修改成如上！					
                </label> 
            </td>
        </tr>      
	  
	   <tr>
            <td width="100%" align="center" >
                <label id="Request">                   
				  	
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
				$sql="SELECT sid,nid,data,temperature,humidity,status,time FROM api_temperature WHERE uid='".$uid."' and sid='".$sid."' and nid='".$nid."' order by time desc limit 0,30";
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
					echo "电灯开关：".sprintf("%02d",$row[1])."时间：".getTimeDifference($row[0])."<BR>";			
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
<!--
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='?mode=list'" >查看操作记录</button></div><BR>
-->
<div style="padding: 0 8px"><button style="width: 100%" class="button3" onClick="javascript:window.location.href='http://www.znck007.com/forum.php?mod=viewthread&tid=26577'" >电灯开关DIY教程</button></div>

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