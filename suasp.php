<?php
include 'connect.php';

// Lấy ID sản phẩm cần sửa
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header('Location: admin.php?tab=tab-products');
    exit();
}

// Lấy thông tin sản phẩm hiện tại
$sp = $conn->query("SELECT * FROM san_pham WHERE id = $id")->fetch_assoc();

// Lấy danh sách Danh mục và Thương hiệu để làm menu thả xuống
$ds_danhmuc = $conn->query("SELECT * FROM danh_muc");
$ds_thuonghieu = $conn->query("SELECT * FROM thuong_hieu");

$error_message = "";

if(isset($_POST['submit'])) {
    $ten_sp = $_POST['ten_sp'];
    $gia_ban = $_POST['gia_ban'];
    $so_luong_ton = $_POST['so_luong_ton'];
    
    // Xử lý ID: Nếu không chọn thì gán là NULL
    $danh_muc_id = empty($_POST['danh_muc_id']) ? 'NULL' : $_POST['danh_muc_id'];
    $thuong_hieu_id = empty($_POST['thuong_hieu_id']) ? 'NULL' : $_POST['thuong_hieu_id'];
    
    // Nếu có up ảnh mới
    if(isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['name'] != "") {
        $file_name = basename($_FILES['hinh_anh']['name']);
        if(move_uploaded_file($_FILES['hinh_anh']['tmp_name'], "image/" . $file_name)) {
            // Thêm ảnh mới vào kho hình ảnh
            $conn->query("INSERT INTO quan_ly_hinh_anh (file_name) VALUES ('$file_name')");
            $new_img_id = $conn->insert_id;
            
            // Cập nhật ID ảnh mới cho sản phẩm
            $conn->query("UPDATE san_pham SET thumbnail_id = $new_img_id WHERE id = $id");
        }
    }

    // Cập nhật các thông tin bằng văn bản
    $sql = "UPDATE san_pham SET ten_sp='$ten_sp', gia_ban=$gia_ban, so_luong_ton=$so_luong_ton, danh_muc_id=$danh_muc_id, thuong_hieu_id=$thuong_hieu_id WHERE id=$id";
    
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-products');
        exit();
    } else {
        $error_message = "Lỗi cập nhật: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa Sản Phẩm</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 500px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #10B981; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%;}
        .btn-cancel { background: #ccc; border: none; padding: 10px; font-weight: bold; border-radius: 4px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px;}
        .error { color: red; font-size: 14px; margin-bottom: 15px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">SỬA SẢN PHẨM #<?php echo $id; ?></h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Tên sản phẩm</label>
                <input type="text" name="ten_sp" value="<?php echo $sp['ten_sp']; ?>" required>
            </div>
            <div class="form-group">
                <label>Giá bán (VNĐ)</label>
                <input type="number" name="gia_ban" value="<?php echo $sp['gia_ban']; ?>" required>
            </div>
            <div class="form-group">
                <label>Số lượng tồn</label>
                <input type="number" name="so_luong_ton" value="<?php echo $sp['so_luong_ton']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Danh mục</label>
                <select name="danh_muc_id">
                    <option value="">-- Chọn danh mục --</option>
                    <?php 
                    if($ds_danhmuc && $ds_danhmuc->num_rows > 0) {
                        while($dm = $ds_danhmuc->fetch_assoc()) {
                            $selected = ($sp['danh_muc_id'] == $dm['id']) ? "selected" : "";
                            echo "<option value='{$dm['id']}' $selected>{$dm['ten_danh_muc']}</option>";
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
                            $selected = ($sp['thuong_hieu_id'] == $th['id']) ? "selected" : "";
                            echo "<option value='{$th['id']}' $selected>{$th['ten_thuong_hieu']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Ảnh mới (Bỏ trống nếu không muốn đổi ảnh)</label>
                <input type="file" name="hinh_anh" accept="image/*">
            </div>
            <button type="submit" name="submit" class="btn-submit">Cập Nhật</button>
            <a href="admin.php?tab=tab-products" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>