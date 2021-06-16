<?php
include "./.inc/discord.class.php";

session_start();
$_SESSION["discord"] = new Discord;