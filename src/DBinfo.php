<?php
$servername='localhost';
$username='root';
$password='';
$dbname = "email_imap";
$DB_Connect = new mysqli($servername,$username,$password,"$dbname");
if ($DB_Connect->connect_error) {
   die("連線失敗: " . $DB_Connect->connect_error);
}
date_default_timezone_set("Asia/Taipei");