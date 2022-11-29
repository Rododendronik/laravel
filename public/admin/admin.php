<?php
require_once "../config.php";
require_once "../session_expiration.php";
require_once "admin_auth.php";
 

if(isset($_POST['deleteUser']) && isset($_POST['userid']) && isset($_POST['userrole'])){

    $userID = $_POST['userid'];
    $role = $_POST['userrole'];
    
    if($role == 1){
        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
    } else if($role == 2){

        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    } else if($role == 3){
        $sql = "DELETE FROM Reservations WHERE VolunteerID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM VolunteersVerification WHERE VolunteerID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .center{ font: 14px sans-serif; text-align: center;}
    </style>
</head>
<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a class="active" href="#home">User management</a>
        <a href="createEmployee.php">Create Employee</a>
        <a href="../logout.php">Logout</a>
  </div>
</header>
<body>
    <div class="center">
        <h1 class="my-5">This is Admin site, be careful.</h1>

        <div class="filters">
            <label>Filter by role:</label>
            <select name="filterRole" id="filterRole">
                <option value="3">Volunteers</option>
                <option value="2">Caretakers</option>
                <option value="1">Veterinarians</option>
            </select>

            <label>Filter by name:</label>
            <input type="text" class="filterName" id="filterName"/>

            <label>Filter by Email:</label>
            <input type="text" class="filterEmail" id="filterEmail"/>
        </div>
    </div>
    
    <div class="container user_listing"> <!-- content from fetch -->
        <table class="table"></table>
        <div class="pages"></div>
    </div>

    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Deleting user from system:<span id="user"> </span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">UserID</label>
                                    <input required type="text" readonly name="userid" id="userid" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">User Role</label>
                                    <input required type="text" readonly name="userrole" id="userrole" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Name</label>
                                    <input required type="text" readonly name="name" id="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input required type="text" readonly name="email" id="email" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' id="deleteUser" name='deleteUser'>Delete User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</body>

<footer>
    <script type="text/javascript">
        $(document).ready(function(){
            
            function updateTable(curPage) {
                var roleValue = $("#filterRole").val();
                var nameValue = $("#filterName").val();
                var emailValue = $("#filterEmail").val();
                
                $.ajax({
                    url:"fetch_users.php",
                    type:"POST",
                    data: {role: roleValue, name: nameValue, 
                            email: emailValue, page: curPage},
                    
                    success:function(data){ 
                        $(".user_listing").html(data);
                    }
                });
            }

            var page = 1;
            updateTable(page);

            
            $(document).on('change', '.filters select', function(){
                page = 1;
                updateTable(page);
            });

            $(document).on('input', '.filters input', function(){
                page = 1;
                updateTable(page);
            });

            $(document).on('click', '.paginationLink', function(){  
                page = $(this).attr("id");  
                updateTable(page);  
            }); 
        });

        $(document).on('click', '.delete', function(){  
            var role = $(this).attr('systemrole');
            var userid = $(this).attr('userid');
            var name = $(this).attr('name').concat(" ").concat($(this).attr('surname'));
            var email = $(this).attr('email')
            $("#userid").val(userid);
            $("#name").val(name);
            $("#userrole").val(role);
            $("#email").val(email);
            $("#deleteModal").modal("show");
        }); 
    </script>
</footer>
</html>