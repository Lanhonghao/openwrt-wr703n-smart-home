<?php
//mysql_select_db选择 MySQL 数据库
//第一步：
define ( 'DB_HOST', '192.168.1.1' );//localhost
define ( 'DB_USER', 'root' );
define ( 'DB_PWD', 'znck007' );
$SF = mysql_connect ( DB_HOST, DB_USER, DB_PWD ) or die ( "mysql_connect err");
mysql_query("SET NAMES 'UTF8'"); 
//第二步:连接数据库
define ( 'DB_NAME', 'znckapi' );
mysql_select_db ( DB_NAME, $SF ) or die ( "mysql_select_db err"); //连接上数据库了
//echo "连接上数据库了";//正式使用请注释这句，这句只是为了检测是否连接数据库成功！
?>