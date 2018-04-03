<?php

/**
 * 替换数据库中字段某数据
 */
class DbUtil
{
    private $con;
    private $database;
    private $user;
    private $password;
    private $mysqli;

    public function __construct($con, $user, $password,$database)
    {
        $this->con = $con;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;

        $this->mysqli = new mysqli($this->con, $this->user, $this->password, $this->database);
        !$this->mysqli->connect_error or die("链接失败" . $mysqli->connect_error);

        $this->mysqli->query("set names utf-8");
    }

    /**
     * 替换数据库中所有表所有字段中字符串方法
     *
     * @param [type] $string 要替换的字符串
     * @param [type] $replace 替换的字符串
     * @return void
     */
    public function replaceSql($string, $replace)
    {
        $sql = "select table_name
			from information_schema.tables
			where table_schema='{$this->database}'";

        $res = $this->mysqli->query($sql);
        $tableName = [];

        foreach ($res as $k => $v) {
            foreach ($v as $kk => $vv) {
                $tableName[] = $vv;
            }
        }

        // print_r($tableName);

        foreach ($tableName as $k => $v) {
            $sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '{$v}' and table_schema = '{$this->database}'";

            $columns = $this->mysqli->query($sql);
            // var_dump($columns['current_field']);
            foreach ($columns as $kk => $vv) {
                $sql = "UPDATE {$v} SET {$vv['COLUMN_NAME']} = replace({$vv['COLUMN_NAME']}, '{$string}', '{$replace}')";
                // echo $sql;
                $res = $this->mysqli->query($sql);
                if (!$res) {
                    // die($this->mysqli->error);
                    return false;
                }
            }
        }

        // echo "替换成功";
        return true;
    }
}
