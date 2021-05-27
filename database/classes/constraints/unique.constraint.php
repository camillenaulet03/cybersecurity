<?php

class UniqueConstraint extends Constraint {
    public $columns = [];

    public function sort_request(){
        return "CONSTRAINT ".$this->name." PRIMARY KEY (".implode($this->columns).")";
    }

    public function on($columns = []){
        if($this->name == ""){
            $this->name = "PK_".$this->getParent()->name."_".implode("_",$this->columns);
        }
        $this->columns = $columns;
        $this->table->add_key($this);
    }

}