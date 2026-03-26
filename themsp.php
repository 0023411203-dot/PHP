<?php
include 'connect.php';

// Lấy danh sách Danh mục và Thương hiệu từ CSDL để đưa vào menu thả xuống
$ds_danhmuc = $conn->query("SELECT * FROM danh_muc");
$ds_thuonghieu = $conn->query("SELECT * FROM thuong_hieu");

$error_message = "";

if(isset($_POST['submit'])) {
    $ten_sp = $_POST['ten_sp'];
    $gia_ban = $_POST['gia_ban'];
    $so_luong_ton = $_POST['so_luong_ton'];
    
    // Xử lý ID: Nếu người dùng không chọn gì thì lưu là NULL
    $danh_muc_id = empty($_POST['danh_muc_id']) ? 'NULL' : $_POST['danh_muc_id'];
    $thuong_hieu_id = empty($_POST['thuong_hieu_id']) ? 'NULL' : $_POST['thuong_hieu_id'];
    
    $thumbnail_id = 'NULL'; // Mặc định nếu không có ảnh

    // Xử lý upload ảnh
    if(isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['name'] != "") {
        $file_name = basename($_FILES['hinh_anh']['name']);
        $target_file = "image/" . $file_name;
        
        if(move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $target_file)) {
            // Lưu vào bảng quan_ly_hinh_anh
            $conn->query("INSERT INTO quan_ly_hinh_anh (file_name) VALUES ('$file_name')");
            $thumbnail_id = $conn->insert_id; // Lấy ID ảnh vừa thêm
        }
    }

    $sql = "INSERT INTO san_pham (ten_sp, gia_ban, so_luong_ton, danh_muc_id, thuong_hieu_id, thumbnail_id) 
            VALUES ('$ten_sp', $gia_ban, $so_luong_ton, $danh_muc_id, $thuong_hieu_id, $thumbnail_id)";
    
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-products');
        exit();
    } else {
        $error_message = "Lỗi khi thêm: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm Sản Phẩm</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 500px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #17eded; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%;}
        .btn-cancel { background: #ccc; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px; text-align: center; display: block; text-decoration: none; color: black; box-sizing: border-box;}
        .error { color: red; font-size: 14px; margin-bottom: 15px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">THÊM SẢN PHẨM MỚI</h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên sản phẩm</label>
                <input type="text" name="ten_sp" required>
            </div>
            <div class="form-group">
                <label>Giá bán (VNĐ)</label>
                <input type="number" name="gia_ban" required>
            </div>
            <div class="form-group">
                <label>Số lượng tồn</label>
                <input type="number" name="so_luong_ton" value="0" required>
            </div>
            
            <div class="form-group">
                <label>Danh mục</label>
                <select name="danh_muc_id">
                    <option value="">-- Chọn danh mục --</option>
                    <?php 
                    if($ds_danhmuc && $ds_danhmuc->num_rows > 0) {
                        while($dm = $ds_danhmuc->fetch_assoc()) {
                            echo "<option value='{$dm['id']}'>{$dm['ten_danh_muc']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Thương hiệu</label>
                <select name="thuong_hieu_id">
                    <option value="">-- Chọn thương hiệu --</option>
                    <?php 
                    if($ds_thuonghieu && $ds_thuonghieu->num_rows > 0) {
                        while($th = $ds_thuonghieu->fetch_assoc()) {
                            echo "<option value='{$th['id']}'>{$th['ten_thuong_hieu']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="hinh_anh" accept="image/*">
            </div>
            <button type="submit" name="submit" class="btn-submit">Lưu Sản Phẩm</button>
            <a href="admin.php?tab=tab-products" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>