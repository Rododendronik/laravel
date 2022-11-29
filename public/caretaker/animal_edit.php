<?php
require_once "../session_expiration.php";
require_once "caretaker_auth.php";

require_once "../config.php";
require_once "animal_fill_out.php";
require_once "animal_update_form.php";

$ID = $_GET['animalID'];
$name = $type = $age = $image = $description = "";
$name_err = $age_err = $image_err = "";

if(isset($_GET['name_err']))
    $name_err = $_GET['name_err']." name";

if(isset($_GET['age_err']))
    $age_err = $_GET['age_err']." age";

if(isset($_GET['image_err']))
    $image_err = $_GET['image_err']." type";


fill_animal($ID ,$name, $type, $age, $image, $description);
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
        .required:after { content:" *"; color: red;}
    </style>
</head>

<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a class="active" href="caretaker.php">Go back to Animals</a>
    </div>
</header>

<body>
    <div class="col-md-12">
        <?php
            if(!empty($_GET['msg']))
                echo "<div class='alert alert-success alert-dismissible'> Animal edited <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
        ?>
    </div>
    <div class="wrapper">
        <h2>Edit animal information</h2>
        <form action="<?php echo htmlspecialchars("animal_update_form.php"."?animalID=$ID"); ?>" method="post"  enctype="multipart/form-data" autocomplete="off">
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
                <textarea name="description" cols="100"><?php echo $description?></textarea>
            </div>

            <div class="form-group">
                <label>Upload image</label>
                <p>Current image: <?php echo $image ?>
                <input type="file" name="imageUpload" class="form-control">
                <span style="color:red; font-weight: bold;"><?php echo $image_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Update">
            </div>
        </form>
        <img src= <?php echo "../".$image ?> width="300px" height="300px" style="object-fit: cover;">
    </div>    
</body>