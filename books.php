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

    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["action"])){
        function sql_execute($sql, $success, $error){
            try{
                $sql->execute();
                $feedback = array(true, $success);
            }catch(PDOException $e){
                $feedback = array(false, $error);
            }
            return $feedback;
        }

        if($_POST["action"]=="load-genre"){
            $sql = $conn->prepare("SELECT id, title FROM genres");
            $sql->execute();
            if($sql->rowCount()>0){
                $feedback = array(true, $sql->fetchAll(PDO::FETCH_NUM));
            }else{
                $feedback = array(false, "Couldn't load genres");
            }
        }else if($_POST["action"]=="insert"){
            $sql = $conn->prepare("INSERT INTO books (title, authors, isbn, genre) VALUES (?, ?, ?, ?)");
            $title = trim($_POST["title"]);
            $authors = trim($_POST["authors"]);
            $isbn = trim($_POST["isbn"]);
            $sql->bindParam(1, $title, PDO::PARAM_STR);
            $sql->bindParam(2, $authors, PDO::PARAM_STR);
            $sql->bindParam(3, $isbn, PDO::PARAM_STR);
            $sql->bindParam(4, $_POST["genre"], PDO::PARAM_INT);
            $feedback = sql_execute($sql, "Data recorded successfully", "Couldn't record the data");
        }else if($_POST["action"]=="update"){
            $sql = $conn->prepare("UPDATE books SET title = ?, authors = ?, isbn = ?, genre = ? WHERE id = ?");
            $title = trim($_POST["title"]);
            $authors = trim($_POST["authors"]);
            $isbn = trim($_POST["isbn"]);
            $sql->bindParam(1, $title, PDO::PARAM_STR);
            $sql->bindParam(2, $authors, PDO::PARAM_STR);
            $sql->bindParam(3, $isbn, PDO::PARAM_STR);
            $sql->bindParam(4, $_POST["genre"], PDO::PARAM_INT);
            $sql->bindParam(5, $_POST["id"], PDO::PARAM_INT);
            $feedback = sql_execute($sql, "Data updated successfully", "Couldn't update the record");
        }else if($_POST["action"]=="load-data"){
            $offset = ($_POST["page"]-1)*$_POST["rpp"];
            $search = "%".trim($_POST["search"])."%";
            $sql = $conn->prepare("SELECT * FROM books WHERE concat(title, authors, isbn) LIKE ? LIMIT ?, ?");
            $sql->bindParam(1, $search, PDO::PARAM_STR);
            $sql->bindParam(2, $offset, PDO::PARAM_INT);
            $sql->bindParam(3, $_POST["rpp"], PDO::PARAM_INT);
            $sql->execute();
            if($sql->rowCount()>0){
                $rows = array();
                while ($row = $sql->fetch(PDO::FETCH_NUM)){
                    $call_number = "";
                    while($row[4]!=null){
                        $sql2 = $conn->prepare("SELECT title, parent_genre FROM genres WHERE id = ?");
                        $sql2->bindParam(1, $row[4], PDO::PARAM_INT);
                        $sql2->execute();
                        $genre = $sql2->fetch(PDO::FETCH_NUM);
                        $call_number = strtoupper(substr($genre[0], 0, 3)).$call_number;
                        $row[4] = $genre[1];
                        if($row[4]!=null){
                            $call_number = "-".$call_number;
                        }
                    }
                    $row[4] = $call_number;
                    array_push($rows, $row);
                }
                $sql = $conn->prepare("SELECT * FROM books WHERE concat(title, authors, isbn) LIKE ?");
                $sql->bindParam(1, $search, PDO::PARAM_STR);
                $sql->execute();
                $count = $sql->rowCount();
                $feedback = array(true, $rows, $count);
            }else{
                $feedback = array(false, "No records found");
            }
        }else if($_POST["action"]=="edit-load"){
            $sql = $conn->prepare("SELECT * FROM books WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            $sql->execute();
            if($sql->rowCount()>0){
                $feedback = array(true, $sql->fetch(PDO::FETCH_NUM));
            }else{
                $feedback = array(false, "Could load record details");
            }
        }

        echo json_encode($feedback);
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
            <aside class="d-none d-md-block col-3 col-xl-2 min-vh-100 border-end">
                <div class="py-3 d-flex flex-column sticky-top h-100">
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
                        <div class="col-sm-6"><button type="button" id="add-btn" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i><span>Add record</span></button></div>
                        <div class="col-sm-6 col-md-4">
                            <form action="#" method="post" id="search-form" class="input-group">
                                <input type="text" id="search-field" class="form-control" placeholder="Search" spellcheck="false" autocomplete="off">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <div><span class="text-secondary me-2">Total records found:</span><b id="records-count"></b></div>
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered table-hover table-striped table-sm mt-1">
                            <tr>
                                <th>Sl</th>
                                <th>Title</th>
                                <th>Author</th>
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
    <div id="submit-modal" class="modal fade">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form action="#" method="post" id="data-form" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title record-action"></h5>
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
                    <button type="submit" id="submit-data" class="btn btn-primary record-action" data-type="" data-id=""></button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="assets/public/js/books.js"></script>
<script>
    $(document).ready(function(){
        var url = window.location.href;

        //Load genre
        function load_genre(select){
            $.post(
                url,
                {action: "load-genre"}
            ).done(function(data){
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var rows = feedback[1];
                    $("#genre>option").remove();
                    $("#genre").html("<option value=''>-select-</option>");
                    for(i=0; i<rows.length; i++){
                        var row = rows[i];
                        var option = "<option value='"+row[0]+"'";
                        if(select == row[0]){
                            option += "selected";
                        }
                        option += ">"+row[1]+"</option>";
                        $("#genre").append(option);
                    }
                }else{
                    alert(feedback[1]);
                }
            }).fail(function(data){
                alert("Unexpected error");
            })
        }

        function submit_modal(text, type){
            $("#submit-modal").modal("show");
            $(".record-action").html(text);
            $("#submit-data").attr("data-type", type);
        }

        //Submit modal
        $("#add-btn").click(function(){
            load_genre();
            $("#data-form")[0].reset();
            submit_modal("Add record","insert");
        })

        //Update modal
        $(document).on("click", ".edit-btn", function(){
            var id = $(this).val();
            $.post(
                url,
                {action: "edit-load", id: id}
            ).done(function(data){
                var feedback = JSON.parse(data);
                if(feedback[0]==true){
                    var row = feedback[1];
                    $("#submit-data").attr("data-id", row[0]);
                    $("#isbn").val(row[3]);
                    $("#title").val(row[1]);
                    $("#authors").val(row[2]);
                    load_genre(row[4]);
                    submit_modal("Update record","update");
                }else{
                    alert(feedback[1]);
                }
            })
        })
    })
</script>
</html>