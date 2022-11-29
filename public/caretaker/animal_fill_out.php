<?php 
    require_once "../config.php";
    function fill_animal(&$ID ,&$name, &$type, &$age, &$image, &$description){
        global $link;
        
        $sql = "SELECT ID, Name, Type, Age, Image, Description FROM Animal WHERE ID = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
    
            mysqli_stmt_bind_param($stmt, "i", $ID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $ID ,$name, $type, $age, $image, $description);
                mysqli_stmt_fetch($stmt);
                $age = date("Y") - $age;
            }  
            mysqli_stmt_close($stmt);
        }
    }

?>