<?php

require 'DbUtil.php';

// echo $_POST['con'];die;

$con = isset($_POST['con']) ? $_POST['con'] : 'wk.php';
$user = isset($_POST['user']) ? $_POST['user'] : 'root';
$passwrod = isset($_POST['passwrod']) ? $_POST['passwrod'] : 'root';
$database = isset($_POST['database']) ? $_POST['database'] : 'db_text1';
$string = isset($_POST['string']) ? $_POST['string'] : '';
$replace = isset($_POST['replace']) ? $_POST['replace'] : '';

$db = new DbUtil($con, $user, $passwrod, $database);

$res = $db->replaceSql($string, $replace);

exit(json_encode($res));
