<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="stylesheets/home.css">
    <title>Blood Bank</title>
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
    <header>
        <h1>Welcome to the Blood Bank System</h1>
    </header>
    <div class="heroConatiner row">
        <div class="col-md-6 herotext">
            <!-- <h1>Welcome to the Blood Bank System</h1> -->
            <h2>A Platform for Saving Lives</h2>
            <p>Connecting hospitals and blood receivers to ensure timely and efficient access to life-saving blood transfusions. Our system provides a centralized platform for hospitals to manage blood samples and for receivers to find and request the blood they need.</p>
            <!-- <p>Join us in our mission to make a difference in the lives of patients in need of blood transfusions. Every donation and request brings hope and saves lives.</p> -->

        </div>
        <div class="col-md-6">
            <img src="./images/heroImg.png" class="heroImg w-100 h-100" alt="">
        </div>
    </div>
    <section class="hero ">
        <div class="hero-content">
            <h2>Our Mission</h2>
            <p>Join us in our mission to make a difference in the lives of patients in need of blood transfusions. Every donation and request brings hope and saves lives. Together, let's make a lasting impact by spreading awareness about the importance of blood donation and encouraging more individuals to join our cause.</p>
        </div>
    </section>
    <div class="mx-auto pb-4">
        <img src="./images/bloodGroup.jpg" class="w-100" alt="">
    </div>
    <div class="row my-5">
        <div class="col-md-4">
            <img src="./images/image4.jpeg" class="w-100 h-100" alt="">
        </div>
        <div class="col-md-4">
            <img src="./images/image2.jpeg" class="w-100 h-100" alt="">
        </div>
        <div class="col-md-4">
            <img src="./images/image3.jpeg" class="w-100 h-100" alt="">
        </div>
    </div>
    <section class="features">
        <div class="feature">
            <h2>Hospitals</h2>
            <p>Manage blood samples and view requests.</p>
            <a href="hospital_register.php" class="btn">Hospital Registration</a>
            <a href="viewrequests.php" class="btn">View Requests</a>
            <a href="addbloodinfo.php" class="btn">Add Blood Info</a>
        </div>
        <div class="feature">
            <h2>Receivers</h2>
            <p>Find available blood samples and request them.</p>
            <a href="user_register.php" class="btn">Receiver Registration</a>
            <a href="availablesamples.php" class="btn">Check available samples</a>
        </div>
    </section>
    <footer class="footer bg-dark text-center">
        <p>&copy; 2024 Blood Bank System. Made with ❤️ by Teeksha.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>