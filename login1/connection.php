<?php

$server = "localhost";
$username = "root";
$password = "";
$db = "loginhub";

$conn = new mysqli($server, $username, $password, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>