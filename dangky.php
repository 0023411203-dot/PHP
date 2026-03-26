<?php
include 'connect.php';
if (isset($_POST['dangky'])) {
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass = $_POST['password'];

    $sql = "INSERT INTO khach_hang (ho_ten, email, mat_khau, so_dien_thoai) 
            VALUES ('$ho_ten', '$email', '$pass', '$phone')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đăng ký thành công!'); window.location='dangnhap.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"><title>T-HEX Register</title><link rel="stylesheet" href="dangky.css">
</head>
<body>
<div class="container">
    <div class="left"></div>
    <div class="right"><form action="dangky.php" method="POST" class="card">
        <img src="image/logodn.png" class="logo"><h2>ĐĂNG KÝ</h2>
        <label>Họ tên</label><input type="text" name="ho_ten" required>
        <label>Tài khoản (Email)</label><input type="email" name="email" required>
        <label>Số điện thoại</label><input type="text" name="phone" required>
        <label>Mật khẩu</label><input type="password" name="password" required>
        <button type="submit" name="dangky">Đăng ký ngay</button>
        <p class="login-link">Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></p>
    </form></div>
</div>
</body>
</html>