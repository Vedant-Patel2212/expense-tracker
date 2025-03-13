<?php
$servername = "localhost"; 
$username = "root"; 
$password = "vedant2212";
$database = "expense_tracker"; 

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_error());
}

?>
