<?php
  session_start();
  include_once 'connect.php'; // เชื่อมต่อดาต้าเบส (ต้องเป็น MySQLi object)

  $username = $_POST['username'];
  $password = $_POST['password']; // นี่คือรหัสผ่านที่ผู้ใช้กรอกแบบ plain text

  // 1. SELECT User Record by Username ONLY (ใช้ Prepared Statement เพื่อความปลอดภัย)
  $sql = "SELECT id, username, fullname, status, password FROM users WHERE username = ?";
  
  if ($stmt = mysqli_prepare($con, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $username); // 's' = string
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      mysqli_stmt_close($stmt);

      // 2. ตรวจสอบว่าพบ Username หรือไม่
      if ($row) {
          // 3. ตรวจสอบรหัสผ่าน: ใช้ password_verify() เท่านั้นสำหรับรหัสที่ถูก Hash
          // หรือตรวจสอบรหัสผ่านแบบ Plain Text สำหรับรหัสที่ไม่ได้ Hash (ไม่แนะนำ)
          
          $stored_password = $row['password'];

          // ตรวจสอบกับรหัสที่ถูก Hash (สำหรับ admin, user1, user2)
          if (strpos($stored_password, '$2y$') === 0 && password_verify($password, $stored_password)) {
              // รหัสผ่านถูกต้องสำหรับผู้ใช้ที่ใช้ Hash
              $login_success = true;
          } 
          // **คำเตือน: โค้ดส่วนนี้เพื่อรองรับรหัสผ่านเก่า (cust01, cust02) เท่านั้น 
          // ควรอัปเดตฐานข้อมูลให้เป็น Hash ทั้งหมด**
          else if ($password === $stored_password) {
              // รหัสผ่านถูกต้องสำหรับผู้ใช้ที่ใช้ Plain Text
              $login_success = true;
          } else {
              $login_success = false;
          }

      } else {
          // ไม่พบ Username
          $login_success = false;
      }
      
      // 4. จัดการผลลัพธ์การ Login
if ($login_success) {
          $_SESSION['id'] = $row['id'];
          $_SESSION['username'] = $row['username'];
          $_SESSION['fullname'] = $row['fullname'];
          
          // ✅ เพิ่มบรรทัดนี้เข้าไปครับ!
          $_SESSION['status'] = $row['status']; 
          
          if ($row['status'] == 'admin') {
              header("location: admin/index.php");
          } else {
              header("location: index.php");
          }
      } else {
          echo "<script>alert('Username หรือ Password ไม่ถูกต้อง');window.history.back();</script>";
      }
  } else {
      echo "ERROR: Could not prepare query: " . mysqli_error($con);
  }
?>