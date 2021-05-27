<?php

class OnlyOneNamed {
    public $parent = null;
    public $parent_array = null;
    public $name = null;
    public $first_of_the_name = true;
    public $new = true;

    public function __construct($name,$parent = null, $parent_array = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->parent_array = (($parent_array)?$parent_array:(strtolower(get_class($this)."s")));
        $this->verify_existance();
    }

    private function import($obj)
    {   
        foreach (get_object_vars($obj) as $key => $value) {
            $this->$key = $value;
        }
        $this->first_of_the_name = false;
        $this->new = true;
    }   

    private function verify_existance(){
        if(!$this->parent){return;}
        $array = $this->parent_array;
        $name = $this->name;
        $obj = null;
        foreach ($this->parent->$array as $element) {
            if($element->name == $name){
                $obj = $element;
            }
        }
        if($obj){
            $this->import($obj);
        } else {
            array_push($this->parent->$array,$this);
        }
    }

    public function getParent(){
        return $this->parent;
    }
}