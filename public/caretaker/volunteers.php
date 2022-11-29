<?php
// Initialize the session
require_once "../session_expiration.php";
require_once "caretaker_auth.php";
require_once "../config.php";
require_once "fetch_volunteer.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteers</title>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a href="caretaker.php">Animals</a>
            <a class="active" href="#Volunteers">Volunteers</a>
            <a href="reservations.php">Reservation System</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="filters container">
        <label>Filter by verification:</label>
        <select name="filterVerification" id="filterVerification">
            <option value="Verified">Verified</option>
            <option value="Unverified">Unverified</option>
        </select>

        <label>Filter by name:</label>
        <input type="text" class="filterName" id="filterName"/>

        <label>Filter by Email:</label>
        <input type="text" class="filterEmail" id="filterEmail"/>
    </div>

    <div class="container volunteer_listing"> <!-- content from fetch -->
        <table class="table"></table>
        <div class="pages"></div>
    </div>

    <div class="modal fade" id="verificationModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm to modify verification</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="confirmVerificationChange btn btn-default" data-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewUserModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Volunteer Information</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>


<script type="text/javascript">
    $(document).ready(function(){
        
        function updateTable(curPage) {
            var verificationValue = $("#filterVerification").val();
            var nameValue = $("#filterName").val();
            var emailValue = $("#filterEmail").val();
            
            $.ajax({
                url:"fetch_volunteers.php",
                type:"POST",
                data: {verification: verificationValue, name: nameValue, 
                        email: emailValue, page: curPage},
                
                success:function(data){ 
                    $(".volunteer_listing").html(data);
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

        $(document).on('click', '.viewVolunteer', function(){  
            $id = $(this).attr("id");

            $.ajax({
                url:"fetch_volunteer.php",
                type:"POST",
                data: {ID: $id},

                success:function(data){
                    $(".modal-body").html(data);
                    $("#viewUserModal").modal("show");
                }   
            });


        });

        $(document).on('click', '.verifyVolunteer', function(){  
            $id = $(this).attr("id");
            var currentRow=$(this).closest("tr");
            var name = currentRow.find(".name").text();
            var surname = currentRow.find(".surname").text();

            $(".modal-body").html("<p>"+name+" "+surname+"</p>");
            $("#verificationModal").modal("show");
        });

        $(document).on('click', '.confirmVerificationChange', function(){  
            $.ajax({
                url:"verifyVolunteer.php",
                type:"POST",
                data: {ID: $id},

                success:function(){
                    updateTable(page);
                }   
            });
        }); 
        
    });
</script>

</html>