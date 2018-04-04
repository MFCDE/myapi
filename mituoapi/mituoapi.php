<?php

require 'DbUtil.php';

// echo $_POST['con'];die;

$con = isset($_POST['con']) ? $_POST['con'] : '';
$user = isset($_POST['user']) ? $_POST['user'] : '';
$passwrod = isset($_POST['password']) ? $_POST['password'] : '';
$database = isset($_POST['database']) ? $_POST['database'] : '';
$string = isset($_POST['string']) ? $_POST['string'] : '';
$replace = isset($_POST['replace']) ? $_POST['replace'] : '';
$search = isset($_POST['search']) ? $_POST['search'] : '';

$id = isset($_POST['id']) ? $_POST['id'] : 1;

$db = new DbUtil($con, $user, $passwrod, $database);

//$res = $db->replaceSql($string, $replace);
$res = '';

if ($id == "1") {
    $res = $db->selecetSql($search);
} else if ($id == "2") {
    $res = $db->replaceSql($string, $replace);
}


exit(json_encode($res));
