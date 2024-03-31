<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] == false || $_SESSION['type'] == 'user') {
        header("Location: login.php");
    }
    $hospital_id = $_SESSION['id'];
    $hospital_name = $_SESSION['name'];

    $sql = "SELECT * FROM `samples` WHERE `hospital_id`='$hospital_id'";
    if ($res = $con->query($sql)) {
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            // echo "yay";
        } else {
            $sql1 = "INSERT INTO `samples` ( `hospital_id`,`hospital_name`) VALUES ('$hospital_id','$hospital_name')";
            $con->query($sql1);
            if ($res1 = $con->query($sql)) {
                $row = $res1->fetch_assoc();
                // echo "y2";
            }
        }
    } else {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
Error: $sql <br> $con->error 
<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
</div>";
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // echo $_POST['Apos'] . $_POST['Aneg'];
        $Apos = $_POST['Apos'];
        $Aneg = $_POST['Aneg'];
        $Bpos = $_POST['Bpos'];
        $Bneg = $_POST['Bneg'];
        $ABpos = $_POST['ABpos'];
        $ABneg = $_POST['ABneg'];
        $Opos = $_POST['Opos'];
        $Oneg = $_POST['Oneg'];
        if ($Apos == null ||  $Aneg == null || $Bpos == null || $Bneg == null || $ABpos == null || $ABneg == null || $Opos == null || $Oneg == null) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        Please enter value for all blood groups!
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        } else {
            $sql = "UPDATE `samples` SET `Apos`='$Apos',`Aneg`='$Aneg',`Bpos`='$Bpos',`Bneg`='$Bneg',`ABpos`='$ABpos',`ABneg`='$ABneg',`Opos`='$Opos',`Oneg`='$Oneg' WHERE `hospital_id`='$hospital_id' ";
            if ($res = $con->query($sql)) {
                // $row = $res->fetch_assoc();
                $sql = "SELECT * FROM `samples` WHERE `hospital_id`='$hospital_id'";
                $res = $con->query($sql);
                $row = $res->fetch_assoc();
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        Blood Info for the Blood Groups Successfully Updated!
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Error: $sql <br> $con->error 
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
            }
        }
    }
    ?>




    <div class="container col-md-7">
        <h2 class="text-center my-4">Add Blood Info</h2>
        <h5 class="mb-3">Please add information about the Blood Present in your hospital of the repective blood groups</h5>
        <form class="mx-auto " action="addbloodinfo.php" method="post">
            <div class="d-flex">

                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">A+</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Apos" value=<?php echo $row['Apos']; ?> />
                </div>
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">A-</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Aneg" value=<?php echo $row['Aneg']; ?> />
                </div>
            </div>
            <div class="d-flex">
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">B+</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Bpos" value=<?php echo $row['Bpos']; ?> />
                </div>
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">B-</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Bneg" value=<?php echo $row['Bneg']; ?> />
                </div>
            </div>
            <div class="d-flex">
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">AB+</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="ABpos" value=<?php echo $row['ABpos']; ?> />
                </div>
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">AB-</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="ABneg" value=<?php echo $row['ABneg']; ?> />
                </div>
            </div>
            <div class="d-flex">
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">O+</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Opos" value=<?php echo $row['Opos']; ?> />
                </div>
                <div class="input-group mb-3 mx-4">
                    <span class="input-group-text" id="inputGroup-sizing-default">O-</span>
                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="Oneg" value=<?php echo $row['Oneg']; ?> />
                </div>
            </div>
            <div class="text-center">

                <button type="submit" class="btn btn-success w-50 mx-auto">
                    Update Blood Info
                </button>
            </div>
        </form>
    </div>
    <div class="py-2"></div>
    <footer class="footer bg-dark text-center text-light py-2 px-5 pt-3 fixed-bottom">
        <p>&copy; 2024 Blood Bank System. Made with ❤️ by Teeksha.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>