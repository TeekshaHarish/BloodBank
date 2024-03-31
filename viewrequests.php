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
    require_once "config.php";
    if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] == false) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Please log in as a Hospital to view Blood Samples
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else if ($_SESSION['type'] == 'user') {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Please log in as a Hospital, you can't view requests as a user
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } else {
        $hospital_id = $_SESSION['id'];
        $sql = "SELECT * FROM `requests` WHERE `status`='pending' AND `hospital_id`='$hospital_id'";
        $res = $con->query($sql);
        if (!$res) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Error: $sql <br> $con->error 
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        }
    }
    $filter = 'pending';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['filter'])) {
            $filterstatus = $_POST['filter'];
            $filter = $filterstatus;
            $hospital_id = $_SESSION['id'];
            $sql = "SELECT * FROM `requests` WHERE `status`='$filterstatus' AND `hospital_id`='$hospital_id'";
            $res = $con->query($sql);
            if (!$res) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Error: $sql <br> $con->error 
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            }
        } else {


            // $sql="UPDATE `requests` SET `status`='$status' WHERE `request_id`='$request_id'";
            $hospital_id = $_SESSION['id'];
            $action = $_POST['action'];
            $request_id = $_POST['request_id'];
            if ($action == 'reject') {
                $sql4 = "UPDATE `requests` SET `status`='rejected' WHERE `request_id`='$request_id'";
                if ($res4 = $con->query($sql4)) {
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                Request Rejected!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
                    $sql = "SELECT * FROM `requests` WHERE `status`='pending' AND `hospital_id`='$hospital_id'";
                    $res = $con->query($sql);
                } else {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Error: $sql <br> $con->error 
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
                }
            } else {

                $sql = "SELECT * FROM `samples` WHERE `hospital_id`='$hospital_id'";
                if ($res1 = $con->query($sql)) {
                    $row1 = $res1->fetch_assoc();

                    $sql2 = "SELECT * FROM `requests` WHERE `request_id`='$request_id'";
                    if ($res2 = $con->query($sql2)) {
                        $row2 = $res2->fetch_assoc();
                        $requestedAmount = $row2['amount'];
                        $bloodGroup = $row2['bloodgroup'];
                        $availableAmount = $row1[$bloodGroup];
                        // echo $availableAmount . $requestedAmount;
                        if ($requestedAmount <= $availableAmount) {
                            $diff = $availableAmount - $requestedAmount;
                            $sql3 = "UPDATE `samples` SET `$bloodGroup`='$diff' WHERE `hospital_id`='$hospital_id'";
                            $res3 = $con->query($sql3);
                            $sql4 = "UPDATE `requests` SET `status`='accepted' WHERE `request_id`='$request_id'";
                            $res4 = $con->query($sql4);
                            if ($res3 && $res4) {
                                $sql = "SELECT * FROM `requests` WHERE `status`='pending' AND `hospital_id`='$hospital_id'";
                                $res = $con->query($sql);
                                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            Success Request accepted!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                                $sql = "SELECT * FROM `requests` WHERE `status`='pending' AND `hospital_id`='$hospital_id'";
                                $res = $con->query($sql);
                            } else {
                                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            Error while updating values;
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        Requested Amount more than available amount. Request can't be processed
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Error: $sql <br> $con->error 
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
    }
    ?>


    <div class="container ">
        <h2 class="mt-4 text-center mb-5">Requests for Blood Samples</h2>
        <form action="viewrequests.php" method="post" class="d-flex justify-content-center container">
            <span class="fs-5">Filter:</span>
            <div class="input-group mb-3 mx-5" style="width:25rem;">
                <label class="input-group-text" for="inputGroupSelect01">Choose Request Type</label>
                <select class="form-select" id="inputGroupSelect01" name="filter">
                    <option value="pending" <?php echo $filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="accepted" <?php echo $filter == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                    <option value="rejected" <?php echo $filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <button class="btn btn-success h-75" type="submit">Filter</button>
        </form>
        <?php
        while ($row = $res->fetch_assoc()) {
            // echo $row['bloodgroup'];
            $bg = str_replace('pos', '+', $row['bloodgroup']);
            // echo "bg" . $bg;
            $bg = str_replace('neg', '-', $bg);
            $bguser = str_replace('pos', '+', $row['userbloodgroup']);
            $bguser = str_replace('pos', '+', $bguser);

            echo "
            <form action='viewrequests.php' method='post'>
        <div class='col-md-6 card text-dark bg-light mb-3 mx-5 my-5' style='max-width: 20rem;''>
            <div class='card-header'>Request by {$row['user_name']}</div>
            <div class='card-body'>
                <h5 class='card-title'>{$row['amount']} ml of blood requested</h5>
                <p class='card-text'><b>Blood group requested: $bg</b></p>
                <p class='card-text'><b>Receiver's blood group: $bguser</b></p>
                <p class='card-text'><b>Receiver's name: </b> {$row['user_name']}</p>
                <p class='card-text'><b>Requested at:</b> {$row['dt']}</p>

                <input type='hidden' name='request_id' value={$row['request_id']}>
                " . ($row['status'] == 'pending' ? "
                <button class='btn btn-success mx-3' type='submit' name='action' value='accept'>Accept</button>
                <button class='btn btn-danger mx-3' type='submit' name='action' value='reject'>Reject</button>" : "") . "
            </div>
        </div>
        </form>
        ";
        }

        ?>
        <!-- <div class="col-md-6 card text-dark bg-light mb-3" style="max-width: 18rem;">
            <div class="card-header">Header</div>
            <div class="card-body">
                <h5 class="card-title">Light card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div> -->
    </div>
    <div class="py-2"></div>
    <footer class="footer bg-dark text-center text-light py-2 px-5 pt-3">
        <p>&copy; 2024 Blood Bank System. Made with ❤️ by Teeksha.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>