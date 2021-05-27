<?php

DB_Manager::table("user", function(){
    $this->id();
    $this->varchar("login");
    $this->varchar("password");
    $this->varchar("token")->nullable();
});