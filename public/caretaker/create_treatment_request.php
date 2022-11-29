<?php
require_once "../config.php";

$name = $description = $animalID = $caretakerID = "";
$name_err = $animalID_err = $caretakerID_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["name"])))
        $name_err = "Please enter a name for the request";
    else
        $name = trim($_POST["name"]);

    if(empty($_GET["animalID"]))
        $animalID_err = "Missing animalID";
    else
        $animalID = $_GET["animalID"];

    if(empty($_GET["caretakerID"]))
        $caretakerID_err = "Missing caretakerID";
    else
        $caretakerID = $_GET["caretakerID"];
    

    if(empty($name_err) && empty($animalID_err) && empty($caretakerID_err)){
        $description = $_POST["description"];
        $sql = "INSERT INTO TreatmentRequests (Date, Name, Description, AnimalID, CaretakerID) VALUES (?,?,?,?,?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            $date = date("y-m-d");
            mysqli_stmt_bind_param($stmt, "sssii", $date, $name, $description, $animalID, $caretakerID);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_close($stmt);
                header('Location: animal_view.php?msg=success&animal='.$animalID);
            }
        }
    }
    else{
        if(empty($animalID_err))
            header('Location: animal_view.php?name_err=1&animal='.$animalID);
        else
            header("location: ../index.php");
    }
}   
?>
