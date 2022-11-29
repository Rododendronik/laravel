<?php

require_once "../config.php";

if(isset($_POST['requestID'])){
    $requestID = $_POST['requestID'];

    $sql = "DELETE FROM TreatmentRequests WHERE ID = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $requestID);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmnt);
}
?>