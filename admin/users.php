<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if (empty($_SESSION['username']) || ($_SESSION['status'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include_once '../connect.php';

// --- ส่วนจัดการ Logic (Save, Delete, Update) ---

// 1. สั่งลบผู้ใช้ (Delete)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    // ห้ามลบตัวเอง
    if ($id_to_delete != $_SESSION['id']) {
        $stmt = mysqli_prepare($con, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id_to_delete);
        if(mysqli_stmt_execute($stmt)){
             $_SESSION['success'] = "ลบผู้ใช้เรียบร้อยแล้ว";
        } else {
             $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ";
        }
    } else {
        $_SESSION['error'] = "ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้";
    }
    header("Location: users.php");
    exit();
}

// 2. บันทึกการแก้ไข (Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];
    $new_password = $_POST['password'];

    // ตรวจสอบ Username ซ้ำ (ยกเว้น ID เดิม)
    $check = mysqli_query($con, "SELECT id FROM users WHERE username = '$username' AND id != $id");
    if (mysqli_num_rows($check) > 0) {
        $error_msg = "Username นี้มีผู้ใช้งานแล้ว";
    } else {
        // ถ้ามีการกรอกรหัสผ่านใหม่ ให้ Hash
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET username=?, fullname=?, phone=?, status=?, password=? WHERE id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssi', $username, $fullname, $phone, $status, $hashed_password, $id);
        } else {
            // ถ้าไม่แก้รหัสผ่าน
            $sql = "UPDATE users SET username=?, fullname=?, phone=?, status=? WHERE id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssi', $username, $fullname, $phone, $status, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
            header("Location: users.php"); // กลับไปหน้ารายการ
            exit();
        } else {
            $error_msg = "เกิดข้อผิดพลาด: " . mysqli_error($con);
        }
    }
}

// --- ส่วนเตรียมข้อมูลสำหรับแสดงผล ---

// โหมดแก้ไข: ดึงข้อมูลผู้ใช้ตาม ID
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $edit_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $edit_user = mysqli_fetch_assoc($res);
}

// โหมดค้นหาและแสดงรายการ
$q = '';
if (!empty($_GET['q'])) {
    $q = trim($_GET['q']);
    $like = "%" . $q . "%";
    $stmt = mysqli_prepare($con, "SELECT id, username, phone, fullname, status FROM users WHERE username LIKE ? OR fullname LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($con, "SELECT id, username, phone, fullname, status FROM users ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php"><i class="bi bi-shield-lock-fill"></i> Admin Panel</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link active" href="users.php">Users</a></li>
        </ul>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($error_msg) || isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error_msg ?? $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($edit_user): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit User: <?php echo htmlspecialchars($edit_user['username']); ?></h5>
        </div>
        <div class="card-body">
            <form method="POST" action="users.php">
                <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($edit_user['username']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($edit_user['fullname']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($edit_user['phone']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="user" <?php if($edit_user['status']=='user') echo 'selected'; ?>>User</option>
                            <option value="admin" <?php if($edit_user['status']=='admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label text-danger">Change Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="New password...">
                    </div>
                </div>
                <button type="submit" name="update_user" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$edit_user): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-people-fill"></i> Users Management</h4>
                <a class="btn btn-success btn-sm" href="../register_form.php"><i class="bi bi-plus-circle"></i> Add User</a>
            </div>
        </div>
        <div class="card-body">
            <form class="row g-2 mb-3" method="get" action="users.php">
              <div class="col-auto">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Search user...">
              </div>
              <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                <a class="btn btn-outline-secondary" href="users.php">Reset</a>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                      <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <?php if($row['status'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-success">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                          <a class="btn btn-sm btn-warning text-dark" 
                             href="users.php?action=edit&id=<?php echo $row['id']; ?>">
                             <i class="bi bi-pencil"></i> Edit
                          </a>
                          
                          <a class="btn btn-sm btn-danger" 
                             href="users.php?action=delete&id=<?php echo $row['id']; ?>" 
                             onclick="return confirm('ยืนยันการลบผู้ใช้นี้?');">
                             <i class="bi bi-trash"></i> Delete
                          </a>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูลผู้ใช้งาน</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>