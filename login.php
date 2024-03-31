<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="stylesheets/style.css">
    <title>BloodBank</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand px-3" href="index.php">BloodBank</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="availablesamples.php">Available Samples</a>
                    </li>

                    <?php
                    session_start();
                    if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] == false) {
                        echo '
                        <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_register.php">Register as User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hospital_register.php">Register as Hospital</a>
                    </li>';
                    } else {
                        if ($_SESSION['type'] == 'hospital') {
                            echo '
                            <li class="nav-item">
                        <a class="nav-link" href="addbloodinfo.php">Add Blood Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="viewrequests.php">View Requests</a>
                    </li>';
                        }
                        echo '
                        <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>';
                    }
                    ?>

                </ul>
            </div>
            <!-- <div class="ml-auto text-light px-4">Welcome <?php echo $_SESSION['name']; ?></div> -->
        </div>
    </nav>


    <?php
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require_once "config.php";

        $username = $_POST['username'];
        $password = $_POST['password'];
        $type = $_POST['type'];

        // INSERT INTO `hospital` (`hospital_id`, `name`, `email`, `address`, `username`, `password`, `td`) VALUES ('1', 'aiims', 'aiims@gmail.com', 'aiims delhi', 'aiims', 'abcd', current_timestamp());
        if (empty(trim($username)) || empty(trim($password))) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Username and password can't be empty
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        } elseif ($type != 'user' && $type != 'hospital') {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Invalid type of user;
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
             </div>";
        } else {
            $sql = "SELECT * from `$type` WHERE `username`='$username'";
            if ($res = $con->query($sql)) {
                if ($res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        $_SESSION["name"] = $row['name'];
                        $_SESSION["type"] = $type;
                        $_SESSION['isLoggedIn'] = true;
                        if ($type == 'user') {
                            $_SESSION['id'] = $row['user_id'];
                            $_SESSION['bloodGroup'] = $row['bloodgroup'];
                        } else {
                            $_SESSION['id'] = $row['hospital_id'];
                        }
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Suucessfully logged in!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
                        header("Location: index.php");
                    } else {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            Password incorrect! Please try again
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
                    }
                } else {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            Username doesn't exists. Please enter a valid username
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
                }
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Error: $sql <br> $con->error 
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
        }
    }
    ?>



    <div class="container w-75">
        <h1 class="mt-4 text-center">Login</h1>
        <form class="col-md-6 mx-auto" action="login.php" method="post">
            <!-- <div class="input-group mb-5 mt-3 mx-auto w-50">
                <label class="input-group-text" for="inputGroupSelect01">Login as</label>
                <select class="form-select" id="inputGroupSelect01" name="type">
                    <option selected value="user">User</option>
                    <option value="hospital">Hospital</option>
                </select>
            </div> -->
            <div class="d-flex justify-content-center align-items-center my-5">
                <span class="mx-3 fs-5"><b>Login as:</b></span>
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="type" id="btnradio1" value="user" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="btnradio1">User</label>

                    <input type="radio" class="btn-check" name="type" id="btnradio2" value="hospital" autocomplete="off">
                    <label class="btn btn-outline-danger" for="btnradio2">Hospital</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Username</span>
                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="username" placeholder="Enter your username" />
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Password</span>
                <input type="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="password" placeholder="Enter your password" />
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success w-50 mx-auto">
                    Login
                </button>
            </div>
        </form>
    </div>
    <div class="py-2"></div>
    <footer class="footer bg-dark text-center text-light py-2 px-5 pt-3">
        <p>&copy; 2024 Blood Bank System. Made with ❤️ by Teeksha.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>