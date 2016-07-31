<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_testdb = "localhost";
$database_testdb = "traveller";
$username_testdb = "root";
$password_testdb = "";
$testdb = mysql_pconnect($hostname_testdb, $username_testdb, $password_testdb) or trigger_error(mysql_error(),E_USER_ERROR); 
?>