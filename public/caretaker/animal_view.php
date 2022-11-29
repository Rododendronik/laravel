<?php
// Initialize the session

require_once "../config.php";
require_once "animal_fill_out.php";
require_once "../treatment_and_request/fetch_treatment.php";
require_once "../treatment_and_request/fetch_treatment_request.php";
require_once "../session_expiration.php";
require_once "caretaker_auth.php";

if(!isset($_GET["animal"])){
    header("location: index.php");
}

$animalID = $_GET["animal"];
$caretakerID = $_SESSION['id'];

$name = $type = $age = $image = $description = "";
fill_animal($animalID ,$name, $type, $age, $image, $description);

?>

<!DOCTYPE html>
<html lang="en">
	<meta charset="UTF-8">
    <title>Animal</title>
    <link rel="stylesheet" href="../styles2.css">
    <link rel="stylesheet" href="../styles.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>       
        .center{
            text-align: center;
        }
        .wrapper{
            width:700px;
        }
    </style>

    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a class="active" href="caretaker.php">Animals</a>
            <a href="volunteers.php">Volunteers</a>
            <a href="reservations.php">Reservation System</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <body>
        <div class="modal fade" id="viewModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="confirmDeleteRequest btn btn-default" data-dismiss="modal">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteAnimalModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body"><p>Are you sure you want to delete this animal?</p></div>
                    <div class="modal-footer">
                        <button type="button" class="confirmDeleteAnimal btn btn-default" data-dismiss="modal">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <?php
                if(!empty($_GET['msg']))
                    echo "<div class='alert alert-success alert-dismissible'> Treatment request created <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
            ?>
        </div>
        <div class="animal-profile">
            <?php
                echo "<h1> $name - $type</h1>";
                echo "<img src='../$image'>";
                echo "<h2>$age Years</h2>";
                echo "<p>$description</p>";
            ?>
            <div class= "wrapper center">
                <a href=<?php echo "animal_edit.php?animalID=".$animalID ?> class="btn btn-info btn-s" >EDIT</a>
                <button class="deleteAnimal btn btn-info btn-s">DELETE</button>
            </div>
        </div>

        <div class="container">
            <div class="filters" style="display: inline;">
                <label>Treatment Requests/Treatments:</label>
                <select name="filterType" id="filterType">
                    <option value="Treatment Requests" selected="">Treatment Requests</option>
                    <option value="Treatments">Treatments</option>
                </select>
                
                <label>Filter by date:</label>
                <input type="text" class="filterDate" id="filterDate"/>

                <label>Filter by treatment name:</label>
                <input type="text" class="filterName" id="filterName"/>
            </div>
        <div>


        <div class="container treatment_listing"> <!-- content from fetch -->
            <table class="table"></table>
            <div class="pages"></div>
        </div>

        <div class="container wrapper center">
            <h2>Register a treatment request in the system</h2>
            <form action="create_treatment_request.php?caretakerID=<?php echo $caretakerID ?>&animalID=<?php echo $animalID ?>" method="post" autocomplete="off">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control <?php echo (isset($_GET['name_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback">Must be filled out</span>
                </div>

                <label>Description</label>
                <div class="form-group">
                    <textarea name="description" cols=100><?php if(isset($_POST['description'])) {  echo htmlentities ($_POST['description']); }?></textarea>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
                
            </form>
        </div>   
    </body>

    <script type="text/javascript">
        $(document).ready(function(){
            function updateTable(curPage) {
                var typeValue = $("#filterType").val();
                var idValue = "<?php echo $animalID ?>";
                var nameValue = $("#filterName").val();
                var dateValue = $("#filterDate").val();
                
                if(typeValue == "Treatments"){
                    $.ajax({
                        url:"fetch_treatments.php",
                        type:"POST",
                        data: {name: nameValue, datetime: dateValue, id: idValue, page: curPage},

                        success:function(data){ 
                            $(".treatment_listing").html(data);
                        }
                    });
                }
                else{
                    $.ajax({
                        url:"fetch_treatment_requests.php",
                        type:"POST",
                        data: {name: nameValue, date: dateValue, id: idValue, page: curPage},

                        success:function(data){ 
                            $(".treatment_listing").html(data);
                        }
                    });
                }
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
            
            $(document).on('click', '.viewTreatmentRequest', function(){  
                $id = $(this).attr("id");

                $.ajax({
                    url:"../treatment_and_request/fetch_treatment_request.php",
                    type:"POST",
                    data: {treatment_request_id: $id},

                    success:function(data){ 
                        $(".modal-body").html(data);
                        $("#viewModal").modal("show");
                    }
                });
            });

            $(document).on('click', '.viewTreatment', function(){  
                $id = $(this).attr("id");

                $.ajax({
                    url:"../treatment_and_request/fetch_treatment.php",
                    type:"POST",
                    data: {treatment_id: $id},

                    success:function(data){ 
                        $(".modal-body").html(data);
                        $("#viewModal").modal("show");
                    }
                });
            });

            $(document).on('click', '.deleteTreatmentRequest', function(){  
                $id = $(this).attr("id");

                $.ajax({
                    url:"../treatment_and_request/fetch_treatment_request.php",
                    type:"POST",
                    data: {treatment_request_id: $id},

                    success:function(data){ 
                        $(".modal-body").html(data);
                        $("#deleteModal").modal("show");
                    }
                });
            });

            $(document).on('click', '.confirmDeleteRequest', function(){  
                $.ajax({
                    url:"deleteRequest.php",
                    type:"POST",
                    data: {requestID:$id},

                    success:function(data){ 
                        var page = 1;
                        updateTable(page);
                    }
                });
            });

            $(document).on('click', '.deleteAnimal', function(){  
                $("#deleteAnimalModal").modal("show");
            });

            $(document).on('click', '.confirmDeleteAnimal', function(){  
                var animalID = "<?php echo $animalID ?>";

                $.ajax({
                    url:"deleteAnimal.php",
                    type:"POST",
                    data: {animalID:animalID},

                    success:function(data){ 
                        window.location.href='caretaker.php'; 
                    }
                });
            });

        });
    </script>
</html>