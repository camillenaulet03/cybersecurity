<?php

class Constraint extends OnlyOneNamed {
    
    public function  __construct(Table $table, String $name = ""){
        parent::__construct($name, $table, "constraints");
        return $this;
    }


}