<?php
include_once("Entity.php");
include_once("Type.php");
class Document{
    var $docid; // int primary key auto_increment,
    var $typeid; // int references Types(typeid) on delete cascade on update cascade,
    var $docpid; // int default 0,
    var $doctitle; // nvarchar(15) not null,
    var $docsubtitle; // nvarchar(50) default "",
    var $docstgnum; // int default 0,
    var $doccomnum; // int default 0,
    var $docview; // int default 0,
    var $doccreatetime; // timestamp default current_timestamp,
    var $docupdatetime; // timestamp default current_timestamp on update current_timestamp,
    var $docsort; // int default 0,
    var $docvalid; // int default 1

    var $Type;

    public function __construct($array = null){
        if($array == null || !is_array($array)){
            $this->docid = 0;
            $this->typeid = 0;
            $this->docpid = 0;
            $this->doctitle = "";
            $this->docsubtitle = "";
            $this->docstgnum = 0;
            $this->doccomnum = 0;
            $this->docview = 0;
            $this->doccreatetime = strtotime("1900-01-01");
            $this->docupdatetime = strtotime("1900-01-01");
            $this->docsort = 0;
            $this->docvalid = 0;
        }
        else{
            $this->docid = isset($array["docid"]) && is_numeric($array["docid"]) ? (int)$array["docid"] : 0;
            $this->typeid = isset($array["typeid"]) && is_numeric($array["typeid"]) ? (int)$array["typeid"] : 0;
            $this->docpid = isset($array["docpid"]) && is_numeric($array["docpid"]) ? (int)$array["docpid"] : 0;
            $this->doctitle = isset($array["doctitle"]) ? $array["doctitle"] ? "";
            $this->docsubtitle = isset($array["docsubtitle"]) ? $array["docsubtitle"] ? "";
            $this->docstgnum = isset($array["docstgnum"]) && is_numeric($array["docstgnum"]) ? (int)$array["docstgnum"] : 0;
            $this->doccomnum = isset($array["doccomnum"]) && is_numeric($array["doccomnum"]) ? (int)$array["doccomnum"] : 0;
            $this->docview = isset($array["docview"]) && is_numeric($array["docview"]) ? (int)$array["docview"] : 0;
            $this->doccreatetime = isset($array["doccreatetime"])  ? $array["doccreatetime"] : "1900-01-01";
            $this->docupdatetime = isset($array["docupdatetime"])  ? $array["docupdatetime"] : "1900-01-01";
            $this->docsort = isset($array["docsort"]) && is_numeric($array["docsort"]) ? (int)$array["docsort"] : 0;
            $this->docvalid = isset($array["docvalid"]) && is_numeric($array["docvalid"]) ? (int)$array["docvalid"] : 0;
            $this->doccreatetime = strtotime($this->doccreatetime);
            if(!$this->doccreatetime) $this->doccreatetime = strtotime("1900-01-01");
            $this->docupdatetime = strtotime($this->docupdatetime);
            if(!$this->docupdatetime) $this->docupdatetime = strtotime("1900-01-01");
            $this->Type = Type::GetType($this->typeid);
        }
    }
}
?>