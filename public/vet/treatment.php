<?php

require_once "../config.php";
require_once "fetch_request_info_for_treatment.php";
require_once "../session_expiration.php";
require_once "vet_auth.php";

$vetID = $_SESSION['id'];
$treatmentRequestID = $_GET['treatmentRequestID'];

$name = $datetime = $description = "";
$name_err = $datetime_err = $animalID_err = $treatmentRequestID_err = "";


$date = new DateTime();
$datetime = $date->format('Y-m-d H:i');

$sql = "SELECT AnimalID FROM TreatmentRequests WHERE ID = ?";

if(!empty($treatmentRequestID) && $stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $treatmentRequestID);
    if(mysqli_stmt_execute($stmt)){
      mysqli_stmt_store_result($stmt);
      mysqli_stmt_bind_result($stmt, $animalID);
      mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);   
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter the Name.";
    } elseif(!preg_match('/^[a-zA-Z0-9 ]+$/', trim($_POST["name"]))){
        $name_err = "Sorry you entered an invalid name.";
    } else{
        $name = ucfirst(strtolower(trim($_POST["name"])));
    }

    if(empty(trim($_POST["datetime"]))){
        $datetime_err = "Please enter date and time.";
    } elseif(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}$/', trim($_POST["datetime"]))){
        $datetime_err = "Sorry you entered an invalid date or time format.";
    } else{
        $datetime = ucfirst(strtolower(trim($_POST["datetime"])));
    }

    if(!empty(trim($_POST["description"]))){
        $description = trim($_POST["description"]);
    }

    if(empty($animalID)){
        $animalID_err = "missing animalID";
    }

    if(empty($name_err) && empty($datetime_err) && empty($animalID_err)){
        $sql = "INSERT INTO Treatments (Name, Datetime, Description, AnimalID, VeterinarianID) VALUES (?,?,?,?,?)";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssii", $name, $datetime, $description, $animalID, $vetID);
            if(mysqli_stmt_execute($stmt)){
               mysqli_stmt_close($stmt);
               $sql = "DELETE FROM TreatmentRequests WHERE ID = ?";
               if($stmt = mysqli_prepare($link, $sql)){
                    mysqli_stmt_bind_param($stmt, "i", $treatmentRequestID);
                    mysqli_stmt_execute($stmt);
               }
               header('Location: veterinarian.php');
               die;
            }
            mysqli_stmt_close($stmt);   
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Animals</title>
    <title>Welcome Veterinarian.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 475px; padding: 20px; margin:auto;}
        .required:after { content:" *"; color: red;}
    </style>
</head>

<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a href="veterinarian.php">Requests</a>
        <a href="treatment_history.php">Treatment History</a>
        <a href="../logout.php">Logout</a>
    </div>
</header>
<body>
  <div class="col-md-12">
      <?php
          if(!empty($_GET['msg']))
              echo "<div class='alert alert-success alert-dismissible'> Treatment created <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
      ?>
  </div>
  <div class="modal fade" id="confirmModal" role="dialog">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Before submitting treatment, make sure all information is correct.</h4>
              </div>
              <div class="modal-footer">
                  <button type="button" class="confirmTreatmentInfo btn btn-default" data-dismiss="modal">Confirm</button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>
  
  <div class = "wrapper requestInfo"><?php printRequestInfoForTreatment($treatmentRequestID)?></div>
  <div class = "wrapper">
    <h2>Treatment Form</h2>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]."?treatmentRequestID=".$treatmentRequestID); ?>" method="post" autocomplete="off">
          <div class="form-group">
              <label class="required">Name</label>
              <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
              <span class="invalid-feedback"><?php echo $name_err; ?></span>
          </div>


          <div class="form-group">
              <label class="required">Date and Time (YYYY-MM-DD HH:MM) </label>
              <input type="text" name="datetime" class="form-control <?php echo (!empty($datetime_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $datetime?>">
              <span class="invalid-feedback"><?php echo $datetime_err; ?></span>
          </div> 

          <div class="form-group">
              <label>Description</label>
              <textarea name="description" cols= 80 rows=10><?php echo $description?></textarea>
          </div>

          <div class="form-group">
              <label>By submitting, original request will be deleted</label>
              <input type="submit" class="btn btn-primary" value="Submit">
              <input type="reset" class="btn btn-secondary ml-2" value="Reset">
          </div>
      </form>
  </div>
</body>

<script>

</script>
</html>