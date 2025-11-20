<?php
  session_start();
  if (empty($_SESSION['username']) || ($_SESSION['status'] ?? '') !== 'admin') {
      header('Location: ../login.php');
      exit();
  }
  include_once '../connect.php';

  // นับจำนวนผู้ใช้ทั้งหมด
  $result_all = mysqli_query($con, "SELECT COUNT(*) as total FROM users");
  $row_all = mysqli_fetch_assoc($result_all);
  $total_users = $row_all['total'];

  // นับจำนวน Admin
  $result_admin = mysqli_query($con, "SELECT COUNT(*) as total FROM users WHERE status='admin'");
  $row_admin = mysqli_fetch_assoc($result_admin);
  $total_admins = $row_admin['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php"><i class="bi bi-speedometer2"></i> Admin Panel</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
          <li class="nav-item"><a class="nav-link" href="../index.php" target="_blank">View Site</a></li>
        </ul>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="display-5">Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h1>
        <p class="text-muted">ภาพรวมระบบและการจัดการสมาชิก</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle p-3 me-3">
              <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
              <h6 class="card-subtitle text-muted mb-1">Total Users</h6>
              <h2 class="card-title mb-0"><?php echo $total_users; ?></h2>
            </div>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="users.php" class="text-decoration-none small text-primary">Manage Users &rarr;</a>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="bg-danger text-white rounded-circle p-3 me-3">
              <i class="bi bi-shield-lock-fill fs-3"></i>
            </div>
            <div>
              <h6 class="card-subtitle text-muted mb-1">Administrators</h6>
              <h2 class="card-title mb-0"><?php echo $total_admins; ?></h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
