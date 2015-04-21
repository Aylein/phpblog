<?php
class Entity {
    var $conn;

    public function __construct(){
        try{
            $this->conn = new PDO("mysql:host=localhost;dbname=phpMyBlog;charset=utf8", "section", "mm19880209");
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

    public function Query($query, $paras = null){
        if(!$this->EntityState()) return null;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($qu->execute($paras))
            return $qu->fetchAll(); //PDO::FETCH_NAMED
        return null;
    }

    public function Querys($query, $paras = null){
        if(!$this->EntityState()) return null; //判断状态
        if($paras && !is_array($paras)) $paras = null; //检查参数数组
        $qu = $this->conn->prepare($query); //prepare
        if($qu->execute($paras)){
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
        if($qu->execute($paras)) return $qu->fetch();
        return null;
    }

    public function Exec($query, $paras = null){
        try{
            if(!$this->EntityState()) return null;
            $this->conn->beginTransaction();
            if($paras && !is_array($paras)) $paras = null;
            $cn = $this->conn->prepare($query)->execute($paras);
            $this->conn->commit();
            return $cn;
        } catch(Exception $e){
            $this->conn->rollback();
            return null;
        }
    }

    public function Scalar($query, $paras = null){
        if(!$this->EntityState()) return null;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($qu->execute($paras)) return $qu->fetch()[0];
        return null;
    }
}
?>