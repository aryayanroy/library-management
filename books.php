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
                        <a href="transactions" class="nav-link link-dark"><i class="fa-solid fa-receipt me-2"></i><span>Transactions</span></a>
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
                        <div class="col-sm-6 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Sort</span>
                                <select class="form-select">
                                    <option>Issue</option>
                                    <option>Due</option>
                                    <option>Fine</option>
                                </select>
                                <select class="form-select">
                                    <option>Assending</option>
                                    <option>Decending</option>
                                </select>
                            </div>
                        </div>
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
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Availability</th>
                                <th>Call Number</th>
                                <th colspan="3">Action</th>
                            </tr>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Chemistry</td>
                                <td>Emily Dickinson</td>
                                <td class="text-center text-nowrap">978-3-16-148410-0</td>
                                <td class="text-center text-success">Yes</td>
                                <td class="text-center text-nowrap">LA-229-L423-1993</td>
                                <td class="text-center"><button type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-clock-rotate-left"></i></button></td>
                                <td class="text-center"><button type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen"></i></button></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Chemistry</td>
                                <td>Emily Dickinson</td>
                                <td class="text-center">978-3-16-148410-0</td>
                                <td class="text-center text-success">Yes</td>
                                <td class="text-center">LA-229-L423-1993</td>
                                <td class="text-center"><button type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-clock-rotate-left"></i></button></td>
                                <td class="text-center"><button type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen"></i></button></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></td>
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
                            <input type="text" name="isbn" class="form-control" placeholder="XX-XXXX-XXX-X">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Author(s)</label>
                            <input type="text" name="author" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Subject</label>
                            <input type="text" name="author" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Publisher</label>
                            <input type="text" name="author" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Publication Date</label>
                            <input type="date" name="author" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label for="isbn" class="form-label">Call Number</label>
                            <input type="text" name="author" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add record</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("#isbn-validity").click(function(){
            $("#isbn").val()
        })
    })
</script>
</html>