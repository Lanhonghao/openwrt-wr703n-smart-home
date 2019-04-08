<?php
//echo "start";
$keyword = array("'",'"',' and ',' or ','update ','delete ','into ');//,' '
$input = array(&$_GET,&$_POST);//,&$_COOKIE
foreach($input as $k){
 foreach($k as $name=>$value){
  foreach($keyword as $key){
   if(strpos($value, $key)==true){
    $fp=fopen('sqlin.txt','a');
    fputs($fp,date("Y-m-d H:i:s")."\t$_SERVER[REMOTE_ADDR]\t$_SERVER[SCRIPT_NAME]\t$value\r\n");
    fclose($fp);
    die("get_post_err");
   }
  }
 }
}

error_reporting(E_ALL & ~E_NOTICE);

//设置头信息
//header("Content-Type: text/html; charset=GB2312");//设置头信息
//mysql_select_db选择 MySQL 数据库
//第一步：
$SF = mysql_connect ( "localhost", "root", "root" ) or die ( "mysql_connect err");
mysql_query("SET NAMES 'UTF8'"); 
//第二步:连接数据库
mysql_select_db ( "mysqlznckapi", $SF ) or die ( "mysql_select_db err"); //连接上数据库了
?>