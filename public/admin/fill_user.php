<?php
require_once "../config.php";
    function fill_animal(&$ID ,&$name, &$surname, &$email, &$phone, &$address){
        global $link;

        $sql = "SELECT UserID, Name, Surname, Email, Phone, Address FROM Users WHERE UserID = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
    
            mysqli_stmt_bind_param($stmt, "i", $ID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $ID ,$name, $surname, $email, $phone, $address);
    
                mysqli_stmt_fetch($stmt);
            }  
            mysqli_stmt_close($stmt);
        }
    }
?>
