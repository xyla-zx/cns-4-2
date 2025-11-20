<?php
  include 'connect.php';
  $username = $_POST['username'];
  $phone = $_POST['phone'];
  $fullname = $_POST['fullname'];
  $password = $_POST['password'];
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  if(empty($username) || empty($phone) || empty($fullname) || empty($password)) {
      die('All fields are required.');
  }else if($password !== $_POST['confirm_password']) {
      die('Passwords do not match.');
  } else {
      $stmt = $con->prepare("INSERT INTO users (username, phone, fullname, password) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $phone, $fullname, $hashed_password);
      if($stmt->execute()) {
          header("location: login.php");
      } else {
          echo "Error: " . $stmt->error;
      }
      $stmt->close();
      $con->close();
  }
