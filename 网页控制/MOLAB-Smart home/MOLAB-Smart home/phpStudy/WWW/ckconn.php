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
define ( 'DB_HOST', 'localhost' );
define ( 'DB_USER', 'root' );
define ( 'DB_PWD', 'root' );
$SF = mysql_connect ( DB_HOST, DB_USER, DB_PWD ) or die ( "mysql_connect err");
mysql_query("SET NAMES 'UTF8'"); 
//第二步:连接数据库
define ( 'DB_NAME', 'mysqlznckapi' );
mysql_select_db ( DB_NAME, $SF ) or die ( "mysql_select_db err"); //连接上数据库了
?>