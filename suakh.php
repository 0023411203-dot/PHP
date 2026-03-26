<?php
include 'connect.php';

// Lấy ID khách hàng cần sửa
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) { 
    header('Location: admin.php?tab=tab-customers'); 
    exit(); 
}

// Lấy thông tin khách hàng hiện tại
$kh = $conn->query("SELECT * FROM khach_hang WHERE id = $id")->fetch_assoc();
$error_message = "";

// Xử lý khi bấm nút Cập Nhật
if(isset($_POST['submit'])) {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $mat_khau = $_POST['mat_khau'];

    $sql = "UPDATE khach_hang SET ho_ten='$ho_ten', email='$email', so_dien_thoai='$so_dien_thoai', mat_khau='$mat_khau' WHERE id=$id";
    
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-customers');
        exit();
    } else {
        $error_message = "Lỗi cập nhật: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa Khách Hàng</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);}
        .form-group { margin-bottom: 15px; } 
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px;}
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #10B981; color: white; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
        .error { color: red; text-align: center; font-weight: bold; margin-bottom: 15px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">SỬA KHÁCH HÀNG #<?php echo $id; ?></h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="ho_ten" value="<?php echo $kh['ho_ten']; ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $kh['email']; ?>" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="so_dien_thoai" value="<?php echo $kh['so_dien_thoai']; ?>" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="text" name="mat_khau" value="<?php echo $kh['mat_khau']; ?>" required>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Cập Nhật</button>
            <a href="admin.php?tab=tab-customers" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>