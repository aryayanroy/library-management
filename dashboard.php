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
    $sql = $conn->prepare("SELECT COALESCE((SELECT COUNT(*) FROM borrows WHERE returned IS NULL), 0), COALESCE((SELECT COUNT(*) FROM borrows WHERE returned IS NOT NULL), 0), COALESCE((SELECT SUM(DATEDIFF(CURDATE(), due)) FROM borrows WHERE returned IS NULL AND CURDATE() > due), 0), COALESCE((SELECT SUM(DATEDIFF(returned, due)) FROM borrows WHERE returned IS NOT NULL AND returned > due), 0), COALESCE((SELECT COUNT(*) FROM members WHERE gender IS TRUE), 0), COALESCE((SELECT COUNT(*) FROM members WHERE gender IS FALSE), 0), COALESCE((SELECT COUNT(*) FROM members WHERE renewal > CURDATE()), 0), COALESCE((SELECT COUNT(*) FROM members WHERE renewal <= CURDATE()), 0), COALESCE((SELECT COUNT(*) FROM books), 0), COALESCE((SELECT COUNT(*) FROM genres AS parent LEFT JOIN genres AS child ON parent.id = child.parent_genre), 0)");
    $sql->execute();
    $data = $sql->fetch(PDO::FETCH_NUM);
    $loan = $data[0];
    $returned = $data[1];
    $collected = $data[2]*5;
    $pending = $data[3]*5;
    $male = $data[4];
    $female = $data[5];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Library Management</title>
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
                        <a href="dashboard" class="nav-link active"><i class="fa-solid fa-gauge me-2"></i><span>Dashboard</span></a>
                        <a href="borrows" class="nav-link link-dark"><i class="fa-solid fa-list-check me-2"></i><span>Borrows</span></a>
                        <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                        <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                        <a href="genres" class="nav-link link-dark"><i class="fa-solid fa-sitemap me-2"></i><span>Genres</span></a>
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
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <table class="table table-striped table-sm">
                                <tr>
                                    <th colspan="2">Borrows</th>
                                </tr>
                                <tr>
                                    <td>Loans</td>
                                    <td class="text-end"><?php echo $loan; ?></td>
                                </tr>
                                <tr>
                                    <td>Returned</td>
                                    <td class="text-end"><?php echo $returned; ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td class="text-end"><?php echo $loan+$returned; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <table class="table table-striped table-sm">
                                <tr>
                                    <th colspan="2">Fines(₹)</th>
                                </tr>
                                <tr>
                                    <td>Collected</td>
                                    <td class="text-end"><?php echo $collected.".00"; ?></td>
                                </tr>
                                <tr>
                                    <td>Pending</td>
                                    <td class="text-end"><?php echo $pending.".00"; ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td class="text-end"><?php echo $collected+$pending.".00"; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <table class="table table-striped table-sm">
                                <tr>
                                    <th colspan="4">Members</th>
                                </tr>
                                <tr>
                                    <th colspan="2">Gender</th>
                                    <th colspan="2">Status</th>
                                </tr>
                                <tr>
                                    <td>Male</td>
                                    <td class="text-end"><?php echo $male; ?></td>
                                    <td>Active</td>
                                    <td class="text-end"><?php echo $data[6]; ?></td>
                                </tr>
                                <tr>
                                    <td>Female</td>
                                    <td class="text-end"><?php echo $female; ?></td>
                                    <td>Expired</td>
                                    <td class="text-end"><?php echo $data[7]; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td colspan="2" class="text-end"><?php echo $male+$female; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <table class="table table-striped table-sm">
                                <tr>
                                    <th colspan="2">Books</th>
                                </tr>
                                <tr>
                                    <td>Books</td>
                                    <td class="text-end"><?php echo $data[8]; ?></td>
                                </tr>
                                <tr>
                                    <td>Genres</td>
                                    <td class="text-end"><?php echo $data[9]; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
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
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
</script>
</html>