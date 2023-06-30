<?php
    session_start();
    if(!isset($_SESSION["admin"])){
        header("Location: /library-management");
        die();
    }
    require "config.php";
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
        if(in_array($_POST["action"], ["load-data", "search"])){
            $statement = "SELECT p.title, c.title, COALESCE(book_counts.book_count, 0), p.id FROM genres p LEFT JOIN genres c ON p.id = c.parent_genre LEFT JOIN( SELECT genre, COUNT(*) AS book_count FROM books GROUP BY genre) AS book_counts ON p.id = book_counts.genre ";
        }
        $output =  array();
        if($_POST["action"]=="load-genre"){
            $sql = $conn->prepare("SELECT id, title FROM genres");
            $sql->execute();
            if($sql->rowCount()>0){
                $output[0] = true;
                $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
            }
        }elseif($_POST["action"]=="load-data"){
            $offset = ($_POST["page"]-1)*25;
            $sql = $conn->prepare($statement."LIMIT ?, 25");
            $sql->bindParam(1, $offset, PDO::PARAM_INT);
            $output = sql_execute($sql, null, "Couldn't fetch records");
            if($output[0] = true){
                if($sql->rowCount()>0){
                    $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
                    $sql = $conn->prepare("SELECT COUNT(*) FROM genres AS p LEFT JOIN genres AS c ON p.id = c.parent_genre");
                    $sql->execute();
                    $output[2] = $sql->fetch(PDO::FETCH_NUM)[0];
                }else{
                    $output[0] = false;
                    $output[1] = "No records found";
                }
            }
        }elseif($_POST["action"]=="insert"){
            $title = trim($_POST["genre"]);
            if($_POST["parent-genre"]==0){
                $parent_genre = null;
            }else{
                $parent_genre = $_POST["parent-genre"];
            }
            $sql = $conn->prepare("INSERT INTO genres (title, parent_genre) VALUES (?, ?)");
            $sql->bindParam(1, $title, PDO::PARAM_STR);
            $sql->bindParam(2, $parent_genre, PDO::PARAM_INT);
            $output = sql_execute($sql, "Data recorded successfully", "Couldn't record data");
        }elseif($_POST["action"]=="update"){  //Edit record
            $sql = $conn->prepare("UPDATE genres SET title = ? WHERE id = ?");
            $sql->bindParam(1, $_POST["title"], PDO::PARAM_STR);
            $sql->bindParam(2, $_POST["id"], PDO::PARAM_INT);
            $output = sql_execute($sql, "Record updated successfully", "Couldn't update the record");
        }elseif($_POST["action"]=="delete"){    //Delete record
            $sql = $conn->prepare("DELETE FROM genres WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            $output = sql_execute($sql, "Record deleted successfully", "Couldn't delete the record");
        }elseif($_POST["action"]=="search"){
            $search = "%".$_POST["search"]."%";
            $sql = $conn->prepare($statement."WHERE p.title LIKE ?");
            $sql->bindParam(1, $search, PDO::PARAM_STR);
            $output = sql_execute($sql, null, "Couldn't fetch records");
            if($output[0]==true){
                if($sql->rowCount()>0){
                    $output[1] = $sql->fetchAll(PDO::FETCH_NUM);
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
    <title>Genres | Library Management</title>
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
                        <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                        <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                        <a href="genres" class="nav-link active"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
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
                        <div class="col-sm-6"><button type="button" id="insert-btn" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i><span>Add Genre</span></button></div>
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
                                <th>Genres</th>
                                <th>Subgenre</th>
                                <th>Books</th>
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
                <a href="genres" class="nav-link link-dark"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
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
                    <h5 class="modal-title action-text"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="parent-genre" class="form-label">Parent Genre</label>
                            <select id="parent-genre" name="parent-genre" class="form-select">
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="genre" class="form-label">Genre</label>
                            <input type="text" id="genre" name="genre" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-btn" class="btn btn-primary action-text"></button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
$(document).ready((function(){var t=window.location.href,a="<i class='fas fa-circle-notch fa-spin fa-xl'></i>";function e(t,a,e,n,r,l){t.append("<tr><td class='text-center'>"+(i+a)+"</td><td>"+e+"</td><td>"+n+"</td><td>"+r+"</td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+l+"' data-title='"+e+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+l+"><i class='fa-solid fa-trash'></i></button></td></tr>")}function n(n){n||(n=$(".active[data-page]").data("page")?$(".active[data-page]").data("page"):1);var r=$("#data-table");r.find("tr:not(:first-child)").remove(),r.append("<tr><td colspan='6' class='text-center'>"+a+"</td></tr>"),$.post(t,{action:"load-data",page:n}).always((function(){r.find("tr:nth-child(2)").remove()})).done((function(t){var a=JSON.parse(t),l=a[2],o=25*(n-1)+1,d=$("#records-count");if(1==a[0]){var s=a[1];for(i=0;i<s.length;i++){var c=s[i];null==c[1]&&(c[1]="-"),e(r,o,c[0],c[1],c[2],c[3])}d.html(l);var p=Math.ceil(l/25);for($("#pagination>*").remove(),i=1;i<=p;i++)$("#pagination").append("<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>");$("[data-page='"+n+"']").addClass("active")}else r.append("<tr><td colspan='5' class='text-center'>"+a[1]+"</td></tr>"),d.html(0)}))}n(),$(document).on("click",".page-link",(function(t){t.preventDefault(),n($(this).data("page"))})),$("#insert-btn").click((function(){$(".action-text").html("Add record");var e=$(this),n=e.html();e.prop("disabled",!0).html(a),$.post(t,{action:"load-genre"}).always((function(){e.prop("disabled",!1).html(n)})).done((function(t){var a=$("#parent-genre");a.html("<option value='0'>None</option>");var e=JSON.parse(t);if(1==e[0]){var n=e[1];for(i=0;i<n.length;i++){var r=n[i],l="<option value='"+r[0]+"'";a&&a==r[0]&&(l+="selected"),l+=">"+r[1]+"</option>",a.append(l)}}$("#data-modal").modal("show")}))})),$("#data-form").submit((function(e){e.preventDefault();var i=$("#submit-btn"),r=i.html();i.prop("disabled",!0).html(a);var l=$(this).serializeArray();l.push({name:"action",value:"insert"}),l=$.param(l),$.post(t,l).always((function(){i.prop("disabled",!1).html(r)})).done((function(t){var a=JSON.parse(t);1==a[0]&&($("#data-form")[0].reset(),$("#data-modal").modal("hide"),n()),alert(a[1])}))})),$(document).on("click",".edit-btn",(function(){var e=prompt("Enter the Genre title",$(this).data("title"));if(e)if(""!=(e=$.trim(e))){var i=$(this),r=i.html();i.prop("disabled",!0).html(a);var l=i.val();$.post(t,{action:"update",title:e,id:l}).always((function(){i.prop("disabled",!1).html(r)})).done((function(t){var a=JSON.parse(t);alert(a[1]),n()}))}else alert("Title cannot be empty")})),$(document).on("click",".delete-btn",(function(){var e=$(this),i=e.html(),r=e.val();confirm("Are you sure want to delete the record? This will also delete it's subgenere.")&&(e.prop("disabled",!0).html(a),$.post(t,{action:"delete",id:r}).always((function(){e.prop("disabled",!1).html(i)})).done((function(t){var a=JSON.parse(t);alert(a[1]),1==a[0]&&n()})))})),$("#search-form").submit((function(r){r.preventDefault();var l=$.trim($("#search-field").val());if(""!=l){var o=$("#search-btn"),d=o.html();o.prop("disabled",!0).html(a),$.post(t,{action:"search",search:l}).always((function(){o.prop("disabled",!1).html(d)})).done((function(t){var a=$("#data-table");a.find("tr:not(:first-child)").remove(),$("#pagination>*").remove();var n=JSON.parse(t);if(1==n[0]){var r=n[1],l=r.length;for(i=0;i<l;i++){var o=r[i];null==o[1]&&(o[1]="-"),e(a,1,o[0],o[1],o[2],o[3])}$("#records-count").html(l)}else a.append("<tr><td colspan='5' class='text-center'>"+n[1]+"</td></tr>"),$("#records-count").html(0)}))}else n()}))}));
</script>
</html>