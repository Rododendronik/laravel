<?php

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Caretaker"){
    header("location: ../index.php");
    exit;
}

?>