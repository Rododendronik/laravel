<?php
// Include config file
require_once "../config.php";
require_once "../session_expiration.php";
require_once "admin_auth.php";

 
// Define variables and initialize with empty values
$status = false;
$name = $surname = $email = $phone = $address = $password = $confirm_password = "";
$name_err = $surname_err = $phone_err = $address_err = $email_err = $password_err = $confirm_password_err = "";
 

if(isset($_SESSION["role_created"]) && $_SESSION["role_created"] == true){
    echo '<script>alert("Employee was created")</script>';
    unset($_SESSION["role_created"]);
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter your Name.";
    } elseif(!preg_match('/^[a-zA-Z]+$/', trim($_POST["name"]))){
        $name_err = "Sorry you entered invalid name.";
    } else{
        $name = ucfirst(strtolower(trim($_POST["name"])));
    }

    // Validate surname
    if(empty(trim($_POST["surname"]))){
        $surname_err = "Please enter your Surname.";
    } elseif(!preg_match('/^[a-zA-Z]+$/', trim($_POST["surname"]))){
        $surname_err = "Sorry you entered invalid surname.";
    } else{
        $surname = ucfirst(strtolower(trim($_POST["surname"])));
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else{
        // Prepare a select statement
        $sql = "SELECT Email FROM Users WHERE Email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate phone
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter your phone.";
    } elseif(!preg_match('/^(\+420)? ?\d{3} ?\d{3} ?\d{3}$/', trim($_POST["phone"]))){
        $phone_err = "Sorry you entered invalid phone number.";
    } else{
        $phone = trim($_POST["phone"]);
    }

    // Validate address
    if(empty(trim($_POST["address"]))){
        $address_err = "Please enter your address.";
    } else{
        $address = ucwords(strtolower(trim($_POST["address"])));
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } elseif(strlen(trim($_POST["password"])) > 60){
        $password_err = "Max lenght of password is 60 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($surname_err) && empty($phone_err) && empty($address_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO Users (Name, Surname, Email, Phone, Address, LoginPassword, UserRole) VALUES (?,?,?,?,?,?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssi", $param_name, $param_surname, $param_email, $param_phone, $param_address, $param_password, $param_role);
            
            // Set parameters
            $param_name = $name;
            $param_surname = $surname;
            $param_email = $email;
            $param_phone = $phone;
            $param_address = $address;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = $_POST["role"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $_SESSION["role_created"] = true;
                header('Location: '.$_SERVER['PHP_SELF']);
                die;
            }
            else{
                echo "Oops! Something went wrong. Please try again later.";  
            }

            // Close statement
            mysqli_stmt_close($stmt);
        
        } else{
            echo "Oops! Something went wrong. Please try again later.";
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
    <title>Creating Employee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 475px; padding: 20px; margin:auto;}
        .required:after {
            content:" *";
            color: red;
        }
    </style>
    <script type="text/javascript">
        // When the user clicks on div, open the popup
    </script>
</script>
</head>
<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a href="admin.php">User management</a>
        <a class="active" href="createEmployee.php">Create Employee</a>
        <a href="../logout.php">Logout</a>
     </div>
</header>
<body>
    <div class="wrapper">
        <h2>Create new account for Employee</h2>
        <div style="<?php echo $status == true ? "display:block;" : "display:none;" ?>">My content</div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
            <div class="form-group">
                <label class="required">Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label class="required">Surname</label>
                <input type="text" name="surname" class="form-control <?php echo (!empty($surname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $surname; ?>">
                <span class="invalid-feedback"><?php echo $surname_err; ?></span>
            </div>  
            <div class="form-group">
                <label class="required">Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div> 
            <div class="form-group">
                <label class="required">Phone (+420) xxx xxx xxx </label>
                <input type="text" name="phone"  placeholder="+420 541 141 145" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>    
            <div class="form-group">
                <label class="required">Address</label>
                <input type="text" name="address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>">
                <span class="invalid-feedback"><?php echo $address_err; ?></span>
            </div>   
            <div class="form-group">
                <label class="required">Password (min. 6 characters)</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label class="required">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label class="required">Employee Role</label>
                <select name="role">
                    <option value=1>Veterinarian</option>
                    <option value=2>Caretaker</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
        </form>
    </div>    
</body>
</html>