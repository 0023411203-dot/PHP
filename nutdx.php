<?php
session_start();

// Xóa sạch các biến session (user_name, role...)
session_unset(); 

// Hủy hoàn toàn phiên làm việc hiện tại
session_destroy(); 

// Chuyển hướng về trang đăng nhập
header('Location: dangnhap.php'); 
exit();
?>