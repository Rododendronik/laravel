<?php
// Initialize the session
require_once "session_exp_home.php";
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["role"])){

    if($_SESSION["role"] === "Admin"){
        header("location: admin/admin.php");
    } elseif($_SESSION["role"] === "Veterinarian"){
        header("location: vet/veterinarian.php");
    } elseif($_SESSION["role"] === "Caretaker"){
        header("location: caretaker/caretaker.php");   
    } elseif($_SESSION["role"] === "Volunteer"){
        header("location: volunteer/welcome.php");
    }
    else{
        header("location: login.php");
    } 
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT UserID, Name, Email, LoginPassword, UserRole, Verified FROM Users LEFT JOIN VolunteersVerification AS verif ON Users.UserID = verif.VolunteerID WHERE Email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $name, $email, $hashed_password, $role, $verified);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["name"] = $name;
                            $_SESSION["email"] = $email;
                            $_SESSION['LAST_ACTIVITY'] = time();
                            
                            // Redirect user to welcome page based on his role
                            if($role == 0){
                                $_SESSION["role"] = "Admin";
                                header("location: admin/admin.php");
                            } elseif ($role == 1){
                                $_SESSION["role"] = "Veterinarian";
                                header("location: vet/veterinarian.php");
                            } elseif ($role == 2){
                                $_SESSION["role"] = "Caretaker";
                                header("location: caretaker/caretaker.php");
                            } else {
                                $_SESSION["role"] = "Volunteer";
                                $_SESSION["verified"] = $verified;
                                header("location: volunteer/welcome.php");
                            }
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else{
                    // email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='index.php'" src='images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a href="index.php">Home</a>
            <a href="animals.php">Animals</a>
            <a class="active" href="login.php">Log In</a>
            <a href="register.php">Register</a>
        </div>
    </header>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email: </label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password: </label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <h6>Want to help as Volunteer <a href="register.php">Sign up now</a>.</h6>
        </form>
    </div>
</body>
</html>