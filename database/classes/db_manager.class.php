<?php

class DB_Manager{
    private static $_instance = null;
    public $request = "";
    public $tables = [];
    public $migrations = [];

    public function __construct(){
    }

    public static function destroy(){
        self::$_instance = null;
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:42:43 
     * @Desc: Singleton function 
     */
    public static function getInstance() : DB_Manager {
        if(is_null(self::$_instance)) {
          return self::$_instance = new DB_Manager();  
        }

        return self::$_instance;
    }

    /** 
     * @Author: DELEON Johan 
     * @Date: 2021-05-20 09:42:52 
     * @Desc: create new table function 
     */
    public static function table(String $name, $function){
        $table = new Table($name);
        $function->call($table);
        $table->add_current_column();
        $table->add_current_key();
        DB_Manager::getInstance()->request .= $table->generate_table_request();
        return $table;
    }

    public function rebase(){
        $this->request = "";
        foreach ($this->tables as $table) {
            $table->rebase();
        }
    }

    public static function faker(String $name, $function){
        $table = new Table($name);
        $function->call($table);
        $table->add_current_column();
        $table->add_current_key();
        DB_Manager::getInstance()->request .= $table->generate_table_request();
        $table->rebase();
        return $table;
    }

    public function migrate(){
        $manager = new Manager();
        $db = $manager->db_connect();
        DB_Manager::destroy();

        $this->get_done_migrations();
        foreach (scandir("./database/instructions/") as $file) {
            if(strlen(str_replace(".","",$file))==0){continue;}
            if(file_exists("./database/instructions/".$file)){
                require_once("./database/instructions/".$file);
                $db->beginTransaction();
                $req = $db->prepare(DB_Manager::getInstance()->request);
                $status = $req->execute();
                 if($status){
                    $db->query("INSERT INTO _deploysql_ames(`file`) VALUES (\"".$file."\")");
                    $db->commit();
                } else {
                    return $db->rollBack();
                }

            }
        }
        $this->rebase();
    }

    public function get_done_migrations(){
        $manager = new Manager();
        $db = $manager->db_connect();
        $db->exec("CREATE TABLE IF NOT EXISTS `_deploysql_ames`(id INT PRIMARY KEY AUTO_INCREMENT, file VARCHAR(255) NOT NULL);");

        $sql = "SELECT * FROM _deploysql_ames;";
        $req = $db->prepare($sql);
        $req->execute();

        while($line = $req->fetch()){
            array_push($this->migrations,$line["file"]);
        }
    }
}


?>