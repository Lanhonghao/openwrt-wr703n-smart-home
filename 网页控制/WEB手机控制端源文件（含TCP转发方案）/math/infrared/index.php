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
		
	//$sid=$_GET['sid'];
	//$nid=$_GET['nid'];
	//$data=$_GET['data'];		
		
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
	//查找最后一个红外线编号
	$sql="SELECT sid,nid,data,note,status,regdate,lasttime FROM api_device WHERE uid='".$uid."' and type='5' ORDER BY sid ASC";
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
		$sid=$row['sid'];//sprintf("%03d",$row['sid']);
		$nid=$row['nid'];//sprintf("%03d",$row['nid']);		
		$data=$row['data'];
		$status=$row['status'];
		$regdate=$row['regdate'];
		$lasttime=$row['lasttime'];
		$note=$row['note'];
		$data=str_replace(",","_",$data);//替换,成_，防止转义问题
		
		echo $sid.",";
		echo $nid.",";
		echo $data.",";
		echo $note."|";
	}
	mysql_close();
	exit;
}


if( $_GET['mode']=="onoff") 
{

	$type=1;//1网关2上传3定时
	$uid=$uid;
	$sid=sprintf("%03d",$_GET['sid']);
	$nid=sprintf("%03d",$_GET['nid']);
	$data=$_GET['data'];
	$note="红外线设置";
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
<title>智能创客－红外线</title>
<meta name="keywords" content="智能创客－红外线" />
<meta name="description" content="智能创客－红外线" />
<meta name="copyright" content="www.znck007.com" />
<meta name="viewport" content="user-scalable=0" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />

<link href="../../css/global.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript">

function onoff(sid,nid,rdata){
	var imgid="imgonoff"+sid;
	var img = document.getElementById(imgid);  //定义一个img对象
	
	var sid = sid;
	var nid = nid;
	var data = rdata;
	
	if( img.src.indexOf("index41hwxon_")>0 )
	{
		data = "1";
	}else
	{
		data = "0";
	}
	
	
	if(data == "0"){
		//var img = document.getElementById("imgonoff");  //定义一个img对象
		img.src = '../../img/index41hwxon_.png'; //img.src + '?'
	}			
	else//if(data == "1")
	{
		//var img = document.getElementById("imgonoff");  //定义一个img对象
		img.src = '../../img/index41hwxon.png'; //img.src + '?'
	}
	
	data = "1";//只发送1
	//var rdata=document.getElementById("rdata").value;
	if(rdata.length>1) data=rdata.replace(/_/g, ",");//转义为,
	
	var url = "?mode=onoff&sid="+sid+"&nid="+nid+"&data="+data+"";		
	loadCheck(url);
	
	//document.getElementById("device").innerHTML=url;	
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
			
		//document.getElementById("device").innerHTML=result;
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
			//document.getElementById("device").innerText=result;
			
			var li_html="";
			var str= new Array();       
			str=result.split("|");       
			for (i=0;i<str.length-1;i++ )    
			{    
				var temp= new Array();
				temp=str[i].split(",");
				if(temp.length>2)
				{
					li_html=li_html+"<li>";
					li_html=li_html+"<div class=\"zycz\">";
					
					if(temp[2]=="0")
					li_html=li_html+"<a onclick=\"onoff('"+temp[0]+"','"+temp[1]+"','"+temp[2]+"')\"> <img src=\"../../img/index41hwxon_.png\" id=\"imgonoff"+temp[0]+"\" /></a>";
					else
					//if(temp[2]==1)
					li_html=li_html+"<a onclick=\"onoff('"+temp[0]+"','"+temp[1]+"','"+temp[2]+"')\"> <img src=\"../../img/index41hwxon.png\" id=\"imgonoff"+temp[0]+"\" /></a>";
					li_html=li_html+"<br />";
					li_html=li_html+"<a href=\"kaiguan.php?sid="+temp[0]+"&nid="+temp[1]+"\" style=\"text-decoration:underline;\">"+temp[3]+"</a>";					
					
					li_html=li_html+"</div>";
					li_html=li_html+"</li>";
					//document.getElementById("device").innerText=str[0]+"-";
				}
			}    
			
			li_html=li_html+"<li>";
			li_html=li_html+"<div class=\"zycz\">";
						
			li_html=li_html+"<a href=\"../../devicename.php?mode=adddevice&type=5\" style=\"color:#999999;\"> <img src=\"../../img/index40tool.png\" />";
			li_html=li_html+"<br />";
			li_html=li_html+"添加新红外线</a>";
			
			li_html=li_html+"</div>";
			li_html=li_html+"</li>";
					
			document.getElementById("device").innerHTML=li_html;
		
			
		}else{//http_request.status != 200
			//document.getElementById("Request").innerHTML="请求失败！";
		}
	}
}
</script>
</head>

<body onLoad="makeRequest('?mode=getonoff&type=5')">

<!--顶部 <button>首页</button> -->
<table class="top" border="0" cellspacing="0" cellpadding="0">
    <colgroup>
        <col width="70px" />
        <col />
        <col width="70px" />
    </colgroup>
    <tr>
        <td><button onClick="javascript:history.back()">返回</button></td>
        <td>智能创客－红外线</td>
        <td><a href="../../index.php"><button>首页</button></a></td>
    </tr>
</table>

	<div class="zy">
        <ul id="device">
            <!--li>
                <div class="zycz">
                   <a > 
				   <img src="../../img/index41hwxon.png" />                    
					</a>
					<br />
					<a href="kaiguan.php?sid=3&nid=1" style="text-decoration:underline;"> 红外线1</a>
				</div>
				  
                <div class="zybtn">
                    <a href="math/ques/filter.html"><input name="" type="button" class="zybtn2" value="试题" /></a>
                    <a href="math/report/filter.html"><input name="" type="button" class="zybtn2" value="试卷" /></a>
                </div>				
            </li-->           
           		  
			
		   <li>				
                <div class="zycz">
					<a href='../../devicename.php?mode=adddevice&type=5' style="color:#999999;"> 
                    <img src="../../img/index40tool.png" /><br />
                    添加新红外线
					</a>
                </div>
            </li>            
        </ul>
    </div>

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