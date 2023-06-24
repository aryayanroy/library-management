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
        $feedback =  array();
        if($_POST["action"]=="load-genre"){     //Load genre
            $sql = $conn->prepare("SELECT id, title FROM genres");
            $sql->execute();
            if($sql->rowCount()>0){
                $feedback[0] = true;
                $feedback[1] = $sql->fetchAll(PDO::FETCH_NUM);
            }
        }elseif($_POST["action"]=="load-data"){     //Load record
            $offset = ($_POST["page"]-1)*25;
            $sql = $conn->prepare("SELECT parent.title, child.title, parent.id AS child_title FROM genres AS parent LEFT JOIN genres AS child ON parent.id = child.parent_genre LIMIT ?, 25");
            $sql->bindParam(1, $offset, PDO::PARAM_INT);
            $feedback = sql_execute($sql, null, "Couldn't fetch records");
            if($feedback[0] = true){
                if($sql->rowCount()>0){
                    $feedback[1] = $sql->fetchAll(PDO::FETCH_NUM);
                    $sql = $conn->prepare("SELECT COUNT(*) AS row_count FROM genres AS parent LEFT JOIN genres AS child ON parent.id = child.parent_genre");
                    $sql->execute();
                    $feedback[2] = $sql->fetch(PDO::FETCH_NUM)[0];
                }else{
                    $feedback[0] = false;
                    $feedback[1] = "No records found";
                }
            }
        }elseif($_POST["action"]=="insert"){    //Insert record
            $title = trim($_POST["genre"]);
            if($_POST["parent-genre"]==0){
                $parent_genre = null;
            }else{
                $parent_genre = $_POST["parent-genre"];
            }
            $sql = $conn->prepare("INSERT INTO genres (title, parent_genre) VALUES (?, ?)");
            $sql->bindParam(1, $title, PDO::PARAM_STR);
            $sql->bindParam(2, $parent_genre, PDO::PARAM_INT);
            $feedback = sql_execute($sql, "Data recorded successfully", "Couldn't record data");
        }elseif($_POST["action"]=="edit"){  //Edit record
            $sql = $conn->prepare("UPDATE genres SET title = ? WHERE id = ?");
            $sql->bindParam(1, $_POST["title"], PDO::PARAM_STR);
            $sql->bindParam(2, $_POST["id"], PDO::PARAM_INT);
            $feedback = sql_execute($sql, "Record updated successfully", "Couldn't update the record");
        }elseif($_POST["action"]=="delete"){    //Delete record
            $sql = $conn->prepare("DELETE FROM genres WHERE id = ?");
            $sql->bindParam(1, $_POST["id"], PDO::PARAM_INT);
            $feedback = sql_execute($sql, "Record deleted successfully", "Couldn't delete the record");
        }elseif($_POST["action"]=="search"){
            $search = "%".$_POST["search"]."%";
            $sql = $conn->prepare("SELECT parent.title, child.title, parent.id AS child_title FROM genres AS parent LEFT JOIN genres AS child ON parent.id = child.parent_genre WHERE parent.title LIKE ?");
            $sql->bindParam(1, $search, PDO::PARAM_STR);
            $feedback = sql_execute($sql, null, "Couldn't fetch records");
            if($feedback[0]==true){
                if($sql->rowCount()>0){
                    $feedback[1] = $sql->fetchAll(PDO::FETCH_NUM);
                }else{
                    $feedback[0] = false;
                    $feedback[1] = "No records found for: ".$_POST["search"];
                }
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
                        <a href="shelves" class="nav-link link-dark"><i class="fa-solid fa-layer-group me-2"></i><span>Shelves</span></a>
                        <a href="genres" class="nav-link active"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
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
    $(document).ready(function(){var l=window.location.href,o="<i class='fas fa-circle-notch fa-spin fa-xl'></i>";function d(r){r=r||($(".active[data-page]").data("page")?$(".active[data-page]").data("page"):1);var s=$("#data-table");s.find("tr:not(:first-child)").remove(),s.append("<tr><td colspan='6' class='text-center'>"+o+"</td></tr>"),$.post(l,{action:"load-data",page:r}).done(function(t){var a=JSON.parse(t),t=a[2],e=25*(r-1)+1;if(1==a[0]){var n=a[1];for(i=0;i<n.length;i++){var l=n[i];null==l[1]&&(l[1]="-"),s.append("<tr><td class='text-center'>"+(i+e)+"</td><td>"+l[0]+"</td><td>"+l[1]+"</td><td></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+l[2]+"' data-title='"+l[0]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+l[2]+"><i class='fa-solid fa-trash'></i></button></td></tr>")}$("#records-count").html(t);var o=Math.ceil(t/25),d="";for(i=1;i<=o;i++)d+="<li class='page-item'><a href='#' class='page-link' data-page="+i+">"+i+"</a></li>";$("#pagination").html(d),$("[data-page='"+r+"']").addClass("active")}else s.append("<tr><td colspan='5' class='text-center'>"+a[1]+"</td></tr>"),$("#records-count").html(0)}).always(function(){s.find("tr:nth-child(2)").remove()})}d(),$("#insert-btn").click(function(){$(".action-text").html("Add record");var t=$(this),a=t.html();t.prop("disabled",!0).html(o),$.post(l,{action:"load-genre"}).done(function(t){var a=$("#parent-genre");a.html("<option value='0'>None</option>");t=JSON.parse(t);if(1==t[0]){var e=t[1];for(i=0;i<e.length;i++){var n=e[i],l="<option value='"+n[0]+"'";a&&a==n[0]&&(l+="selected"),l+=">"+n[1]+"</option>",a.append(l)}}$("#data-modal").modal("show")}).always(function(){t.prop("disabled",!1).html(a)})}),$("#data-form").submit(function(t){t.preventDefault();var a=$("#submit-btn"),e=a.html();a.prop("disabled",!0).html(o);t=$(this).serializeArray();t.push({name:"action",value:"insert"}),t=$.param(t),$.post(l,t).done(function(t){console.log(t);t=JSON.parse(t);1==t[0]&&($("#data-form")[0].reset(),$("#data-modal").modal("hide"),d()),alert(t[1])}).always(function(){a.prop("disabled",!1).html(e)})}),$(document).on("click",".edit-btn",function(){var a,e,t,n=prompt("Enter the Genre title",$(this).data("title"));n&&(""!=(n=$.trim(n))?(a=$(this),e=a.html(),a.prop("disabled",!0).html(o),t=a.val(),$.post(l,{action:"edit",title:n,id:t}).done(function(t){t=JSON.parse(t);alert(t[1]),d()}).always(function(t){a.prop("disabled",!1).html(e)})):alert("Title cannot be empty"))}),$(document).on("click",".delete-btn",function(){var a=$(this),e=a.html(),t=a.val();confirm("Are you sure want to delete the record? This will also delete it's subgenere.")&&(a.prop("disabled",!0).html(o),$.post(l,{action:"delete",id:t}).done(function(t){t=JSON.parse(t);alert(t[1]),1==t[0]&&d()}).always(function(t){a.prop("disabled",!1).html(e)}))}),$(document).on("click",".page-link",function(t){t.preventDefault(),d($(this).data("page"))}),$("#search-form").submit(function(t){t.preventDefault();var a,e,t=$.trim($("#search-field").val());""!=t?(a=$("#search-btn"),e=a.html(),a.prop("disabled",!0).html(o),$.post(l,{action:"search",search:t}).done(function(t){var a=$("#data-table");a.find("tr:not(:first-child)").remove(),$("#pagination>li").remove();t=JSON.parse(t);if(1==t[0]){var e=t[1],n=e.length;for(i=0;i<n;i++){var l=e[i];null==l[1]&&(l[1]="-"),a.append("<tr><td class='text-center'>"+(i+1)+"</td><td>"+l[0]+"</td><td>"+l[1]+"</td><td></td><td class='text-center'><button type='button' class='btn btn-primary btn-sm edit-btn' value='"+l[2]+"' data-title='"+l[0]+"'><i class='fa-solid fa-pen'></i></button></td><td class='text-center'><button type='button' class='btn btn-danger btn-sm delete-btn' value="+l[2]+"><i class='fa-solid fa-trash'></i></button></td></tr>")}$("#records-count").html(n)}else a.append("<tr><td colspan='5' class='text-center'>"+t[1]+"</td></tr>"),$("#records-count").html(0)}).always(function(){a.prop("disabled",!1).html(e)})):d()})});
</script>
</html>