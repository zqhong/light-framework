<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/22
 * Time: 10:35
 */

$dbconfig = array(
    "dbtype"   => "mysql",
    "host"     => "localhost",
    "port"     => "3306",
    "dbname"   => "test",
    "user"      => "root",
    "password" => "",
);

$db = new PDO(getDsn($dbconfig), $dbconfig['user'], $dbconfig['password']);
\Application\Models\BaseModel::setDb($db);
