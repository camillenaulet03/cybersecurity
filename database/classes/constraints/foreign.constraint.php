<?php

class ForeignConstraint extends Constraint {
    public $column = null;
    public $ref_table = null;
    public $ref_column = null;
    public $on_delete = null;
    public $on_update = null;
    
    public function __construct(Table $table, String $name = "")
    {
        parent::__construct($table,$name);
        $this->getParent()->add_current_key();
        $this->getParent()->current_key = $this;
    }
    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 21:50:32 
     * @Desc: set a reference for FK
     */
    public function references($table, $column){
        $this->ref_table = $table;
        $this->ref_column = $column;
        if($this->name == ""){
            $this->name = "FK_".$this->getParent()->name."_".$this->ref_table."_".$this->ref_column;
        }
        $this->getParent()->current_key = $this;
        return $this;
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-21 15:55:50 
     * @Desc: Set column focused 
     */
    public function on($column){
        $this->column = $column;
        return $this;
    }


    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 22:03:44 
     * @Desc: Set comportment on reference delete 
     */
    public function onDelete($procedure){
        $this->on_delete = $procedure;
        $this->getParent()->current_key = $this;
    }
    
    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 22:03:55 
     * @Desc: Set comportment on reference update 
     */
    public function onUpdate($procedure){
        $this->on_delete = $procedure;
        $this->getParent()->current_key = $this;
    }

    public function sort_request(){
        $constraint = "CONSTRAINT ".$this->name." FOREIGN KEY ($this->column) REFERENCES $this->ref_table($this->ref_column)";
        if($this->on_update){
            $constraint .= " ON DELETE ".$this->on_update;
        }
        if($this->on_delete){
            $constraint .= " ON DELETE ".$this->on_delete;
        }
        return $constraint;
    }
    

}