<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/22
 * Time: 10:34
 */

namespace Application\Models;


/**
 * Class BaseModel
 * @package Application\Models
 */
class BaseModel extends \Core\ActiveRecord
{
    public function __construct()
    {
        // 设置表名为当前类名（Model前的所有字符）
        $class_name = get_class($this);
        $index = strrpos($class_name, "\\");

        if ($index !== false) {
            $class_name = substr($class_name, $index+1);
        }
        $this->table_name = strtolower(str_replace("Model", "", $class_name));
    }
}