<!DOCTYPE html>
<html lang="en">
	<meta charset="UTF-8">
    <title>Animals</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles2.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>       
        .landing-page{
        position: relative;
        width: 100%;
        height: var(--navbar-height);
    }
    .animalBox{
        width:300px;float:left;text-align:center;margin:40px;
        padding:10px;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        background: #F0F0F0;
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

        <div class="info">
            <div class="filters" style="display: inline; display: flex; justify-content: center; margin-top: 5px;">
                <label>Filter by species: </label>
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
            <div class="container animal_listing"> <!-- content from fetch -->
                <table class="table"></table>
                <div class="pages"></div>
            </div>
        </div>
    </body>
    <script type="text/javascript">

        $(document).ready(function(){

            function updateTable(curPage) {
                var typeValue = $("#filterType").val();
                var nameValue = $("#filterName").val();
                
                $.ajax({
                    url:"find_animals.php",
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