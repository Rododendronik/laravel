<?php

require_once "../config.php";
require_once "../treatment_and_request/fetch_treatment_request.php";
require_once "../session_expiration.php";
require_once "vet_auth.php";


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome Veterinarian.</title>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>
<body>
    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
        <a class="active" href="#">Requests</a>
        <a href="treatment_history.php">Treatment History</a>
        <a href="../logout.php">Logout</a>
    </div>
    </header>
    
    <div class="modal fade" id="MyModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Available requests</h2>

        <div class="filtersAvailable" style="display: inline;">            
            <label>Filter by date:</label>
            <input type="text" class="filterDateAvailable" id="filterDateAvailable"/>
            <label>Filter by animal name:</label>
            <input type="text" class="filterNameAvailable" id="filterNameAvailable"/>
        </div>
    
        
        <div class="container treatment_request_available_listing"> <!-- content from fetch -->
            <table class="table"></table>
            <div class="pages"></div>
        </div>
    </div>

    <div class="container">
        <h2>Accepted requests</h2>

        <div class="filtersAccepted" style="display: inline;">            
            <label>Filter by date:</label>
            <input type="text" class="filterDateAccepted" id="filterDateAccepted"/>
            <label>Filter by animal name:</label>
            <input type="text" class="filterNameAccepted" id="filterNameAccepted"/>
        </div>
        
        <div class="container treatment_request_accepted_listing"> <!-- content from fetch -->
            <table class="table"></table>
            <div class="pages"></div>
        </div>
    </div>
</body>

<script type="text/javascript">
    $(document).ready(function(){
        function updateTableAvailable(curPage) {
            var dateValue = $("#filterDateAvailable").val();
            var nameValue = $("#filterNameAvailable").val();
            
            $.ajax({
                url:"fetch_treatment_requests.php",
                type:"POST",
                data: {date: dateValue, animalName: nameValue, page: curPage},

                success:function(data){ 
                    $(".treatment_request_available_listing").html(data);
                }
            }); 
        }

        function updateTableAccepted(curPage) {
            var vetID = "<?php echo $_SESSION['id'] ?>";
            var dateValue = $("#filterDateAccepted").val();
            var nameValue = $("#filterNameAccepted").val();
            
            $.ajax({
                url:"fetch_treatment_requests.php",
                type:"POST",
                data: {date: dateValue, animalName: nameValue, vetID: vetID, page: curPage},

                success:function(data){ 
                    $(".treatment_request_accepted_listing").html(data);
                }
            }); 
        }
        
        var pageAvailable = 1;
        updateTableAvailable(pageAvailable);

        var pageAccepted = 1;
        updateTableAccepted(pageAccepted);

        
        $(document).on('input', '.filtersAvailable input', function(){
            pageAvailable = 1;
            updateTableAvailable(pageAvailable);
        });

        $(document).on('input', '.filtersAccepted input', function(){
            pageAccepted = 1;
            updateTableAccepted(pageAccepted);
        });


        $(document).on('click', '.paginationLinkAvailable', function(){  
            pageAvailable = $(this).attr("id");  
            updateTableAvailable(pageAvailable);  
        }); 

        $(document).on('click', '.paginationLinkAccepted', function(){  
            pageAccepted = $(this).attr("id");  
            updateTableAccepted(pageAccepted);  
        }); 
        
        function fillModal(id, button){
            var footer = button+'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
            
            $.ajax({
                url:"../treatment_and_request/fetch_treatment_request.php",
                type:"POST",
                data: {treatment_request_id:id},

                success:function(data){ 
                    $(".modal-body").html(data);
                    $(".modal-footer").html(footer);
                    $("#MyModal").modal("show");
                }
            });
        }

        $(document).on('click', '.viewTreatmentRequest', function(){  
            $id = $(this).attr("id");
            var button = '';
            fillModal($id, button);
        });   
        
        $(document).on('click', '.returnTreatmentRequest', function(){  
            $id = $(this).attr("id");
            var button = "<button type='button' class='confirmReturn btn btn-default' data-dismiss='modal'>REMOVE FROM ACCEPTED</button>";
            fillModal($id, button);
        }); 


        $(document).on('click', '.acceptTreatmentRequest', function(){  
            $id = $(this).attr("id");
            var button = "<button type='button' class='confirmAccept btn btn-default' data-dismiss='modal'>ACCEPT REQUEST</button>";
            fillModal($id, button);
        }); 

        $(document).on('click', '.confirmAccept', function(){  
            var vetID = "<?php echo $_SESSION['id']?>";
            
            $.ajax({
                url:"acceptTreatmentRequest.php",
                type:"POST",
                data: {requestID: $id, vetID: vetID},

                success:function(){
                    pageAccepted=1;
                    updateTableAccepted(pageAccepted);
                    pageAvailable = 1;
                    updateTableAvailable(pageAvailable);
                }   
            });
        }); 

        $(document).on('click', '.confirmReturn', function(){  
            
            $.ajax({
                url:"returnTreatmentRequest.php",
                type:"POST",
                data: {requestID: $id},
                
                success:function(){
                    pageAccepted=1;
                    updateTableAccepted(pageAccepted);
                    pageAvailable = 1;
                    updateTableAvailable(pageAvailable);
                }   
            });
        }); 
    });
</script>
</html>