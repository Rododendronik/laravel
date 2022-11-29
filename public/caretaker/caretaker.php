<?php
require_once "../session_expiration.php";
require_once "caretaker_auth.php";
require_once "../config.php";

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animals</title>
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>

    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a class="active" href="#home">Animals</a>
            <a href="volunteers.php">Volunteers</a>
            <a href="reservations.php">Reservation System</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="filters" style="display: inline;">
            <label>Filter by species:</label>
            <select name="filterType" id="filterType">
                <option value="Any" selected="">Any</option>
                <option value="Dog">Dog</option>
                <option value="Cat">Cat</option>
                <option value="Bunny">Bunny</option>
                <option value="Hamster">Hamster</option>
                <option value="Guinea Pig">Guinea Pig</option>
            </select>
            <label>Filter by name:</label>
            <input type="text" class="filterName" id="filterName"/>
        </div>

        <a href="add_animal.php" class="btn btn-info btn-s" >ADD ANIMAL</a>
    <div>


    <div class="container animal_listing"> <!-- content from fetch -->
        <table class="table"></table>
        <div class="pages"></div>
    </div>
    
</body>

<script type="text/javascript">
    $(document).ready(function(){

        function updateTable(curPage) {
            var typeValue = $("#filterType").val();
            var nameValue = $("#filterName").val();
            
            $.ajax({
                url:"fetch_animals.php",
                type:"POST",
                data: {type: typeValue, name: nameValue, page: curPage},
                
                success:function(data){ 
                    $(".animal_listing").html(data);
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
</script>
</html>