<?php
session_start();
include_once("./.inc/EnvBuilder.php");
(new Env('.env'))->load();

include "./.inc/discord.class.php";

$discord = new Discord;