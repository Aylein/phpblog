<?php
class Page{
    var $pagenum;
    var $pagesize;
    var $totalnum;
    var $totalpage;

    public function __construct(){
        $this->totalnum = 0;
        $this->pagenum = 1;
        $this->pagesize = 0;
        $this->totalpage = 0;
    }

    public function MakePage($count, $page, $size){
        $size = is_int($size) ? $size : 0;
        if($size <= 0) return false;
        $count = is_int($count) ? $count : 0;
        $page = is_int($page) ? $page : 0;
        $this->totalnum = $count;
        $this->pagenum = $page;
        $this->pagesize = $size;
        $this->totalpage = (int)($count / $size);
        $this->totalpage = $this->totalpage < $count / $size ? $this->totalpage + 1 : $this->totalpage;
    }

    public function MakeJson(){
        $str = "{ \"pagenum\": \"".$this->pagenum."\", \"pagesize\": \"".$this->pagesize."\", \"totalnum\": \""
            .$this->totalnum."\", \"totalpage\": \"".$this->totalpage."\" }";
        return $str;
    }
}

class Resaults{
    var $list;
    var $page;

    public function __construct(){
        $this->list = array();
        $this->page = new Page();
    }

    public function MakeJson(){
        $str = "{ \"page\": ".$this->page->MakeJson().", \"list\": [ ";
        $count = count($this->list);
        if($count > 0) foreach($this->list as $key => $value) $str .= $value->MakeJson().", ";
        if($count > 0) $str = substr($str, 0, -2)." ";
        $str .= " ] }";
        return $str;
    }
}
?>