<?php
require_once "../session_expiration.php";
require_once "caretaker_auth.php";
require_once "../config.php";


$name = $type = $age = $image = $description = "";
$name_err = $age_err = $image_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter the animal's Name.";
    } elseif(!preg_match('/^[a-zA-Z ]+$/', trim($_POST["name"]))){
        $name_err = "Sorry you entered an invalid name.";
    } else{
        $name = ucfirst(strtolower(trim($_POST["name"])));
    }

    if(empty(trim($_POST["age"]))){
        $age_err = "Please enter the animal's Age.";
    } elseif(!preg_match('/^[0-9]+$/', trim($_POST["age"]))){
        $age_err = "Sorry you entered an invalid age.";
    } else{
        $age = ucfirst(strtolower(trim($_POST["age"])));
    }

    if(!empty(trim($_POST["description"]))){
        $description = trim($_POST["description"]);
    }

    if(!empty($_FILES["imageUpload"]["name"])){
        $fileType = $_FILES["imageUpload"]["type"];
        $allowedFileTypes = array('image/jpeg', 'image/png', 'image/jpg');
        if(!in_array($fileType,$allowedFileTypes)){
            $image_err = "Unsupported file type.";
        }
    }

    $type = $_POST["type"];

    if(empty($name_err) && empty($age_err) && empty($image_err)){
        $sql = "INSERT INTO Animal (Name, Type, Age, Description) VALUES (?,?,?,?)";
        if($stmt = mysqli_prepare($link, $sql)){
            $age = date("Y")-$age; // save year
            mysqli_stmt_bind_param($stmt, "ssis", $name, $type, $age, $description);

            if(mysqli_stmt_execute($stmt)){
                $newID = mysqli_stmt_insert_id($stmt);
                mysqli_stmt_close($stmt);

                if(!empty($_FILES["imageUpload"]["name"])){
                    $imageUploadPath = "images/".$newID.".jpg";
                    $sql = "UPDATE Animal SET Image = '$imageUploadPath' WHERE ID = ?";
                    
                    if($stmt = mysqli_prepare($link, $sql)){
                        mysqli_stmt_bind_param($stmt, "i", $newID);
                        if(mysqli_stmt_execute($stmt)){
                            $imageTemp = $_FILES["imageUpload"]["tmp_name"]; 
                            move_uploaded_file($imageTemp, "../".$imageUploadPath);
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                header('Location: '.$_SERVER['PHP_SELF'].'?msg=success');
            
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal Form</title>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 475px; padding: 20px; margin:auto;}
        .required:after {
            content:" *";
            color: red;
        }
    </style>
</head>

<header>
  <div class="logo">CompanyLogo</div>
  <div class="header-right">
    <a class="active" href="caretaker.php">Go back to Animals</a>
  </div>
</header>

<body>
    <div class="col-md-12">
        <?php
            if(!empty($_GET['msg']))
                echo "<div class='alert alert-success alert-dismissible'> Animal created <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
        ?>
    </div>
    <div class="wrapper">
        <h2>Register a new animal in the system</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label class="required">Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>

            <div class="form-group">
                <label>Set animal species</label>
                <select name="type">
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Bunny">Bunny</option>
                    <option value="Hamster">Hamster</option>
                    <option value="Guinea Pig">Guinea Pig</option>
                </select>
            </div>

            <div class="form-group">
                <label class="required">Age</label>
                <input type="text" name="age" class="form-control <?php echo (!empty($age_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $age; ?>">
                <span class="invalid-feedback"><?php echo $age_err; ?></span>
            </div> 

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo $description?></textarea>
            </div>

            <div class="form-group">
                <label>Upload image</label>
                <input type="file" name="imageUpload" class="form-control">
                <span style="color:red; font-weight: bold;"><?php echo $image_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form>
    </div>    
</body>
