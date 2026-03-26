<?php
include 'connect.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nv = $conn->query("SELECT * FROM nhan_vien WHERE id = $id")->fetch_assoc();
$ds_role = $conn->query("SELECT * FROM roles");

if(isset($_POST['submit'])) {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $role_id = empty($_POST['role_id']) ? 'NULL' : $_POST['role_id'];

    if(isset($_FILES['avatar']) && $_FILES['avatar']['name'] != "") {
        $file_name = basename($_FILES['avatar']['name']);
        if(move_uploaded_file($_FILES['avatar']['tmp_name'], "image/" . $file_name)) {
            $conn->query("INSERT INTO quan_ly_hinh_anh (file_name) VALUES ('$file_name')");
            $new_avatar_id = $conn->insert_id;
            $conn->query("UPDATE nhan_vien SET avatar_id = $new_avatar_id WHERE id = $id");
        }
    }

    $sql = "UPDATE nhan_vien SET ho_ten='$ho_ten', email='$email', mat_khau='$mat_khau', role_id=$role_id WHERE id=$id";
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-employees');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa Nhân Viên</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px;}
        .form-group { margin-bottom: 15px; } .form-group label { display: block; font-weight: bold; margin-bottom: 5px;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #10B981; color: white; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">SỬA NHÂN VIÊN #<?php echo $id; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Họ tên</label><input type="text" name="ho_ten" value="<?php echo $nv['ho_ten']; ?>" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $nv['email']; ?>" required></div>
            <div class="form-group"><label>Mật khẩu</label><input type="text" name="mat_khau" value="<?php echo $nv['mat_khau']; ?>" required></div>
            <div class="form-group">
                <label>Vai trò</label>
                <select name="role_id" required>
                    <?php while($r = $ds_role->fetch_assoc()) { 
                        $sel = ($nv['role_id'] == $r['id']) ? "selected" : "";
                        echo "<option value='{$r['id']}' $sel>{$r['role_name']}</option>"; 
                    } ?>
                </select>
            </div>
            <div class="form-group"><label>Đổi ảnh mới (nếu muốn)</label><input type="file" name="avatar" accept="image/*"></div>
            <button type="submit" name="submit" class="btn-submit">Cập Nhật</button>
            <a href="admin.php?tab=tab-employees" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>