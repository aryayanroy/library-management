<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrows | Library Management</title>
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
            <aside class="col-2 min-vh-100 border-end">
                <div class="py-3 d-flex flex-column sticky-top h-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 55 64" width=50 class="mx-auto"><path d="M55 26.5v23.8c0 1.2-.4 2.2-1.3 3.2-.9.9-1.9 1.5-3.2 1.6-3.5.4-6.8 1.3-10.1 2.6S34 60.8 31 62.9a6.06 6.06 0 0 1-3.5 1.1 6.06 6.06 0 0 1-3.5-1.1c-3-2.1-6.1-3.9-9.4-5.2s-6.7-2.2-10.1-2.6c-1.3-.2-2.3-.7-3.2-1.6-.9-1-1.3-2-1.3-3.2V26.5c0-1.3.5-2.4 1.4-3.2s2-1.2 3.1-1c4 .6 8 2 11.9 4 3.9 2.1 7.6 4.8 11.1 8.1 3.5-3.3 7.2-6 11.1-8.1s7.9-3.4 11.9-4c1.2-.2 2.2.1 3.1 1 .9.8 1.4 1.9 1.4 3.2z" fill="#004d40"/><path d="M39.5 11.8c0 3.3-1.1 6.1-3.4 8.4s-5.1 3.4-8.4 3.4-6.1-1.1-8.4-3.4-3.4-5.1-3.4-8.4 1.1-6.1 3.4-8.4S24.4 0 27.7 0s6.1 1.1 8.4 3.4 3.4 5.1 3.4 8.4z" fill="#e65100"/></svg>
                    <hr>
                    <nav class="nav nav-pills flex-column flex-grow-1">
                        <a href="dashboard" class="nav-link link-dark"><i class="fa-solid fa-gauge me-2"></i><span>Dashboard</span></a>
                        <a href="borrows" class="nav-link active"><i class="fa-solid fa-list-check me-2"></i><span>Borrows</span></a>
                        <a href="books" class="nav-link link-dark"><i class="fa-solid fa-book me-2"></i><span>Books</span></a>
                        <a href="members" class="nav-link link-dark"><i class="fa-solid fa-users me-2"></i><span>Members</span></a>
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
            <main class="col-10 px-0">
                <header class="navbar bg-white border-bottom sticky-top">
                    <div class="container-fluid">
                        <a href="/" class="navbar-brand"><h3 class="mb-0"><i class="fa-solid fa-landmark me-3"></i><span>Library Management</span></h3></a>
                    </div>
                </header>
                <article class="container-fluid py-3">
                    <h5>Borrows</h5>
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="input-group">
                                <span class="input-group-text">Sort by</span>
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
                        <div class="col-4">
                            <form action="#" method="post" class="input-group">
                                <input type="text" class="form-control" placeholder="Search">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <table id="data-table" class="table table-bordered table-striped table-sm mt-3">
                        <tr>
                            <th>Sl</th>
                            <th>Borrower ID</th>
                            <th>Book ID</th>
                            <th>Issue</th>
                            <th>Due</th>
                            <th>Fine (₹)</th>
                            <th colspan=2>Action</th>
                        </tr>
                        <tr>
                            <td class="text-center">1</td>
                            <td><a href="#" class="text-decoration-none">BTECH/10504/22</a></td>
                            <td><a href="#" class="text-decoration-none">TB-2344-FF2</a></td>
                            <td class="text-center">10-06-2022</td>
                            <td class="text-center">20-06-2022</td>
                            <td class="text-end">0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i></button></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td><a href="#" class="text-decoration-none">BTECH/10504/22</a></td>
                            <td><a href="#" class="text-decoration-none">TB-2344-FF2</a></td>
                            <td class="text-center">10-06-2022</td>
                            <td class="text-center">20-06-2022</td>
                            <td class="text-end">0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i></button></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td><a href="#" class="text-decoration-none">BTECH/10504/22</a></td>
                            <td><a href="#" class="text-decoration-none">TB-2344-FF2</a></td>
                            <td class="text-center">10-06-2022</td>
                            <td class="text-center">20-06-2022</td>
                            <td class="text-end">0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i></button></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                    </table>
                </article>
            </main>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</html>