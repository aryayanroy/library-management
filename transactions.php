<?php
    session_start();
    if(!isset($_SESSION["admin"])){
        header("Location: login");
        die();
    }
    if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["isbn"], $_POST["type"], $_POST["amount"], $_POST["provider"], $_POST["date"], $_POST["receipt"])){
        require "config.php";

        $type = trim($_POST["type"]);

        if($type==1){   //Purchase of book

            //Insert book in books table
            $isbn = trim($_POST["isbn"]);
            $sql = $conn->prepare("INSERT INTO books isbn VALUES ?");
            $sql->bindParam(1, $isbn, PDO::PARAM_STR);
            $sql->execute();

            //Select book id from ISBN
            $isbn = trim($_POST["isbn"]);
            $sql = $conn->prepare("INSERT INTO books isbn VALUES ?");
            $sql->bindParam(1, $isbn, PDO::PARAM_STR);
            $sql->execute();


        }else if($type==0){

        }

        $amount = trim($_POST["amount"]);
        $provider = trim($_POST["provider"]);
        $date = trim($_POST["date"]);
        $receipt = trim($_POST["receipt"]);

        $sql = $conn->prepare("INSERT INTO transactions (isbn, type, amount, provider, date, receipt_number) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bindParam(1, $isbn, PDO::PARAM_STR);
        $sql->bindParam(2, $type, PDO::PARAM_BOOL);
        $sql->bindParam(3, $amount, PDO::PARAM_INT);
        $sql->bindParam(4, $provider, PDO::PARAM_STR);
        $sql->bindParam(5, $date, PDO::PARAM_STR);
        $sql->bindParam(6, $receipt, PDO::PARAM_STR);
        $sql->execute();
        header("Location: transactions");
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
                        <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                        <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                        <a href="shelves" class="nav-link link-dark"><i class="fa-solid fa-layer-group me-2"></i><span>Shelves</span></a>
                        <a href="transactions" class="nav-link active"><i class="fa-solid fa-receipt me-2"></i><span>Transactions</span></a>
                        <a href="settings" class="nav-link link-dark"><i class="fa-solid fa-cog me-2"></i><span>Settings</span></a>
                    </nav>
                    <hr>
                    <div class="dropup-start dropup">
                        <button type="button" class="btn w-100 dropdown-toggle text-truncate" data-bs-toggle="dropdown">Hello, Sabrina</button>
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
                        <div class="col-sm-6 col-md-4"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-new"><i class="fa-solid fa-plus me-2"></i><span>Add new</span></button></div>
                        <div class="col-sm-6 col-md-4">
                            <form action="#" method="post" class="input-group">
                                <input type="text" class="form-control" placeholder="Search">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <div><span class="text-secondary me-2">Total records found:</span><b>25</b></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped table-sm mt-1">
                            <tr>
                                <th>Sl</th>
                                <th>SBIN</th>
                                <th>Type</th>
                                <th>Amount(₹)</th>
                                <th>Provider</th>
                                <th>Date</th>
                                <th>Recipt number</th>
                            </tr>
                            <tr>
                                <td class="text-center">1</td>
                                <td>978-3-16-148410-0</td>
                                <td class="text-center">Fine</td>
                                <td class="text-end">0.00</td>
                                <td class="text-center">--</td>
                                <td>12-07-2023</td>
                                <td>INV-21-12-009</td>
                            </tr>
                        </table>
                    </div>
                    <ul class="pagination justify-content-center">
                        <li class="page-item"><a href="#" class="page-link"><i class="fa-solid fa-angles-left"></i></a></li>
                        <li class="page-item active"><a href="#" class="page-link">1</a></li>
                        <li class="page-item"><a href="#" class="page-link">2</a></li>
                        <li class="page-item"><a href="#" class="page-link">3</a></li>
                        <li class="page-item"><a href="#" class="page-link"><i class="fa-solid fa-angles-right"></i></a></li>
                    </ul>
                </article>
            </main>
        </div>
    </div>
    <!-- off canvas -->
    <aside id="aside-right" class="offcanvas offcanvas-end">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-truncate">Hello, Sabrina</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav nav-pills flex-column flex-grow-1">
                <a href="dashboard" class="nav-link link-dark"><i class="fa-solid fa-gauge me-2"></i><span>Dashboard</span></a>
                <a href="borrows" class="nav-link link-dark"><i class="fa-solid fa-list-check me-2"></i><span>Borrows</span></a>
                <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
                <a href="shelves" class="nav-link link-dark"><i class="fa-solid fa-layer-group me-2"></i><span>Shelves</span></a>
                <a href="transactions" class="nav-link link-dark"><i class="fa-solid fa-receipt me-2"></i><span>Transactions</span></a>
                <a href="settings" class="nav-link link-dark"><i class="fa-solid fa-cog me-2"></i><span>Settings</span></a>
            </nav>
        </div>
        <div class="p-3 border-top">
            <a href="logout" class="btn btn-light w-100">Logout</a>
        </div>
    </aside>
    <div id="add-new" class="modal fade">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control" placeholder="XX-XXXX-XXX-X" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="type" class="form-label">Type</label>
                            <select id="type" name="type" class="form-select">
                                <option value="0">Fine</option>
                                <option value="1">Purchase</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" id="amount" name="amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="provider" class="form-label">Provider</label>
                            <input type="text" id="provider" name="provider" class="form-control" spellcheck="false" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="date" class="form-label">Payment date</label>
                            <input type="date" id="date" name="date" class="form-control" max="2000-01-01" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="receipt" class="form-label">Receipt number</label>
                            <input type="text" id="receipt" name="receipt" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add-record" class="btn btn-primary">Add record</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</html>