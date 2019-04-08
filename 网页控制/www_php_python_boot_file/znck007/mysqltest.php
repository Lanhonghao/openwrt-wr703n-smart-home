<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>PHP操作MYSQL-智能创客</title>
<meta name="keywords" content="PHP操作MYSQL-智能创客" />
<meta name="description" content="PHP操作MYSQL-智能创客" />
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
        <td>PHP操作MYSQL-智能创客</td>
        <td></td>
    </tr>
</table>


<?php
//header("Content-Type: text/html; charset=utf-8");
require_once('conn.php');//连接数据库


$sid="001";//模块类型
$nid="001";//模块编号



if( $_GET['mode']=="select")//获取数据库记录
{	
			
		$sql="select * from api_worklist WHERE sid='".$sid."' and nid='".$nid."' order by time desc limit 0,1";//最后1条
		$result = mysql_query($sql);//执行sql语句
		if (!$result)
		{
		  die('Error: ' . mysql_error());//如果出错则显示错误	
		}
		$num=mysql_num_rows($result);//统计行数
		while($row = mysql_fetch_array($result)) //获取各个字段的值
		{
			$id=$row['id'];
			$sid=sprintf("%03d",$row['sid']);
			$nid=sprintf("%03d",$row['nid']);
			$data=$row['data'];
			$note=$row['note'];
			$status=$row['status'];				
			$time=$row['time'];
			
			//echo "sid：".$sid." nid：".$nid." data：".$data." note：".$note." status：".$status." time：".$time."<BR>";			
		}		
			
}


if( $_GET['mode']=="insert") //插入数据
{
	//$sid=sprintf("%03d",$row['sid']);
	$type=1;//1网设2上传3定时
	$uid=0;
	$sid=$_POST['sid'];
	$nid=$_POST['nid'];
	$data=$_POST['data'];
	$note=$_POST['note'];
	$status=$_POST['status'];
	
	$nowt=time();//当前时间 60*60是一个小时 
	$time=date("Y-m-d H:i:s",$nowt);
	
	$ip=$_SERVER["REMOTE_ADDR"];//$_SERVER["REMOTE_ADDR"];//记录IP

	
	//插入
	$sql="INSERT INTO api_worklist(type,uid,sid,nid,data,note,status,time,ip) 
	VALUES ('".$type."', '".$uid."', '".$sid."', '".$nid."', '".$data."', '".$note."', '".$status."', '".$time."', '".$ip."')";	
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());//如果出错则显示错误		
	}
	
	
	echo "恭喜，添加记录成功！<a href='?mode=select'>点击返回</a>";
	mysql_close();
	exit;
}


if( $_GET['mode']=="update") //更新数据
{
	//$sid=sprintf("%03d",$row['sid']);
	$id=$_POST['id'];
	$type=1;//1网设2上传3定时
	$uid=0;
	$sid=$_POST['sid'];
	$nid=$_POST['nid'];
	$data=$_POST['data'];
	$note=$_POST['note'];
	$status=$_POST['status'];
	
	$nowt=time();//当前时间 60*60是一个小时 
	$time=date("Y-m-d H:i:s",$nowt);
	
	$ip=$_SERVER["REMOTE_ADDR"];//$_SERVER["REMOTE_ADDR"];//记录IP
		
	//更新设置最后时间
	$nowt=time();//当前时间 60*60是一个小时 
	$time=date("Y-m-d H:i:s",$nowt);	
		
	$sql="UPDATE api_worklist SET data='".$data."',note='".$note."',status='".$status."',time='".$time."',ip='".$ip."' WHERE id='".$id."' and sid='".$sid."' and nid='".$nid."'";//更新表记录的sql语句
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());//如果出错则显示错误
	}
		
	echo "恭喜，更新数据成功！<a href='?mode=select'>点击返回</a>";
	mysql_close();
	exit;
}


if( $_GET['mode']=="delete") //删除数据
{
	//$sid=sprintf("%03d",$row['sid']);
	$id=$_POST['id'];
	
	$sql="DELETE FROM api_worklist WHERE id='".$id."' and sid='".$sid."' and nid='".$nid."'";//删除表记录的sql语句
	if (!mysql_query($sql))
	{
		die('Error: ' . mysql_error());
	}
		
	echo "恭喜，删除记录成功！<a href='?mode=select'>点击返回</a>";//<table width='100%' align='center'></table>
	mysql_close();
	exit;
}

?>



<?php 
if( $_GET['mode']!="list")//默认显示界面
{	
?>
  
	
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="search_btn"> 
		<colgroup><col/><col width="5px" /><col width="32px" /></colgroup>
    <tbody> 
	
	<tr>		
		<td colspan=3 align="center">
		<form name="dbform" action="?mode=" method="post" onSubmit="return CheckSearch();">
				<input maxlength="32" type="hidden" name="id" value="<?php echo $id;?>"  /><BR>
		模块类型:<input maxlength="32" type="text" name="sid" value="<?php echo $sid;?>" placeholder="模块的类型..." /><BR>
		模块编号:<input maxlength="32" type="text" name="nid" value="<?php echo $nid;?>" placeholder="模块的编号..." /><BR>
		
		操作数据:<input maxlength="32" type="text" name="data" value="<?php echo $data;?>" placeholder="具体操作的数据..." /><BR>
		详细说明:<input maxlength="32" type="text" name="note" value="<?php echo $note;?>" placeholder="详细说明..." /><BR>
		当前状态:<input maxlength="32" type="text" name="status" value="<?php echo $status;?>" placeholder="当前状态..." /><BR>
		更新时间:<input maxlength="32" type="text" name="time" value="<?php echo $time;?>" placeholder="最后更新时间..."  readonly="trun"/><BR>
		<button type="submit" onClick="javascript:document.dbform.action='?mode=insert'">添加</button>&nbsp;&nbsp;
		<button type="button" onClick="javascript:window.location.href='?mode=select'">读取</button>&nbsp;&nbsp;
		<button type="submit" onClick="javascript:document.dbform.action='?mode=update'">修改</button>&nbsp;&nbsp;        
        <button type="submit" onClick="javascript:document.dbform.action='?mode=delete'">删除</button>
		</form>
		</td>
	</tr>
		
	
    </tbody>
		   
</table>

<?php 
}//结束 默认显示界面
?>



<?php 
if( $_GET['mode']=="list")//显示数据库所有记录
{		
?>

 <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" width="100%" style="color:#00AA00">
			<?php 				
				$sql="select * from api_worklist order by id desc limit 0,100";//前100条数据  WHERE sid='".$sid."' and nid='".$nid."'
				$result = mysql_query($sql);
				if (!$result)
				{
				  die('Error: ' . mysql_error());				
				}
				$count=mysql_num_rows($result);
				while($row = mysql_fetch_array($result)) 
				{
					$id=$row['id'];
					$type=$row['type'];
					$sid=sprintf("%03d",$row['sid']);
					$nid=sprintf("%03d",$row['nid']);
					$data=$row['data'];
					$note=$row['note'];
					$status=$row['status'];				
					$time=$row['time'];
					$ip=$row['ip'];
					$num=$row['num'];
					
					echo "id=".$id."/type=".$type."/sid=".$sid."/nid=".$nid."/data=".$data."/note=".$note."/status=".$status."/time=".$time."/ip=".$ip."/num=".$num."<BR>";			
				}
				if($count==0){
					echo "对不起，当前数据库没有数据！<BR>";
				}
			?>
			</td>            
		</tr>
</table>
	
<?php 
}//结束 显示数据库所有记录
?>

<div class="clear2"></div>
<div style="padding: 0 8px"><button style="width: 100%" class="button2" onClick="javascript:window.location.href='?mode=list'" >查看数据库记录</button></div><BR>

<!--底部--> 
<div class="clear2"></div>
<div data-role="footer" style="text-align:center;">   
   <p style="color:#B4B4B4"><a href="http://www.znck007.com" target="_blank" style="text-decoration:none;color:#B4B4B4">论坛</a>&nbsp;|&nbsp;<a href="http://weibo.cn/znck007" target="_blank" style="color:#B4B4B4;">微博</a>&nbsp;|&nbsp;<a href="http://www.znck007.com/wap/img/qrcode_for_gh_9be0babbcc4b_258.jpg" target="_blank" style="color:#B4B4B4;">微信znck007</a></p>
   <p style="color:#B4B4B4;margin-top:5px">@智能创客 每周手把手教您DIY智能产品</p>
</div>

</body>
</html>

<?php 
mysql_close();//记得最后要关闭数据库
?>