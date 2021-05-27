<?php

class Table extends OnlyOneNamed {
    public $columns = [];
    public $current_column;
    public $constraints = [];
    public $current_key;

    public function __construct(String $name)
    {
        parent::__construct($name, DB_Manager::getInstance() );
    }

    public function __call($name, $arg)
    {
        if($name && sizeof($arg)==1){
            return $this->column(strtolower($arg[0]), strtoupper($name));
        } 
    }


    public function column(String $name, String $type = null, Int $size = null){
        $this->add_current_column();
        $column = new Column($this, $name);
        $column->type($type);
        $column->size($size);
        return $column;
    }

    public function add_current_column(){
        //if($this->current_column instanceof Column){
        //    $this->current_column->add_to_parent();
        //    $this->current_column = null;
        //}
    }

    public function add_current_key(){
        //if($this->current_key instanceof ForeignConstraint){
        //    $this->add_key($this->current_key);
        //    $this->current_key = null;
        //}
    }


    public function foreign($name = "") : ForeignConstraint{
        $obj = new ForeignConstraint($this, $name);
        return $obj;
    }

    public function unique($name = "") : UniqueConstraint{
        $obj = new UniqueConstraint($this, $name);
        return $obj;
    }

    // --- COLUMN INSTANCIATION ---

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:11:05 
     * @Desc: Instanciate int column as current_column, set auto_increment and primary key
     */
    public function id($name = "id"){
        $c = $this->column($name, "INT",11);
        $c->extra("AUTO_INCREMENT PRIMARY KEY");
        return $c;

        //[TODO] INSTANCIATE PRIMARY KEY
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:01:17 
     * @Desc: Instanciate float column as current_column
     */
    public function float(String $name){
        return $this->column(strtolower($name), "FLOAT");
    }
    
    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:01:54 
     * @Desc: Instanciate string column as current_column
     */
    public function varchar($name){
        return $this->column($name, "VARCHAR",255);
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:11:51 
     * @Desc: Instanciate date column as current_column
     */
    public function date($name = "id"){
        $this->column($name, "DATE");
        $this->current_column->default("NOW()");
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:12:41 
     * @Desc: Instanciate datetime column as current_column
     */
    public function datetime($name = "id"){
        $this->column($name, "DATETIME");
        $this->current_column->default("NOW()");
    }
    

    // --- GENERATION REQUEST FUNCTIONS

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 22:20:58 
     * @Desc: Concat request for each column of table 
     */
    private function concat_columns() : String{
        $columns = [];
        foreach ($this->columns as $column) {
            $req = $column->definition();
            if(!$column->new){
                continue;
            }
            if(!$this->first_of_the_name){
                if($column->first_of_the_name){
                    $req = "ADD COLUMN ".$req;
                } else {
                    $req = "MODIFY COLUMN ".$req;
                }
            } 
            array_push($columns,$req);
        }
        return implode(",",$columns);
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-21 15:36:22 
     * @Desc:  Concat request for each keys of table 
     */
    private function concat_keys() : String{
        $constraints = [];
        foreach ($this->constraints as $constraint) {
            $req = $constraint->sort_request();
            if($constraint->new){
                if(!$this->first_of_the_name){
                    $req = "ADD ".$req;
                }
            }
            array_push($constraints,$req);
        }
        return implode(",",$constraints);
    }

    public function add_key(Constraint $constraint){
        array_push($this->constraints,$constraint);
    }


    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 22:21:21 
     * @Desc: Generate request for interact with database 
     */
    public function generate_table_request(){
        $request = "";
        
        if($this->new && $this->first_of_the_name){
            $request .= "CREATE TABLE `$this->name`(";
            $request .= $this->concat_columns();
            $request .= (sizeof($this->constraints)>0)?",".$this->concat_keys():"";
            $request .= ");";
        } else if($this->new){
            $req_columns = $this->concat_columns();
            $req_keys = $this->concat_keys();
            if(strlen($req_columns)>0 ){
                $request .= "ALTER TABLE ".$this->name." ".$req_columns.";";
            }
            if(strlen($req_keys)>0 ){
                $request .= "ALTER TABLE ".$this->name." ".$req_keys.";";
            }
        }
        
        return $request;
    }

    public function rebase(){
        $this->new = false;
        foreach ($this->columns as $column) {
            $column->new = false;
        }
        foreach ($this->constraints as $constraint) {
            $constraint->new = false;
        }
    }
}

?>