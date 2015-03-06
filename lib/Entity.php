<?php
class Entity {
    var $conn;

    public function __construct(){
        try{
            $this->conn = new PDO("mysql:host=localhost;dbname=phpMyBlog", "section", "mm19880209");
        } catch (Exception $e) {
            //exit(); //die("数据库连接失败");
            return;
        }
    }

    public function EntityState(){
        return $this->conn;
    }

    public function Query($query, $paras = null){
        if(!$this->EntityState()) return false;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($qu->execute($paras)) return $qu->fetchAll();
        return false;
    }

    public function First($query, $paras = null){
        if(!$this->EntityState()) return false;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($qu->execute($paras)) return $qu->fetch();
        return false;
    }

    public function Exec($query, $paras = null){
        try{
            if(!$this->EntityState()) return false;
            $this->conn->beginTransaction();
            if($paras && !is_array($paras)) $paras = null;
            $cn = $this->conn->prepare($query)->execute($paras);
            $this->conn->commit();
            return $cn;
        } catch(Exception $e){
            $this->conn->rollback();
            return false;
        }
    }

    public function Scalar($query, $paras = null){
        if(!$this->EntityState()) return false;
        if($paras && !is_array($paras)) $paras = null;
        $qu = $this->conn->prepare($query);
        if($qu->execute($paras)) return $qu->fetch()[0];
        return false;
    }
}
?>