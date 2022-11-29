<?php

require_once "../config.php";

if(isset($_POST['ID']) || isset($_POST['vetID'])){
    $requestID = $_POST['requestID'];
    $vetID = $_POST['vetID'];

    $sql = "UPDATE TreatmentRequests SET VeterinarianID = ? WHERE ID =?";

    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $vetID, $requestID);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmnt);
}
?>