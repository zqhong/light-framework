<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/17
 * Time: 17:46
 */

namespace Core;

/**
 * 基类
 * Class Base
 * @package Application\Models
 */
abstract class Base
{
    /**
     * 保存数据的数组
     *
     * @var array
     */
    public $data = array();

    /**
     * @var PDO 实例
     */
    public static $db;

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @param $var_name
     * @param $value
     */
    public function __set($var_name, $value)
    {
        $this->data[$var_name] = $value;
    }

    /**
     * @param $var_name
     * @return null
     */
    public function __get($var_name)
    {
        $value = isset($this->data[$var_name]) ? $this->data[$var_name] : null;
        return $value;
    }
}

/**
 * AR类
 *
 * Class ActiveRecord
 * @package Application\Models
 */
abstract class ActiveRecord extends Base
{
    /**
     * 占位符前缀。比如：insert into user set uid = :ph1, name = :ph2，这里的“:ph”就是占位符前缀
     */
    const PREFIX = ":ph";

    /**
     * @var array 参数值，占位符指代的值。比如：array(":ph1" => "1", ":ph2" => "akira")。
     * 其中，SQL语句为：insert into user set uid = :ph1, name = :ph2
     */
    public $params = array();

    /**
     * @var string 表名
     */
    public $table_name;

    /**
     * @var string
     */
    public $primary_key = "id";

    /**
     * @var array sql part映射表
     */
    public $sql_parts = array(
        "select" => "SELECT",
        "from"   => "FROM",
        "set"    => "SET",
        "where" => "WHERE",
        "group" => "GROUP BY", "groupby" => "GROUP BY",
        "having" => "HAVING",
        "order" => "ORDER BY", "orderby" => "ORDER BY",
        "limit" => "LIMIT",
        "top" => "TOP",
    );

    /**
     * @var array 操作符映射表
     */
    public $operators = array(
        "equal" => "=", "eq" => "=",
        "notequal" => "<>", "ne" => "<>",
        "greaterthan" => ">", "gt" => ">",
        "lessthan" => "<", "lt" => "<",
        "greaterthanorequal" => ">=", "ge" => ">=", "gte" => ">=",
        "lessthanorequal" => "<=", "le" => "<=", "lte" => "<=",
        "between" => "BETWEEN",
        "like" => "LIKE",
        "notin" => "NOT IN",
        "isnull" => "IS NULL",
        "isnotnull" => "IS NOT NULL", "notnull" => "IS NOT NULL",
    );

    /*
     *
     */
    public $default_sql_expressions = array(
        "expressions" => array(), "wrap" => false,
        "select" => null, "insert" => null, "update" => null, "set" => null, "delete" => "DELETE",
        "from" => null, "values" => null, "where" => null, "having" => null, "limit" => null,
        "order" => null, "group" => null,
    );

    /**
     * @var array
     */
    public $sql_expressions = array();

    /**
     * @var array
     */
    public $dirty = array();

    /**
     * @var array
     */
    public $relations = array();

    /**
     * @var int
     */
    public static $count = 0;

    /**
     * @return $this
     */
    public function reset()
    {
        $this->params = array();
        $this->sql_expressions = array();
        return $this;
    }

    /**
     * @param $db
     */
    public static function setDb($db)
    {
        self::$db = $db;
    }


    /**
     * @param null $id
     * @param bool|false $raw_query 当$raw_query为true时，不执行sql语句，而只是返回原始的sql语句。
     */
    public function find($id = null, $raw_query = false)
    {
        if ($id) {
            $this->reset()->eq($this->primary_key, $id);
        }

        $sql = $this->limit(1)->_buildSql(array("select", "from", "where", "group", "having", "order", "limit"));

        if ($raw_query) {
            return $sql;
        }
        return $this->query($sql, $this->params, $this->reset());
    }

    /**
     * @return string
     */
    public function findAll()
    {
        return $this->_buildSql(array("select", "from", "where", "group", "having", "order", "limit"),
                                            $this->params, $this->reset());
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $sql = $this->eq($this->primary_key, $this->{$this->primary_key})
                    ->_buildSql(array("delete", "from", "where"));
        return $this->execute($sql, $this->params);
    }

    /**
     * @return $this|bool
     */
    public function update()
    {
        if (count($this->dirty) == 0) {
            return true;
        }

        foreach ($this->dirty as $field => $value) {
            $this->addCondition($field, "=", $value, ",", "set");
        }

        $sql = $this->eq($this->primary_key, $this->{$this->primary_key})
                    ->_buildSql(array("update", "set", "where"));

        if ($this->execute($sql, $this->params)) {
            return $this->dirty()->reset();
        }

        return false;
    }

    public function insert() {
        if (count($this->dirty) == 0) {
            return true;
        }
        $value = $this->_filterParam($this->dirty);
        $this->insert = new Expressions(array('operator'=> 'INSERT INTO '. $this->table,
            'target' => new WrapExpressions(array('target' => array_keys($this->dirty)))));
        $this->values = new Expressions(array('operator'=> 'VALUES', 'target' => new WrapExpressions(array('target' => $value))));
        if (self::execute($this->_buildSql(array('insert', 'values')), $this->params)) {
            $this->id = self::$db->lastInsertId();
            return $this->dirty()->reset();
        }
        return false;
    }

    /**
     * @param $sql
     * @param array $param
     * @return mixed
     */
    public function execute($sql, $param = array())
    {
        $sth = self::$db->prepare($sql);
        return $sth->execute($param);
    }

    /**
     * @param array $dirty
     * @return $this
     */
    public function dirty($dirty = array())
    {
        $this->data = array_merge($this->data, $this->dirty = $dirty);
        return $this;
    }

    /**
     * @param $sql
     * @param array $param
     * @param null $obj
     * @return bool
     */
    public function query($sql, $param = array(), $obj = null)
    {
        if ($sth = self::$db->prepare($sql)) {
            $sth->execute($param);
            $sth->setFetchMode(\PDO::FETCH_INTO , ($obj ? $obj : new get_called_class()));
            $sth->fetch();
            return $obj->dirty();
        }

        return false;
    }

    /**
     * @param $sql
     * @param array $param
     * @param null $obj
     * @return array|bool
     */
    public function queryAll($sql, $param = array(), $obj = null)
    {
        if ($sth = self::$db->prepare($sql)) {
            $sth->execute($param);
            $sth->setFetchMode(\PDO::FETCH_INTO , ($obj ? $obj : new get_called_class()));
            while ($obj = $sth->fetch()) {
                $result[] = clone $obj->dirty();
            }
            return $result;
        }

        return false;
    }

    /**
     * @param $value
     * @return array|string
     */
    protected function _filterParam($value) {
        if (is_array($value)) {
            foreach($value as $key => $val) {
                $this->params[$value[$key] = self::PREFIX. ++self::$count] = $val;
            }
        } else if (is_string($value)){
            $this->params[$ph = self::PREFIX. ++self::$count] = $value;
            $value = $ph;
        }
        return $value;
    }

    /**
     * @param $n string 将被构建的 sql part。比如：select、from、where、group等。
     * @param $i int $i 是 $sqls 数组的索引
     * @param $o ActiveRecord 的实例，也就是 $this
     */
    private function _buildSqlCallback(&$n, $i, $o)
    {
        if ("select" === $n && null == $o->$n) {
            $n = strtoupper($n) . " " . $o->table_name . ".*";
        } else if (("update" === $n || "from" === $n) && null == $o->$n) {
            $n = strtoupper($n) . " " . $o->table_name;
        } else {
            $n = (null !== $o->$n) ? $o->$n . " " : "";
        }
    }

    /**
     * @param array $sqls
     * @return string
     */
    protected function _buildSql($sqls = array())
    {
        array_walk($sqls, array($this, "_buildSqlCallback"), $this);
        return implode(" ", $sqls);
    }


    /**
     * 在 where 之后追加条件
     *
     * @param $field string 操作字段名
     * @param $operator string 操作符
     * @param $value string 操作值
     * @param string $op
     * @param string $name
     */
    public function addCondition($field, $operator, $value, $op = "AND", $name = "where")
    {
        $exp = new Expressions(array(
            "source" => ("where" == $name ? $this->table_name . "." : "") . $field,
            "operator" => $operator,
            "target" => (is_array($value) ? new WrapExpressions(array("target" => $value)) : $value),
        ));

        if ($exp && !$this->wrap) {
            $this->_addCondition($exp, $op, $name);
        } else {
            $this->_addExpression($exp, $op);
        }
    }

    /**
     * @param $exp
     * @param $operator
     */
    protected function _addExpression($exp, $operator) {
        if (!is_array($this->expressions) || count($this->expressions) == 0)
            $this->expressions = array($exp);
        else
            $this->expressions[] = new Expressions(array('operator'=>$operator, 'target'=>$exp));
    }

    /**
     * @param $exp
     * @param $operator
     * @param string $name
     */
    protected function _addCondition($exp, $operator, $name ='where' ) {
        if (!$this->$name)
            $this->$name = new Expressions(array('operator'=>strtoupper($name) , 'target'=>$exp));
        else
            $this->$name->target = new Expressions(array('source'=>$this->$name->target, 'operator'=>$operator, 'target'=>$exp));
    }

    /**
     * @param $method
     * @param $args
     */
    public function __call($method, $args)
    {
        if (in_array($name = str_replace("by", "", $method), array_keys($this->sql_parts))) {
            if ($this->sql_parts[$name] == "SELECT") {
                foreach ($args as $k => $v) {
                    $args[$k] = $this->table_name . "." . $v;
                }
            }
            $this->$name = new Expressions(array("operator" => $this->sql_parts[$name], "target" => implode(", ", $args)));
        }

        if (in_array($name = strtolower($method), array_keys($this->operators))) {
            $filed = $args[0];                                                                // 操作字段
            $operator = $this->operators[$name];                                            // 操作符，如：=
            $value = isset($args[1]) ? $args[1] : null;                                     // 操作值
            $op = is_string(end($args)) && "or" === strtolower(end($args)) ? "OR" : "AND"; // 逻辑运算符，如：OR 或 AND
            $this->addCondition($filed, $operator, $value, $op);
        }

        return $this;
    }

    /**
     * @param $var
     * @param $val
     */
    public function __set($var, $val)
    {
        if (array_key_exists($var, $this->sql_expressions) || array_key_exists($var, $this->default_sql_expressions)) {
            $this->sql_expressions[$var] = $val;
        } else {
            $this->dirty[$var] = $this->data[$var] = $val;
        }
    }

    /**
     * @param $var
     */
    public function __unset($var)
    {
        if (array_key_exists($var, $this->sql_expressions)) {
            unset($this->sql_expressions[$var]);
        }

        if (isset($this->data[$var])) {
            unset($this->data[$var]);
        }

        if (isset($this->dirty[$var])) {
            unset($this->dirty[$var]);
        }
    }

    /**
     * @param $var
     * @return null
     */
    public function __get($var)
    {
        if (array_key_exists($var, $this->sql_expressions)) {
            return $this->sql_expressions[$var];
        } else if (array_key_exists($var, $this->relations)) {
        } else {
            return parent::__get($var);
        }
    }

}


/**
 * Class Expressions
 * @package Application\Models
 */
class Expressions extends Base
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->source . " " . $this->operator . " " . $this->target;
    }
}


/**
 * Class WrapExpressions
 * @package Application\Models
 */
class WrapExpressions extends Base
{
    public function __toString()
    {
        $delimiter = $this->delimiter ? $this->delimiter : ",";
        $start = $this->start ? $this->start : "(";
        $target = implode($delimiter, $this->target);
        $end = $this->end ? $this->end : ")";

        return $start . $target . $end;
    }
}