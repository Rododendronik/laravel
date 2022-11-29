<?php

require_once "../config.php";

if(isset($_POST['ID'])){
    $id = $_POST['ID'];

    $sql = "UPDATE VolunteersVerification SET Verified = Verified XOR 1 WHERE VolunteerID=?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmnt);
}
?>