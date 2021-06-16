<?php
include('database/classes/db_manager.class.php');
include('.inc/manager.php');
if(isset($_GET["connexion2"])){
$db = new DB_Manager();
$db->insertToken('test');
}
