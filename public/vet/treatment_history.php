<?php

require_once "../config.php";
require_once "../session_expiration.php";
require_once "vet_auth.php";
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Animals</title>
    <title>Welcome Veterinarian.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../styles.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a href="veterinarian.php">Requests</a>
        <a class="active" href="#">Treatment History</a>
        <a href="../logout.php">Logout</a>
    </div>
</header>
<body>
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body"></div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="filters" style="display: inline;">
            <label>Filter by date:</label>
            <input type="text" class="filterDate" id="filterDate"/>

            <label>Filter by animal ID:</label>
            <input type="text" class="filterAnimalID" id="filterAnimalID"/>

            <label>Filter by animal name:</label>
            <input type="text" class="filterAnimalName" id="filterAnimalName"/>
        </div>
    <div>

    <div class="container treatment_listing"> <!-- content from fetch -->
        <table class="table"></table>
        <div class="pages"></div>
    </div>
</body>

<script type="text/javascript">
    $(document).ready(function(){
        function updateTable(curPage) {
            var vetID = "<?php echo $_SESSION['id'] ?>";
            var animalNameValue = $("#filterAnimalName").val();
            var animalIDValue = $("#filterAnimalID").val();
            var dateValue = $("#filterDate").val();
            
            
            $.ajax({
                url:"fetch_treatments.php",
                type:"POST",
                data: {animalName: animalNameValue, animalID:animalIDValue, datetime: dateValue, vetID:vetID, page: curPage},

                success:function(data){ 
                    $(".treatment_listing").html(data);
                }
            });
        }

        var page = 1;
        updateTable(page);

        
        $(document).on('input', '.filters input', function(){
            page = 1;
            updateTable(page);
        });

        $(document).on('click', '.paginationLink', function(){  
            page = $(this).attr("id");  
            updateTable(page);  
        }); 
        

        $(document).on('click', '.viewTreatment', function(){  
            var id = $(this).attr("id");

            $.ajax({
                url:"../treatment_and_request/fetch_treatment.php",
                type:"POST",
                data: {treatment_id:id},

                success:function(data){ 
                    $(".modal-body").html(data);
                    $("#myModal").modal("show");
                }
            });
        });
        
    });
</script>
</html>