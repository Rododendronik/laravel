<?php
// Initialize the session
require_once "config.php";
require_once "session_exp_home.php";

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(!isset($_GET["animal"])){
        header("location: index.php");
    }
    $idx = $_GET["animal"];

$sql = "SELECT ID, Name, Type, Age, Image, Description FROM Animal WHERE ID = ?";
    
if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $idx);
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_store_result($stmt);
            
        if(mysqli_stmt_num_rows($stmt) == 1){                    
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $name, $type, $age, $image, $description);
            mysqli_stmt_fetch($stmt);
            $age = date("Y") - $age;
        }
        else{
            header("location: index.php");
        }
    // Close statement
    mysqli_stmt_close($stmt);
    }
    else{
        header("location: index.php");
    }
}
else{
    header("location: index.php");
}

// Close connection
mysqli_close($link);
}
else{
    header("location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
	<meta charset="UTF-8">
    <title>Animal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles2.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>       
    .landing-page{
        position: relative;
        width: 100%;
        height: var(--navbar-height);
    }
    </style>
    <body>
        <header style="padding: 10px 0px 0px 0px">
            <div class="logo"><img onclick="window.location.href='index.php'" src='images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
            <div class="header-right">
                <a href="index.php">Home</a>
                <a class="active" href="animals.php">Animals</a>
                <a href="login.php">Log in</a>
                <a href="register.php">Register</a>
            </div>
        </header>
        <div class="animal-profile">
            <?php
            echo "<h1> $name - $type</h1>";
            echo "<img style='max-width:none; width:17%; height 17%;' src='$image'>";
            echo "<h2>$age Years</h2>";
            echo "<p>$description</p>";
            ?>
            <a class="btn btn-primary" href="<?php echo $_SERVER['HTTP_REFERER'] ?>">Back</a>
        </div>
    </body>
</html>