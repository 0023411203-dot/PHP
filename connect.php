<?php
$host = "localhost";
$username = "root"; // Tên đăng nhập mặc định của XAMPP
$password = "";     // Mật khẩu mặc định của XAMPP là rỗng
$dbname = "thex";  // Tên cơ sở dữ liệu bạn đã tạo

// Tạo biến kết nối $conn
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Set charset để không bị lỗi font tiếng Việt
$conn->set_charset("utf8mb4");
?>