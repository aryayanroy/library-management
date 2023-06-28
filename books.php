<?php
    session_start();
    if(!isset($_SESSION["admin"])){
        header("Location: /library-management");
        die();
    }

    require "config.php";

    //Get admin username
    $sql = $conn->prepare("SELECT username FROM admins WHERE id = ?");
    $sql->bindParam(1, $_SESSION["admin"], PDO::PARAM_STR);
    $sql->execute();
    $username = $sql->fetch(PDO::FETCH_NUM)[0];

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        function sql_execute($sql, $success, $error){
            try{
                $sql->execute();
                $feedback = array(true, $success);
            }catch(PDOException $e){
                $feedback = array(false, $error);
            }
            return $feedback;
        }
        $output = array();
        if(in_array($_POST["action"], ["load-data", "search"])){
            $statement = "WITH RECURSIVE genre_hierarchy AS (SELECT id, title, parent_genre, title AS genre_hierarchy FROM genres WHERE parent_genre IS NULL UNION ALL SELECT g.id, g.title, g.parent_genre, CONCAT_WS(',', gh.genre_hierarchy, g.title) AS genre_hierarchy FROM genres g INNER JOIN genre_hierarchy gh ON g.parent_genre = gh.id) SELECT b.id, b.title, b.authors, b.isbn, gh.genre_hierarchy FROM books b JOIN genre_hierarchy gh ON b.genre = gh.id ";
        }
        if($_POST["action"]=="load-genre"){     //Load genre
            $sql = $conn->prepare("SELECT id, title FROM genres ORDER BY title");
            $sql->execute();
            if($sql->rowCount()>0){
                $output[0] = true;
                $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
            }
        }elseif($_POST["action"]=="load-data"){     //Load data
            $offset = ($_POST["page"]-1)*25;
            $sql = $conn->prepare($statement."LIMIT ?, 25");
            $sql->bindParam(1, $offset, PDO::PARAM_INT);
            $output = sql_execute($sql, null, "Couldn't fetch records");
            if($output[0] = true){
                if($sql->rowCount()>0){
                    $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
                    foreach($output[1] as &$row){
                        $genres = explode(",", $row[4]);
                        foreach($genres as &$genre){
                            $genre = strtoupper(substr($genre, 0, 3));
                        }
                        $row[4] = implode("-", $genres);
                    }
                    $sql = $conn->prepare("SELECT COUNT(*) FROM books");
                    $sql->execute();
                    $output[2] = $sql->fetch(PDO::FETCH_NUM)[0];
                }else{
                    $output[0] = false;
                    $output[1] = "No records found";
                }
            }
        }elseif($_POST["action"]=="insert"){    //Insert record
            $sql = $conn->prepare("INSERT INTO books (title, authors, isbn, genre) VALUES (?, ?, ?, ?)");
            $sql->bindParam(1, $_POST["title"], PDO::PARAM_STR);
            $sql->bindParam(2, $_POST["authors"], PDO::PARAM_STR);
            $sql->bindParam(3, $_POST["isbn"], PDO::PARAM_STR);
            $sql->bindParam(4, $_POST["genre"], PDO::PARAM_INT);
            $output = sql_execute($sql, "Data recorded successfully", "Couldn't record the data");
        }elseif($_POST["action"]=="load-edit"){
            $sql = $conn->prepare("SELECT * FROM books WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            $output = sql_execute($sql, null, "Couldn't load the book data");
            if($output[0]=true){
                $output[1] = $sql->fetch(PDO::FETCH_NUM);
            }
        }elseif($_POST["action"]=="update"){
            $sql = $conn->prepare("UPDATE books SET title = ?, authors = ?, isbn = ?, genre = ? WHERE id = ?");
            $sql->bindParam(1, $_POST["title"], PDO::PARAM_STR);
            $sql->bindParam(2, $_POST["authors"], PDO::PARAM_STR);
            $sql->bindParam(3, $_POST["isbn"], PDO::PARAM_STR);
            $sql->bindParam(4, $_POST["genre"], PDO::PARAM_INT);
            $sql->bindParam(5, $_POST["id"], PDO::PARAM_INT);
            $output = sql_execute($sql, "Record updated successfully", "Couldn't update the record");
        }elseif($_POST["action"]=="delete"){
            $sql = $conn->prepare("DELETE FROM books WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            $output = sql_execute($sql, "Record deleted successfully", "Couldn't delete the record");
        }elseif($_POST["action"]=="search"){
            $search = "%".$_POST["search"]."%";
            $sql = $conn->prepare($statement."WHERE CONCAT(b.title,b.authors,b.isbn) LIKE ?");
            $sql->bindParam(1, $search, PDO::PARAM_STR);
            $output = sql_execute($sql, null, "Couldn't fetch records");
            if($output[0]==true){
                if($sql->rowCount()>0){
                    $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
                    foreach($output[1] as &$row){
                        $genres = explode(",", $row[4]);
                        foreach($genres as &$genre){
                            $genre = strtoupper(substr($genre, 0, 3));
                        }
                        $row[4] = implode("-", $genres);
                    }
                }else{
                    $output[0] = false;
                    $output[1] = "No records found for: ".$_POST["search"];
                }
            }
        }
        echo json_encode($output);
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books | Library Management</title>
    <link rel="shortcut icon" href="assets/public/images/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/public/css/style.css">
</head>
<body>
    <div class="container-xxl">
        <div class="row">
            <aside class="d-none d-md-block col-3 col-xl-2 sticky-top vh-100 border-end">
                <div class="py-3 d-flex flex-column h-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 55 64" width=50 class="mx-auto"><path d="M55 26.5v23.8c0 1.2-.4 2.2-1.3 3.2-.9.9-1.9 1.5-3.2 1.6-3.5.4-6.8 1.3-10.1 2.6S34 60.8 31 62.9a6.06 6.06 0 0 1-3.5 1.1 6.06 6.06 0 0 1-3.5-1.1c-3-2.1-6.1-3.9-9.4-5.2s-6.7-2.2-10.1-2.6c-1.3-.2-2.3-.7-3.2-1.6-.9-1-1.3-2-1.3-3.2V26.5c0-1.3.5-2.4 1.4-3.2s2-1.2 3.1-1c4 .6 8 2 11.9 4 3.9 2.1 7.6 4.8 11.1 8.1 3.5-3.3 7.2-6 11.1-8.1s7.9-3.4 11.9-4c1.2-.2 2.2.1 3.1 1 .9.8 1.4 1.9 1.4 3.2z" fill="#004d40"/><path d="M39.5 11.8c0 3.3-1.1 6.1-3.4 8.4s-5.1 3.4-8.4 3.4-6.1-1.1-8.4-3.4-3.4-5.1-3.4-8.4 1.1-6.1 3.4-8.4S24.4 0 27.7 0s6.1 1.1 8.4 3.4 3.4 5.1 3.4 8.4z" fill="#e65100"/></svg>
                    <hr>
                    <nav class="nav nav-pills flex-column flex-grow-1">
                        <a href="dashboard" class="nav-link link-dark"><i class="fa-solid fa-gauge me-2"></i><span>Dashboard</span></a>
                        <a href="borrows" class="nav-link link-dark"><i class="fa-solid fa-list-check me-2"></i><span>Borrows</span></a>
                        <a href="books" class="nav-link active"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                        <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                        <a href="shelves" class="nav-link link-dark"><i class="fa-solid fa-layer-group me-2"></i><span>Shelves</span></a>
                        <a href="genres" class="nav-link link-dark"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
                        <a href="settings" class="nav-link link-dark"><i class="fa-solid fa-cog me-2"></i><span>Settings</span></a>
                    </nav>
                    <hr>
                    <div class="dropup-start dropup">
                        <button type="button" class="btn w-100 dropdown-toggle text-truncate" data-bs-toggle="dropdown">Hello, <?php echo $username; ?></button>
                        <ul class="dropdown-menu">
                            <li><a href="logout" class="dropdown-item">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </aside>
            <main class="col-md-9 col-xl-10 px-0">
                <header class="navbar bg-white border-bottom sticky-top">
                    <div class="container-fluid">
                        <a href="/" class="navbar-brand"><h3 class="mb-0"><i class="fa-solid fa-landmark me-3"></i><span class="d-none d-md-inline">Library Management</span></h3></a>
                        <button type="button" class="btn d-md-none" data-bs-toggle="offcanvas" data-bs-target="#aside-right"><i class="fa-solid fa-bars fa-xl"></i></button>
                    </div>
                </header>
                <article class="container-fluid py-3">
                    <div class="row g-3 justify-content-between mb-3">
                        <div class="col-sm-6"><button type="button" id="insert-btn" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i><span>Add book</span></button></div>
                        <div class="col-sm-6 col-md-4">
                            <form action="#" method="post" id="search-form" class="input-group">
                                <input type="text" id="search-field" class="form-control" placeholder="Search" spellcheck="false" autocomplete="off">
                                <button type="submit" id="search-btn" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <div><span class="text-secondary me-2">Total records found:</span><b id="records-count"></b></div>
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered table-hover table-striped table-sm mt-1">
                            <tr>
                                <th>Sl</th>
                                <th>Title</th>
                                <th>Author(s)</th>
                                <th>ISBN</th>
                                <th>Availability</th>
                                <th>Call Number</th>
                                <th colspan="2">Action</th>
                            </tr>
                        </table>
                    </div>
                    <ul id="pagination" class="pagination justify-content-center"></ul>
                </article>
            </main>
        </div>
    </div>
    <!-- off canvas -->
    <aside id="aside-right" class="offcanvas offcanvas-end">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-truncate">Hello, <?php echo $username; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav nav-pills flex-column flex-grow-1">
                <a href="dashboard" class="nav-link link-dark"><i class="fa-solid fa-gauge me-2"></i><span>Dashboard</span></a>
                <a href="borrows" class="nav-link link-dark"><i class="fa-solid fa-list-check me-2"></i><span>Borrows</span></a>
                <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                <a href="shelves" class="nav-link link-dark"><i class="fa-solid fa-layer-group me-2"></i><span>Shelves</span></a>
                <a href="genres" class="nav-link link-dark"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
                <a href="settings" class="nav-link link-dark"><i class="fa-solid fa-cog me-2"></i><span>Settings</span></a>
            </nav>
        </div>
        <div class="p-3 border-top">
            <a href="logout" class="btn btn-light w-100">Logout</a>
        </div>
    </aside>
    <div id="data-modal" class="modal fade">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form action="#" method="post" id="data-form" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title action-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control" placeholder="XX-XXXX-XXX-X" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" name="title" class="form-control" spellcheck="false" autocomplete="off" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="authors" class="form-label">Author(s)</label>
                            <input type="text" id="authors" name="authors" class="form-control" spellcheck="false" autocomplete="off" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="genre" class="form-label">Genres</label>
                            <select id="genre" name="genre" class="form-select" required>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-btn" class="btn btn-primary"></button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    var url = window.location.href;
    var loading = "<i class='fas fa-circle-notch fa-spin fa-xl'></i>";

    function append(table, offset, title, authors, isbn, call_number, id){
        table.append("<tr><td class='text-center'>"+(i+offset)+"</td><td>"+title+"</td><td>"+authors+"</td><td class='text-nowrap'>"+isbn+"</td><td></td><td class='text-nowrap'>"+call_number+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+id+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+id+"><i class='fa-solid fa-trash'></i></button></td></tr>");
    }

    function load_data(page){
        if(!page){
            if($(".active[data-page]").data("page")){
                page = $(".active[data-page]").data("page");
            }else{
                page = 1;
            }
        }
        var table = $("#data-table");
        table.find("tr:not(:first-child)").remove();
        table.append("<tr><td colspan='7' class='text-center'>"+loading+"</td></tr>");
        $.post(
            url,
            {action: "load-data", page: page}
        ).always(function(){
            table.find("tr:nth-child(2)").remove();
        }).done(function(data){
            var feedback = JSON.parse(data);
            var total_records = feedback[2];
            var offset = (page-1)*25+1;
            if(feedback[0]==true){
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    append(table, offset, row[1], row[2], row[3], row[4], row[0]);
                }
                $("#records-count").html(total_records);
                var total_pages = Math.ceil(total_records/25);
                $("#pagination>*").remove();
                for(i=1; i<=total_pages; i++){
                    $("#pagination").append("<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>");
                }
                $("[data-page='"+page+"']").addClass("active");
            }else{
                table.append("<tr><td colspan='7' class='text-center'>"+feedback[1]+"</td></tr>")
                $("#records-count").html(0);
            }
        })
    }

    //data modal
    function data_modal(element, action, title, button, selected){
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        $.post(
            url,
            {action: "load-genre"}
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var select = $("#genre");
                select.find("option").remove();
                if(action=="insert"){
                    select.append("<option value=''>-select-</option>");
                }
                var rows = feedback[1];
                for(i=0; i<rows.length; i++){
                    var row = rows[i];
                    var option = "<option value='"+row[0]+"'>"+row[1]+"</option>";
                    select.append(option);
                }
                $("#genre>option[value="+selected+"]").prop("selected", true);
                $(".action-title").html(title);
                $("#submit-btn").html(button).data("action",action);
                $("#data-modal").modal("show");
            }else{
                alert(feedback[1]);
            }
        })
    }

    load_data();
    
    //pagination
    $(document).on("click", ".page-link", function(e){
        e.preventDefault();
        load_data($(this).data("page"));
    })

    $("#insert-btn").click(function(){
        $("#data-form")[0].reset();
        data_modal($(this), "insert","New record","Add record");
    })

    $(document).on("click",".edit-btn", function(){
        var element = $(this);
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        var id = element.val();
        $.post(
            url,
            {action: "load-edit", id: id}
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                var data = feedback[1];
                data_modal(element, "update","Edit record","Update", data[4]);
                $("#submit-btn").data("id",data[0]);
                $("#isbn").val(data[3]);
                $("#title").val(data[1]);
                $("#authors").val(data[2]);
            }else{
                alert(feedback[1]);
            }
        })
    })

    $("#data-form").submit(function(e){
        e.preventDefault();
        var element= $("#submit-btn");
        var initial_text = element.html();
        element.prop("disabled", true).html(loading);
        var action = element.data("action");
        var form_data = $(this).serializeArray();
        form_data.push({name: "action", value: action});
        if(action=="update"){
            form_data.push({name: "id", value: element.data("id")});
        }
        form_data = $.param(form_data);
        $.post(
            url,
            form_data
        ).always(function(){
            element.prop("disabled", false).html(initial_text);
        }).done(function(data){
            var feedback = JSON.parse(data);
            if(feedback[0]==true){
                $("#data-form")[0].reset();
                $("#data-modal").modal("hide");
                load_data();
            }
            alert(feedback[1]);
        })
    })

    //delete record
    $(document).on("click",".delete-btn",function(){
        var element = $(this);
        var initial_text = element.html();
        var id = element.val();
        if(confirm("Are you sure want to delete the record? This will also delete it's all related records.")){
            element.prop("disabled", true).html(loading);
            $.post(
                url,
                {action: "delete", id: id}
            ).always(function(){
                element.prop("disabled", false).html(initial_text);
            }).done(function(data){
                var feedback = JSON.parse(data);
                alert(feedback[1]);
                if(feedback[0]==true){
                    load_data();
                }
            })
        }
    })

    $("#search-form").submit(function(e){
        e.preventDefault();
        var search = $.trim($("#search-field").val());
        if(search!=""){
            var element = $("#search-btn");
            var initial_text = element.html();
            element.prop("disabled", true).html(loading);
            $.post(
                url,
                {action: "search", search: search}
            ).done(function(data){
                var table = $("#data-table");
                table.find("tr:not(:first-child)").remove();
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var rows = feedback[1];
                    var count = rows.length;
                    for(i=0; i<count; i++){
                        var row = rows[i];
                        append(table, 1, row[1], row[2], row[3], row[4], row[0]);
                    }
                    $("#records-count").html(count);
                }else{
                    table.append("<tr><td colspan='7' class='text-center'>"+feedback[1]+"</td></tr>")
                    $("#records-count").html(0);
                }
            }).always(function(){
                element.prop("disabled", false).html(initial_text);
            })
        }else{
            load_data();
        }
    })
    
})
</script>
</html>