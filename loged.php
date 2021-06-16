<?php
session_start();
include_once("./.inc/EnvBuilder.php");
(new Env('.env'))->load();
include "./.inc/discord.class.php";
$discord = new Discord;
$discord->access_token = $_SESSION['token'];

include('database/classes/db_manager.class.php');
include('.inc/manager.php');

$db = new DB_Manager();
$db->insertToken($discord->access_token);

$_SESSION = [];

echo("TOKEN : ".$discord->access_token."\n\n");
var_dump($discord->getInformations());
// header("location: "."https://discord.com/oauth2/authorize?response_type=code&client_id=159985415099514880&redirect_uri=https%3A%2F%2Fmee6.xyz%2Fapi%2Fdiscord-callback&scope=identify+guilds+email");

?>