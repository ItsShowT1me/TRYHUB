<?php
session_start();

  include 'function.php';
  include 'connection.php';

  

  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    
    $email = $_POST["email"];
    $password = $_POST["password"];

    if(!empty($email) && !empty($password)) {
      
      
      // read to database
      
      $query = "select * from users where email = '$email' limit 1";

      $result = mysqli_query($con, $query);

      mysqli_query($con, $query);

      if($result) 

      { if($result && mysqli_num_rows($result) > 0) 
        {
          $user_data = mysqli_fetch_assoc($result);

          // If you store hashed passwords, use password_verify()
          // if(password_verify($password, $user_data['password'])) {
          if($user_data['password'] === $password) 
          {
            $_SESSION['user_id'] = $user_data['user_id'];

            // Redirect admin user to admin-dashboard.php
            if ($_SESSION['user_id'] == 971221) {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: index.php");
            }
            die;
            //รอแก้จอขาว
          } else {
            echo "<script>alert('Wrong email or password');</script>";
          }
        } else {
          echo "<script>alert('Wrong email or password');</script>";
        }
    
    }
      

    } else {
      echo "<script>alert(Please enter some valid information!.);</script>";
    }

  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BUMBTI</title>

  <!-- BOXICONS -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- CSS -->
  <link rel="stylesheet" href="CSS code/login_f1.css" />
</head>

<body>
  <div class="wrapper">
    <div class="form-header">
      <div class="titles">
        <div class="title-login">Login</div>
        
      </div>
    </div>

  

    <!-- Login Form -->
    <form action="#" method ="post" class="login-form" autocomplete="off">
      <div class="input-box">
        <input type="email" class="input-field" id="log-email" name="email" required />
        <label for="log-email" class="label">Email</label>
        <i class='bx bxs-envelope icon'></i>
      </div>
      <div class="input-box">
        <input type="password" class="input-field" id="log-pass" name="password" required />
        <label for="log-pass" class="label">Password</label>
        <i class='bx bxs-lock-alt icon'></i>
      </div>
      <div class="form-cols">
        <div class="col-1"></div>
        <div class="col_2"><a href="#">Forgot password?</a></div>
      </div>
      <div class="input-box">
        <button class="btn-submit" id="button" value="Login">Sign In <i class='bx bx-log-in'></i></button>
      </div>
      <div class="switch-form">
        <span>Don't have an account? <a href="register.php" onclick="registerFunction()">Register</a></span>
      </div>
    </form>



<video autoplay muted loop id="bg-video">
    <source src="pf_image/bg-gif.mp4" type="video/mp4">
  </video>



<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
    }

    
    video#bg-video {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        min-height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    
    .content {
        position: relative;
        z-index: 1;
        color: white;
        text-align: center;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        background: rgba(0,0,0,0.4);
        padding: 20px;
        border-radius: 10px;
    }
</style>

  <script src="login_f1.js"></script>
</body>
</html>
