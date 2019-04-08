
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>智能创客－WIFI摄像头</title>
<meta name="keywords" content="智能创客－WIFI摄像头" />
<meta name="description" content="智能创客－WIFI摄像头" />
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
        <td>智能创客－WIFI摄像头</td>
        <td><a href="../../index.php"></a></td>
    </tr>
</table>


<?php 
//header("Content-Type: text/html; charset=utf-8");
require_once('../../ckconn.php');


$uid=$_SESSION['uid'];
if( $uid=="" ) $uid=$_COOKIE['uid'];
if( $uid=="" ){
	header("Location:../../login.php");
	exit;
}

?>


<div class="sxst">    
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
			
		<tr>
            <td width="100%" align="center" >
				功能介绍：
                <label style="color:#00AA00">                   
				   WIFI摄像头
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >
				网页版：
                <label style="color:#00CC00">                   
				   http://192.168.1.1:8080
                </label>
            </td>
        </tr>
		
		<tr>
            <td width="100%" align="center" >				
                <label style="color:#FF9900"> 
				<a href="http://192.168.1.1:8080" target="_blank">                   
				   点击查看摄像头
				</a>
                </label>				
            </td>
        </tr>		
       
	   <tr>
            <td width="100%" align="center" >
                <label>                   
				  	提示：如果更改了IP请直接在浏览器输入IP:端口					
                </label> 
            </td>
        </tr>
	   
    </table>
</div>	


<div class="clear2"></div>
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='http://pan.baidu.com/s/1o6sWMtO'" >下载安卓APK客户端</button></div><BR>
<div style="padding: 0 8px"><button style="width: 100%" class="button3" onClick="javascript:window.location.href='http://www.znck007.com/forum.php?mod=viewthread&tid=2448'" >WIFI摄像头DIY教程</button></div> 

<div data-role="footer" style="text-align:center;">    
</div>
</body>
</html>
