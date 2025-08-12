<?php
session_start();

  include 'function.php';
  include 'connection.php';

  

  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_name = $_POST["user_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if(!empty($user_name) && !empty($email) && !empty($password)) {
      
      
      // Save to database
      do {
          $user_id = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // never 0
          $check = mysqli_query($con, "SELECT 1 FROM users WHERE user_id = '$user_id' LIMIT 1");
      } while(mysqli_num_rows($check) > 0);
      
      $query = "INSERT INTO users (user_id, user_name, email, password) 
                VALUES ('$user_id', '$user_name', '$email', '$password')";

      mysqli_query($con, $query);

      header("Location: login.php");
      die;

    } else {
      echo "Please enter some valid information!.";
    }

  }
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - BUMBTI</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="CSS code/register_f1.css" />
</head>
<body>
  <div class="wrapper" style="height: 580px;">
    <div class="form-header">
      <div class="titles">
        <div class="title-register" style="opacity: 1; top: 50%;">Register</div>
      </div>
    </div>

    <!-- Register Form -->
    <form action="#" method="POST" class="register-form" autocomplete="off" style="left: 50%; opacity: 1;">
      <div class="input-box">
        <input type="text" class="input-field" id="reg-name" name="user_name" required />
        <label for="reg-name" class="label">Username</label>
        <i class='bx bx-user icon'></i>
      </div>
      <div class="input-box">
        <input type="text" class="input-field" id="reg-email" name="email" required />
        <label for="reg-email" class="label">Email</label>
        <i class='bx bxs-envelope icon'></i>
      </div>
      <div class="input-box">
        <input type="password" class="input-field" id="reg-pass" name="password" required />
        <label for="reg-pass" class="label">Password</label>
        <i class='bx bxs-lock-alt icon'></i>
      </div>
      <div class="form-cols">
        <div class="col-1">
          <input type="checkbox" id="agree" />
          <label for="agree"> I agree to terms & condition</label>
        </div>
        <div class="col_2"></div>
      </div>
      <div class="input-box">
        <button class="btn-submit" id="button" value="Signup">Sign Up <i class='bx bx-log-out'></i></button>
      </div>
      <div class="switch-form">
        <span>Already have an account? <a href="login_f1.php">Login</a></span>
      </div>
    </form>
  </div>
  <script src="register_f1.js"></script>
</body>
</html>
