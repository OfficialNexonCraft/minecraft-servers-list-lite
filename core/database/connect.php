<?php
// Connection parameters
$DatabaseServer = "localhost";
$DatabaseUser   = "root";
$DatabasePass   = "admin";
$DatabaseName   = "minecraft-servers-list-lite";

// Connecting to the database
$database = new mysqli($DatabaseServer, $DatabaseUser, $DatabasePass, $DatabaseName);

?>