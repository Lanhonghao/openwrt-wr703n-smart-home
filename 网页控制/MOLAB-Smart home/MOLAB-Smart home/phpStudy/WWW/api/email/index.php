<?php 
header("Content-Type: text/html; charset=utf-8");
session_start();

$alarmvalue = urldecode($_GET['alarmvalue']);
if( $alarmvalue=="" ) $alarmvalue = urldecode($_POST['alarmvalue']);
$alarmemail = urldecode($_GET['alarmemail']);
if( $alarmemail=="" ) $alarmemail = urldecode($_POST['alarmemail']);
$alarmcontent = urldecode($_GET['alarmcontent']);
if( $alarmcontent=="" ) $alarmcontent = urldecode($_POST['alarmcontent']);

if( $alarmemail!="")
{
		if(time() - $_SESSION['refresh']<60){
			echo (time() - $_SESSION['refresh']);
			exit;
		} 
		$_SESSION['refresh'] = time();
		
		$sto=$alarmemail;
		$sfrom="智能创客<alarm@znck007.com>";
		$stitle=preg_replace("(<.*?>)", "", $alarmcontent).'《智能创客》';//<img src="'.$hostking.'/img/preview1.png" />
		
		$sneirong=$alarmcontent;//.'<BR><BR>本邮件是系统发送，请不要回复哦。'; 
				
		include("smail.php"); 
		// 只需要把这部分改成你的信息就行
		$sm = new smail( "alarm@znck007.com", "*********", "smtp.exmail.qq.com" );
		$end = $sm->send( $sto, $sfrom, $stitle, $sneirong );		
		if( $end ) echo $end; 
	
}	
		
		
?>
