<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    // 1. KIỂM TRA ADMIN (Đặc cách: Không cần mật khẩu)
    if ($user === 'admin@thex.com') {
        $_SESSION['user_name'] = "Xuân Phát";
        $_SESSION['user_role'] = "Admin";
        header("Location: admin.php");
        exit();
    }

    // 2. KIỂM TRA NHÂN VIÊN (Tài khoản Admin cấp - Cần mật khẩu)
    $sql_nv = "SELECT nv.*, r.role_name FROM nhan_vien nv 
               JOIN roles r ON nv.role_id = r.id 
               WHERE nv.email = '$user' AND nv.mat_khau = '$pass'";
    $res_nv = mysqli_query($conn, $sql_nv);
    if (mysqli_num_rows($res_nv) > 0) {
        $row = mysqli_fetch_assoc($res_nv);
        $_SESSION['user_name'] = $row['ho_ten'];
        $_SESSION['user_role'] = $row['role_name'];
        header("Location: staff.php");
        exit();
    }

    // 3. KIỂM TRA KHÁCH HÀNG (Tự đăng ký - Cần mật khẩu)
    $sql_kh = "SELECT * FROM khach_hang WHERE email = '$user' AND mat_khau = '$pass'";
    $res_kh = mysqli_query($conn, $sql_kh);
    if (mysqli_num_rows($res_kh) > 0) {
        $row = mysqli_fetch_assoc($res_kh);
        $_SESSION['user_name'] = $row['ho_ten'];
        $_SESSION['user_role'] = "Khách hàng";
        header("Location: main.php");
        exit();
    } else {
        echo "<script>alert('Tài khoản hoặc mật khẩu không chính xác!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><title>T-HEX Login</title><link rel="stylesheet" href="dangnhap.css">
</head>
<body>
<div class="container">
    <div class="left"></div>
    <div class="right"><form action="dangnhap.php" method="POST" class="card">
        <img src="image/logodn.png" class="logo"><h2>T-HEX</h2><p class="sub">PC WORLD</p>
        <div class="input-box"><span>👤</span><input type="text" name="username" placeholder="Nhập tài khoản (Email)" required></div>
        <div class="input-box"><span>🔒</span><input type="password" name="password" placeholder="Nhập mật khẩu (Admin bỏ trống)"></div>
        <button type="submit" name="login" class="login-btn">Đăng nhập</button>
        <div class="links"><a href="dangky.php">Tạo tài khoản mới</a></div>
    </form></div>
</div>
</body>
</html>