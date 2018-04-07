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

    public function __construct($con, $user, $password, $database)
    {
        $this->con = $con;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        $this->mysqli = new MYSQLI($this->con, $this->user, $this->password, $this->database);
        !$this->mysqli->connect_error or die("链接失败" . $this->mysqli->connect_error);

        $this->mysqli->query("set names utf8");
    }

    /**
     * 执行sql语句
     */
    public function execute($sql)
    {
        $res = $this->mysqli->query($sql);

        if ($res !== false) {
            return $this->show(1, '执行成功');
        }

        return $this->show(0, $this->mysqli->error);
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
        if (!$res) {
            return $this->show(0, '替换失败' . $this->mysqli->error);
        }

        $tableName = [];

        foreach ($res as $k => $v) {
            foreach ($v as $kk => $vv) {
                $tableName[] = $vv;
            }
        }

        // print_r($tableName);
        foreach ($tableName as $k => $v) {
            $sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '{$v}' and table_schema = '{$this->database}'";

            $colres = $this->mysqli->query($sql);
            $columns = $colres->fetch_all();
            // var_dump($columns['current_field']);
            foreach ($columns as $kk => $vv) {
                if ($vv[0] == 'title' || $vv[0] == 'content' || $vv[0] == 'name') {
                    $sql = "UPDATE {$v} SET {$vv[0]} = replace({$vv[0]}, '{$string}', '{$replace}')";
                    // echo $sql;
                    $res = $this->mysqli->query($sql);
                    if ($res === false) {
                        // die($this->mysqli->error);
                        return $this->show(0, '替换失败' . $this->mysqli->error);
                    }
                }
            }
        }

        // echo "替换成功";
        return $this->show(2, '替换成功');
    }

    /**
     * 查询匹配的字符串
     */
    public function selecetSql($str)
    {
        $sql = "select table_name
			from information_schema.tables
			where table_schema='{$this->database}'";

        $res = $this->mysqli->query($sql);
        if (!$res) {
            return $this->show(0, '查询失败' . $this->mysqli->error);
        }

        $tableName = [];

        foreach ($res as $k => $v) {
            foreach ($v as $kk => $vv) {
                $tableName[] = $vv;
            }
        }

//      print_r($tableName);
        $arr = [];
        foreach ($tableName as $k => $v) {
            $sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '{$v}' and table_schema = '{$this->database}'";

            $columns = $this->mysqli->query($sql);
            $colres = $columns->fetch_all();
            // var_dump($columns['current_field']);
            foreach ($colres as $kk => $vv) {
//                if ($vv['COLUMN_NAME'] == 'title' || $vv['COLUMN_NAME'] == 'content' || $vv['COLUMN_NAME'] == 'name') {
                if ($vv[0] == 'title' || $vv[0] == 'content' || $vv[0] == 'name') {
//                    $sql = "SELECT {$vv['COLUMN_NAME']} FROM {$v} where {$vv['COLUMN_NAME']} LIKE '%{$str}%'";
                    $sql = "SELECT {$vv[0]} FROM {$v} where {$vv[0]} LIKE '%{$str}%'";
                    $res = $this->mysqli->query($sql);
                    $data = $res->fetch_all();

                    foreach ($data as $kkk => $vvv) {
                        $pos = mb_strpos($vvv[0], $str, 0, 'utf-8');
                        $pos = $pos > 10 ? $pos - 10 : 0;
//                        $temp = $this->msubstr($vvv[0], $pos, 10);
                        $temp = mb_substr($vvv[0], $pos, 30, 'utf-8');
//                      $arr[] = $vvv[0];
                        $arr[] = $temp;
                    }
                }

//                $sql = "UPDATE {$v} SET {$vv['COLUMN_NAME']} = replace({$vv['COLUMN_NAME']}, '{$string}', '{$replace}')";
//                $sql = "SELECT {$vv['COLUMN_NAME']} FROM {$v} where {$vv['COLUMN_NAME']} LIKE '%{$str}%'";
////                echo $sql;
//                $res = $this->mysqli->query($sql);
//                echo $sql . "\n";
//                var_dump($res);

//                $res->fetch_all();
//                var_dump($data);
//                if ($data) {
//                    foreach ($data as $kkk => $vvv) {
////                        var_dump($vvv);
//                    }
//                }
            }
        }

        return $this->show(1, '查询成功', $arr);

        // echo "替换成功";
//        return $this->show(1, '替换成功');
    }

    /**
     * 处理返回信息
     */
    public function show($status, $message, $data = [])
    {
        return ['status' => $status, 'message' => $message, 'data' => $data];
    }
}
