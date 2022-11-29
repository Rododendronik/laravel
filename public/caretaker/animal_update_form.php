<?php 

    require_once "../config.php";

    $name_err = $age_err = $image_err = "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $ID = $_GET['animalID'];

        if(empty(trim($_POST["name"]))){
            $name_err = "Enter";
        } elseif(!preg_match('/^[a-zA-Z ]+$/', trim($_POST["name"]))){
            $name_err = "Invalid";
        } else{
            $name = ucfirst(strtolower(trim($_POST["name"])));
        }
    
        if(empty(trim($_POST["age"]))){
            $age_err = "Enter";
        } elseif(!preg_match('/^[0-9]+$/', trim($_POST["age"]))){
            $age_err = "Invalid";
        } else{
            $age = ucfirst(strtolower(trim($_POST["age"])));
        }
    
        if(!empty(trim($_POST["description"]))){
            $description = trim($_POST["description"]);
        }
    
        if(!empty($_FILES["imageUpload"]["name"])){
            $fileType = $_FILES["imageUpload"]["type"];
            $allowedFileTypes = array('image/jpeg', 'image/png', 'image/jpg');
            if(!in_array($fileType, $allowedFileTypes)){
                $image_err = "Unsupported";
            }
        }
        
        $type = $_POST["type"];
        
        if(empty($name_err) && empty($age_err) && empty($image_err)){
            $age = date("Y") - $age;
            $sql = "UPDATE Animal SET Name = ?, Type = ?, Age = ?, Description = ? WHERE ID = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ssisi", $name, $type, $age, $description, $ID);
                if(mysqli_stmt_execute($stmt)){

                    mysqli_stmt_close($stmt);

                    if(!empty($_FILES["imageUpload"]["name"])){
                        $imageUploadPath = "images/".$ID.".jpg".time();

                        $sql = "SELECT Image FROM Animal WHERE ID = ?";
                        if($stmt = mysqli_prepare($link, $sql)){
                            mysqli_stmt_bind_param($stmt, "i", $ID);
                            if(mysqli_stmt_execute($stmt)){
                                mysqli_stmt_store_result($stmt);
                                mysqli_stmt_bind_result($stmt, $oldImagePath);
                                mysqli_stmt_fetch($stmt);
                            }
                            mysqli_stmt_close($stmt);
                        }
                        
                        $sql = "UPDATE Animal SET Image = ? WHERE ID = ?";
                        if($stmt = mysqli_prepare($link, $sql)){
                            mysqli_stmt_bind_param($stmt, "si", $imageUploadPath, $ID);
                            if(mysqli_stmt_execute($stmt)){

                                if(file_exists("../".$imageUploadPath)) //replace if new exists
                                    unlink("../".$imageUploadPath);
                                

                                if(!empty($oldImagePath) && file_exists("../".$oldImagePath)) // delete previous
                                    unlink("../".$oldImagePath);
                                

                                $imageTemp = $_FILES["imageUpload"]["tmp_name"]; 
                                move_uploaded_file($imageTemp, "../".$imageUploadPath);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                }
            }

            header('Location: animal_edit.php?animalID='.$ID.'&msg=success');
        }
        else{
            $errors = "";
            if(!empty($name_err)){
                $errors .= "&name_err=".$name_err;
            }
            if(!empty($age_err)){
                $errors .= "&age_err=".$age_err;
            }
            if(!empty($image_err)){
                $errors .= "&image_err=".$image_err;
            }

            header('Location: animal_edit.php?animalID='.$ID.$errors);
        }
    }
?>