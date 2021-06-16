<?php

DB_Manager::table("user", function(){
    $this->id();
    $this->varchar("token");
});
