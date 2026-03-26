<?php
session_start();
include 'connect.php';

$error_message = "";
// Lấy danh sách Role để đổ vào Select box
$ds_role = $conn->query("SELECT * FROM roles");

if(isset($_POST['submit'])) {
    // Lấy dữ liệu và làm sạch
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mat_khau = mysqli_real_escape_string($conn, $_POST['mat_khau']);
    $role_id = $_POST['role_id'];
    $avatar_id = "NULL"; // Mặc định là NULL nếu không up ảnh

    // Xử lý upload ảnh đại diện
    if(isset($_FILES['avatar']) && $_FILES['avatar']['name'] != "") {
        $file_name = time() . "_" . basename($_FILES['avatar']['name']); // Thêm time() để tránh trùng tên file
        $target_file = "image/" . $file_name;
        $file_type = $_FILES['avatar']['type'];

        if(move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            // Lưu vào bảng quản lý hình ảnh trước để lấy ID
            $sql_img = "INSERT INTO quan_ly_hinh_anh (file_name, file_url, file_type) 
                        VALUES ('$file_name', '$target_file', '$file_type')";
            if($conn->query($sql_img)) {
                $avatar_id = $conn->insert_id;
            }
        }
    }

    // Thêm vào bảng nhân viên
    $sql = "INSERT INTO nhan_vien (ho_ten, email, mat_khau, role_id, avatar_id) 
            VALUES ('$ho_ten', '$email', '$mat_khau', $role_id, $avatar_id)";
    
    if($conn->query($sql)) {
        echo "<script>alert('Thêm nhân viên thành công!'); window.location.href='admin.php?tab=tab-employees';</script>";
        exit();
    } else {
        $error_message = "Lỗi database: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>T-HEX | Thêm Nhân Viên</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f3f4f6; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .form-container { width: 100%; max-width: 450px; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-top: 5px solid #17eded; }
        h2 { text-align: center; color: #333; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; transition: 0.3s; outline: none; }
        .form-group input:focus, .form-group select:focus { border-color: #17eded; box-shadow: 0 0 5px rgba(23, 237, 237, 0.3); }
        .btn-submit { background: #17eded; border: none; padding: 14px; font-weight: bold; border-radius: 8px; width: 100%; cursor: pointer; font-size: 16px; transition: 0.3s; }
        .btn-submit:hover { background: #12c7c7; transform: translateY(-2px); }
        .btn-cancel { background: #eee; padding: 12px; display: block; text-align: center; color: #666; text-decoration: none; margin-top: 15px; border-radius: 8px; font-weight: 600; font-size: 14px; }
        .btn-cancel:hover { background: #e0e0e0; }
        .error { color: #ff4d4d; background: #fff5f5; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-size: 13px; border: 1px solid #ffcccc; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Thêm Nhân Viên</h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Họ và Tên</label>
                <input type="text" name="ho_ten" placeholder="Nhập họ tên nhân viên" required>
            </div>
            
            <div class="form-group">
                <label>Tài khoản (Email)</label>
                <input type="email" name="email" placeholder="nhanvien@thex.com" required>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu đăng nhập</label>
                <input type="text" name="mat_khau" placeholder="Nhập mật khẩu cấp cho NV" required>
            </div>
            
            <div class="form-group">
                <label>Quyền hạn (Role)</label>
                <select name="role_id" required>
                    <option value="" disabled selected>-- Chọn vai trò --</option>
                    <?php 
                    if($ds_role) {
                        while($r = $ds_role->fetch_assoc()) {
                            echo "<option value='{$r['id']}'>{$r['role_name']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Ảnh đại diện</label>
                <input type="file" name="avatar" accept="image/*" style="border: none; padding: 5px 0;">
            </div>
            
            <button type="submit" name="submit" class="btn-submit">LƯU NHÂN VIÊN</button>
            <a href="admin.php?tab=tab-employees" class="btn-cancel">Quay lại trang quản trị</a>
        </form>
    </div>
</body>
</html>