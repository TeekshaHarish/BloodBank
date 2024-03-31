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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // INSERT INTO `hospital` (`hospital_id`, `name`, `email`, `address`, `username`, `password`, `td`) VALUES ('1', 'aiims', 'aiims@gmail.com', 'aiims delhi', 'aiims', 'abcd', current_timestamp());
    if (empty(trim($name)) || empty(trim($email)) || empty(trim($address)) || empty(trim($username)) || empty(trim($password))) {
      echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Please enter value for all fields!
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    } elseif (trim($password) != trim($confirm_password)) {
      echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Password and confirm password dont match
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
    } else {
      $sql = "SELECT * from `hospital` WHERE `username`='$username'";
      if ($res = $con->query($sql)) {
        if ($res->num_rows > 0) {
          echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    Username already exists. Please choose a unique username
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
        } else {
          $password = password_hash($password, PASSWORD_DEFAULT);
          $sql = "INSERT INTO `hospital` ( `name`, `email`, `address`, `username`, `password`) VALUES ('$name', '$email', '$address', '$username', '$password')";
          if ($res = $con->query($sql)) {
            $id = $con->insert_id;
            $_SESSION["name"] = $name;
            $_SESSION["type"] = "hospital";
            $_SESSION['id'] = $id;
            $_SESSION['isLoggedIn'] = true;


            $sql1 = "INSERT INTO `samples` ( `hospital_id`,`hospital_name`) VALUES ('$id','$name')";
            if ($res1 = $con->query($sql1)) {
              // echo $_SESSION['id'] . $_SESSION["name"] . $_SESSION["type"];
              echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
      Registered as Hospital successfully!
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
              header("Location: index.php");
            }
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
    }
  }
  ?>




  <div class="container">
    <h1 class="mt-4 text-center">Register as Hospital</h1>
    <form class="col-md-6 mx-auto" action="hospital_register.php" method="post">
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Name of Hospital</span>
        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="name" placeholder="Enter your name" required />
      </div>

      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
        <input type="email" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="email" placeholder="Enter your email" required />
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Address</span>
        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="address" placeholder="Enter your hospital adddress" required />
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Username</span>
        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="username" placeholder="Enter your unique hospital username" required />
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Password</span>
        <input type="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="password" placeholder="Enter your password" required />
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text" id="inputGroup-sizing-default">Confirm Password</span>
        <input type="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="confirm_password" placeholder="Enter your password again" required />
      </div>

      <div class="text-center">

        <button type="submit" class="btn btn-primary w-50 mx-auto">
          Register
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