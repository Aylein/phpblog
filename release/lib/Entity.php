<?php
class Entity {
    var $conn;

    public function __construct(){
        $host = defined("SAE_MYSQL_HOST_M") ? SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT : "localhost";
        $name = defined("SAE_MYSQL_USER") ? SAE_MYSQL_USER : "section";
        $pass = defined("SAE_MYSQL_PASS") ? SAE_MYSQL_PASS : "mm19880209";
        try{
            $this->conn = new PDO("mysql:host=".$host.";dbname=app_aylein;charset=utf8", $name, $pass);
            //$this->conn->setAttribute(PDO::ATTR_PERSISTENT, true); //mysql 长链接
            //$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //关闭php注入模拟 使用mysql参数注入 //加上报错
        } catch (Exception $e) {
            //exit(); //die("数据库连接失败");
            return $this->conn = null;
        }
    }

    public function EntityState(){
        return $this->conn;
    }
    
    //解决 limit 无法绑定参数的错误
    //http://blog.csdn.net/jinbiao520/article/details/7469264@站住借个吻
    private function getType($val) {
        if (is_bool($val)) return PDO::PARAM_BOOL;
        else if (is_int($val)) return PDO::PARAM_INT;
        else if (is_null($val)) return PDO::PARAM_NULL;
        else return PDO::PARAM_STR;
    }
    
    private function bindParam($qu, $para){
        foreach($para as $key => $value)
            $qu->bindValue($key, $value, $this->getType($value));
    }

    public function Query($query, $paras = null){
        if(!$this->EntityState()) return null;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($paras) $this->bindParam($qu, $paras);
        if($qu->execute())
            return $qu->fetchAll(); //PDO::FETCH_NAMED
        return null;
    }

    public function Querys($query, $paras = null){
        if(!$this->EntityState()) return null; //判断状态
        if($paras && !is_array($paras)) $paras = null; //检查参数数组
        $qu = $this->conn->prepare($query); //prepare
        if($paras) $this->bindParam($qu, $paras);
        if($qu->execute()){
            $res = Array();
            $i = 0;
            do $res[] = $qu->fetchAll(); //PDO::FETCH_NAMED
            while($qu->nextRowset());
            return $res;
        } //excute and retruen
        return null;
    }

    public function First($query, $paras = null){
        if(!$this->EntityState()) return null;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($paras) $this->bindParam($qu, $paras);
        if($qu->execute()) return $qu->fetch();
        return null;
    }

    public function Exec($query, $paras = null){
        try{
            if(!$this->EntityState()) return null;
            $this->conn->beginTransaction();
            if($paras && !is_array($paras)) $paras = null;
            $qu = $this->conn->prepare($query);
            if($paras) $this->bindParam($qu, $paras);
            $cn = execute();
            $this->conn->commit();
            return $cn;
        }catch(Exception $e){
            $this->conn->rollback();
            return null;
        }
    }

    public function Scalar($query, $paras = null){
        if(!$this->EntityState()) return null;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($paras) $this->bindParam($qu, $paras);
        if($qu->execute()) return $qu->fetch()[0];
        return null;
    }
}
?>