<?php
include_once("./.inc/EnvBuilder.php");
(new Env('.env'))->load();
include('database/classes/db_manager.class.php');
include('.inc/manager.php');
var_dump(getenv("DB_USERNAME"));
if(isset($_GET["connexion2"])){
$db = new DB_Manager();
$db->insertToken('test');
}
