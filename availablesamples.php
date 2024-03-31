<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="stylesheets/samples.css">
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
    $sql = "SELECT * FROM `samples`";
    $cnt = 1;
    $res = $con->query($sql);
    $matchingBloodGroups = array('Apos' => array('Apos', 'Aneg', 'Opos', 'Oneg'), 'Aneg' => array('Aneg', 'Oneg'), 'Bpos' => array('Bpos', 'Bneg', 'Opos', 'Oneg'), 'Bneg' => array('Bneg', 'Oneg'), 'ABpos' => array('Apos', 'Aneg', 'Opos', 'Oneg', 'ABpos', 'ABneg', 'Bpos', 'Bneg'), 'ABneg' => array('Aneg', 'Oneg', 'ABneg', 'Bneg'), 'Opos' => array('Opos', 'Oneg'), 'Oneg' => array('Oneg'));
    if (!$res) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Error: $sql <br> $con->error 
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    }



    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        //first check if user is logged in
        if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] == false) {

            header("location: login.php");
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Please log in as a User to request Blood Samples
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
        } else if ($_SESSION['type'] == 'hospital') {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Please log in as a User, you can't request blood samples as a doctor
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
        } else {
            // echo ;
            $bloodGroup = trim($_POST['bloodGroup']);
            $hospital_name = trim($_POST['hospital_name']);
            $hospital_id = trim($_POST['hospital_id']);
            $amount = $_POST['amount'];
            $user_id = $_SESSION['id'];
            $user_name = $_SESSION['name'];
            $userBloodGroup = $_SESSION['bloodGroup'];

            if (empty($bloodGroup) || empty($hospital_name) || empty($amount)) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Enter amount of blood requested!
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
            } else {
                // echo $bloodGroup . $_SESSION['bloodGroup'];
                if (in_array($bloodGroup, $matchingBloodGroups[$_SESSION['bloodGroup']])) {
                    $sql = "SELECT * FROM `requests` WHERE `hospital_id`='$hospital_id' AND `user_id`='$user_id' AND `status`='pending' ";
                    if ($res = $con->query($sql)) {
                        if ($res->num_rows > 0) {
                            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        You have already requested blood from this hospital.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                        } else {
                            $sql1 = "INSERT INTO `requests` (`hospital_id`, `hospital_name`, `user_id`, `user_name`, `amount`, `bloodgroup`,`userbloodgroup`, `status`) VALUES ('$hospital_id', '$hospital_name', '$user_id', '$user_name', '$amount', '$bloodGroup','$userBloodGroup','pending')";
                            if ($res1 = $con->query(($sql1))) {
                                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    $amount ml of blood requested from $hospital_name hospital
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
                            } else {
                                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Error: $sql <br> $con->error 
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
                            }
                        }
                    } else {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    Error: $sql <br> $con->error 
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
                    }
                } else {
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                You can't receive blood from this blood group,please select a compatible blood group!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
                }
            }
        }
        $sql = "SELECT * FROM `samples`";
        $res = $con->query($sql);
    }
    ?>



    <div class="container">
        <h2 class="mt-4 text-center mb-4">Availabe Blood Samples</h2>
        <p>You can see all the availabe blood samples from all the hospitals here. You can request the blood samples which are compatible with your blood group only.</p>

        <?php
        if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] == true && $_SESSION['type'] == 'user') {
            echo "<p>The blood groups you can receive blood from are : <b>";
            $bloodGroup = $_SESSION['bloodGroup'];
            $arr = $matchingBloodGroups[$bloodGroup];
            for ($i = 0; $i < count($arr); $i += 1) {
                $bg = str_replace('pos', '+', $arr[$i]);
                $bg = str_replace('neg', '-', $bg);
                echo $bg . " ";
                if ($i + 1 < count($arr)) {
                    echo ", ";
                }
            }
            echo " </b></p>";
        }
        ?>
        <div class="pb-4"></div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">S.No.</th>
                    <th scope="col">Hospital</th>
                    <th scope="col">Amount of blood (in ml)</th>
                    <th scope="col">A+</th>
                    <th scope="col">A-</th>
                    <th scope="col">B+</th>
                    <th scope="col">B-</th>
                    <th scope="col">AB+</th>
                    <th scope="col">AB-</th>
                    <th scope="col">O+</th>
                    <th scope="col">O-</th>
                    <th scope="col">Request</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $res->fetch_assoc()) {
                    //                    <div class='btn-group' role='group' aria-label='Basic radio toggle button group'>
                    //   <input type='radio' class='btn-check' name='btnradio' id='btnradio1' autocomplete='off' checked>
                    //   <label class='btn btn-outline-primary' for='btnradio1'>{$row['Apos']}</label>
                    //   <input type='radio' class='btn-check' name='btnradio' id='btnradio1' autocomplete='off' checked>
                    //   <label class='btn btn-outline-primary' for='btnradio1'>{$row['Aneg']}</label>
                    // </div>
                    echo "
                    <form action='availablesamples.php' method='post'>
                    <tr>
                    <th scope='row' class='py-4'>$cnt</th>

                    <td class='py-4'>{$row['hospital_name']}</td>
                    <td class='py-4' ><input type='number' class='form-control'  name='amount' placeholder='Enter Amount of blood sample' ></td>

                    <input type='hidden' name='hospital_name' value={$row['hospital_name']}>
                    <input type='hidden' name='hospital_id' value={$row['hospital_id']}>

                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Apos'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Apos']} ml
                    </label>
                  </div> </td>

                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Aneg'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Aneg']} ml
                    </label>
                  </div> </td>

                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Bpos'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Bpos']} ml
                    </label>
                  </div> </td>

                  
                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Bneg'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Bneg']} ml
                    </label>
                  </div> </td>

                  
                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='ABpos'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['ABpos']} ml
                    </label>
                  </div> </td>

                 
                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='ABneg'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['ABneg']} ml
                    </label>
                  </div> </td>

                 
                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Opos' checked>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Opos']} ml
                    </label>
                  </div> </td>

                
                    <td class='py-4'><div class='form-check'>
                    <input class='form-check-input' type='radio' name='bloodGroup' id='flexRadioDefault1' value='Oneg'>
                    <label class='form-check-label' for='flexRadioDefault1'>
                    {$row['Oneg']} ml
                    </label>
                  </div> </td>


                    
                    <td class='py-4'><button class='btn btn-info'>Request</button></td>
                    
                </tr>
                </form>
                ";
                    $cnt += 1;
                }
                ?>
            </tbody>
        </table>
        <!-- // <td>{$row['Aneg']} ml</td>
                    // <td>{$row['Bpos']} ml</td>
                    // <td>{$row['Bneg']} ml</td>
                    // <td>{$row['ABpos']} ml</td>
                    // <td>{$row['ABneg']} ml</td>
                    // <td>{$row['Opos']} ml</td>
                    // <td>{$row['Oneg']} ml</td> -->
        <!-- <label class='input-group-text' for='inputGroupSelect01'>BG</label> <select class='form-select' id='inputGroupSelect01' name='bloodgroup'> <option selected value='A+'>A+</option><option value='A-'>A-</option><option value='B+'>B+</option><option value='B-'>B-</option><option value='AB+'>AB+</option><option value='AB-'>AB-</option><option value='O+'>O+</option><option value='O-'>O-</option></select> -->
        <!-- <?php
                echo '<div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">AIMS</h5>
                        <p class="card-text">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <label class="btn btn-outline-primary" for="btnradio1">A+ $cnt</label>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <label class="btn btn-outline-primary" for="btnradio2">Radio 2</label>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
  <label class="btn btn-outline-primary" for="btnradio3">Radio 3</label>
</div></p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            
        </div>'; ?> -->
    </div>
    <div class="py-2"></div>
    <footer class="footer bg-dark text-center text-light py-2 px-5 pt-3">
        <p>&copy; 2024 Blood Bank System. Made with ❤️ by Teeksha.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>