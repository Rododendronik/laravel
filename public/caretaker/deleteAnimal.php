<?php

require_once "../config.php";

if(isset($_POST['animalID'])){
    ?><script>alert("potao")</script><?php
    $animalID = $_POST['animalID'];

    $sql = "SELECT Image FROM Animal WHERE ID = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $animalID);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Image);
            mysqli_stmt_fetch($stmt);
            $imagePath = "../".$Image;

            $sql = "DELETE FROM Animal WHERE ID = ?";
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "i", $animalID);
                mysqli_stmt_execute($stmt);
                if(file_exists($imagePath)) //delete if exists
                    unlink($imagePath); 
            }
        }
    }

    mysqli_stmt_close($stmnt);
}
?>