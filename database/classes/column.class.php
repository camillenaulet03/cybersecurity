<?php

class Column extends OnlyOneNamed{
    public $table;

    public $type = null;
    public $size = null;
    public $default = null;
    public $nullable = false;
    public $extra = null;
    public $unique = null;
    public $comment = null;
    public $position = null;

    public function __construct(Table $table, String $name){
        parent::__construct($name,$table);
    }

    public function __call($name, $arguments)
    {
        if(property_exists($this,$name)){
            $this->$name = (sizeof($arguments)>0)?$arguments[0]:true;
            $this->getParent()->current_column = $this;
            return $this;
        }
    }    

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 22:15:11 
     * @Desc: Set comment to column 
     */
    public function comment($comment){
        $this->comment($comment);
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-21 12:23:23 
     * @Desc: Set size of column 
     */
    public function size($size){
        $this->size = intval($size);
        $this->getParent()->current_column = $this;
        return $this;
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:15:52 
     * @Desc: Add this to parent
     */
    public function add_to_parent(){
        array_push($this->getParent()->columns,$this);
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 12:10:04 
     * @Desc: Generate line for mysql request 
     */
    public function definition(){
        $r = $this->name." ".$this->type;
        $r .= ($this->size?"(".$this->size.")":"");
        $r .= ($this->unique?"UNIQUE":"");
        $r .= ($this->nullable?"":" NOT NULL ");
        $r .= ($this->default?" DEFAULT ".((is_string($this->default)&& !strpos ($this->default,"("))?"'$this->default'":$this->default):"");
        
        $r .= ($this->comment?" COMMENT '".str_replace(["'",'"'],"_",$this->comment)."' ":"");
        $r .= ($this->extra?" $this->extra":"");
        return $r;
    }
}