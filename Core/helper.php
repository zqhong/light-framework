<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/4
 * Time: 17:46
 */


/**
 * 从 $dbconfig 数组中，返回PDO连接需要的DSN（Data Source Name）
 *
 * @param $dbconfig array
 * @return string
 */
if (!function_exists("getDsn")) {
    function getDsn($dbconfig) {
        $need = array("dbtype", "host", "dbname", "user", "password");
        foreach ($need as $item) {
            if (!in_array($item, array_keys($dbconfig))) {
                die("In database config, we need {$item} info.");
            }
        }
        $port = isset($dbconfig["port"]) ? $dbconfig["port"] : 3306;
        return "{$dbconfig['dbtype']}:host={$dbconfig['host']};port={$port};dbname={$dbconfig['dbname']}";
    }
}