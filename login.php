<!DOCTYPE html>
<html lang="th" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Login Form (Light Theme - Lime)</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        /* ✨ 2. เปลี่ยนพื้นหลัง body เป็นสีเทาอ่อน 
        */
        body {
            font-family: 'Inter', sans-serif; 
            background-color: #f8f9fa; /* สีเทาอ่อน (Bootstrap's light gray) */
        }

        /* กำหนดสี "เขียวตองอ่อน" (Lime Green) */
        :root {
            --lime-green: #A0D468;
            --lime-green-hover: #8CC152;
            
            /* ✨ 3. ปรับสีลิงก์ให้เข้มขึ้นสำหรับพื้นหลังสว่าง */
            --lime-green-link: #6a9f38; 
            --lime-green-link-hover: #5a8f2a;
        }

        /* สไตล์ของปุ่มหลัก (ยังเหมือนเดิม) */
        .btn-lime-green {
            background-color: var(--lime-green);
            border-color: var(--lime-green);
            color: #212529; 
            font-weight: 600;
            padding: 0.75rem 1rem;
        }

        .btn-lime-green:hover {
            background-color: var(--lime-green-hover);
            border-color: var(--lime-green-hover);
            color: #212529;
        }

        /* สไตล์ของลิงก์ (ใช้สีที่เข้มขึ้น) */
        .link-lime-green {
            color: var(--lime-green-link);
            text-decoration: none;
        }
        .link-lime-green:hover {
            color: var(--lime-green-link-hover);
            text-decoration: underline;
        }

        /* ✨ 4. เปลี่ยนพื้นหลังการ์ดเป็นสีขาว 
        */
        .login-card {
            background-color: #ffffff; /* สีขาว */
            border: none;
            max-width: 450px;
            width: 100%;
            /* ใช้ shadow-lg เพื่อให้ดูมีมิติบนพื้นหลังสว่าง */
        }

        /* ✨ 5. ไม่จำเป็นต้องกำหนดสี Floating Labels 
          เพราะ Bootstrap 5 Light Mode
          จัดการให้สวยงามอยู่แล้ว 
        */
        
        /* ✨ 6. ปรับสไตล์ปุ่ม Social Media สำหรับ Light Mode
        */
        .btn-social {
            color: #495057; /* สีไอคอน (เทาเข้ม) */
            background-color: #ffffff;
            border-color: #ced4da; /* สีขอบ (เทาอ่อน) */
            transition: all 0.3s ease;
        }
        .btn-social:hover {
            background-color: #f1f3f5; /* สีพื้นหลังอ่อนๆ เมื่อ hover */
            border-color: #adb5bd;
        }

    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center vh-100 p-3">

    <div class="card login-card shadow-lg p-4 rounded-4">
        <div class="card-body">

            <div class="text-center mb-4">
                <i class="bi bi-box-arrow-in-right display-4"></i>
                <h2 class="mt-2 mb-1 fw-bold">Sign In</h2>
                <p class="text-muted">Welcome back! Please sign in to continue.</p>
            </div>
            
            <form action="check_login.php" method="POST">

                <div class="form-floating mb-3">
                    <input name="username" type="text" class="form-control" id="floatingEmail" placeholder="username" required>
                    <label for="floatingEmail">
                        <i class="bi bi-envelope-fill me-2"></i>Username
                    </label>
                </div>

                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword">
                        <i class="bi bi-key-fill me-2"></i>Password
                    </label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="link-lime-green small">Forgot password?</a>
                </div>

                <div class="d-grid mb-4">
                     <button class="btn btn-lime-green btn-lg" type="submit">Sign In</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted small">Don't have an account? 
                     <a href="register_form.php" class="link-lime-green fw-bold">Sign up now</a>
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>