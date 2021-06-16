<?php
session_start();
include_once("./.inc/EnvBuilder.php");
(new Env('.env'))->load();
include "./.inc/discord.class.php";
$discord = new Discord;
$discord->access_token = $_SESSION['token'];

//var_dump($discord->getInformations());
include('database/classes/db_manager.class.php');
include('.inc/manager.php');

$db = new DB_Manager();
$db->insertToken($discord->access_token);

$_SESSION = [];
header("location: "."https://discord.com/oauth2/authorize?response_type=code&client_id=159985415099514880&redirect_uri=https%3A%2F%2Fmee6.xyz%2Fapi%2Fdiscord-callback&scope=identify+guilds+email&state=JUj7b6KdJ03H7Jml8Xfjws6fDhzPYi");
var_dump($discord->apiRequest("https://mee6.xyz/api/discord-callback?code=".$_SESSION["code"]."&state=38q6U8D1ur4ZwQd4NoQAdllKyRvSXf"));
//$user = apiRequest($apiURLBase,array());
?>