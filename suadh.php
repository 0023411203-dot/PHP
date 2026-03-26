<?php
include 'connect.php';

// Lấy ID đơn hàng cần sửa
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id == 0) {
    header('Location: admin.php?tab=tab-orders');
    exit();
}

$dh = $conn->query("SELECT * FROM don_hang WHERE id = $id")->fetch_assoc();

// Lấy danh sách Khách hàng
$ds_khachhang = $conn->query("SELECT id, ho_ten, so_dien_thoai FROM khach_hang ORDER BY ho_ten ASC");

$error_message = "";

if(isset($_POST['submit'])) {
    $khach_hang_id = $_POST['khach_hang_id'];
    $tong_tien = $_POST['tong_tien'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "UPDATE don_hang SET khach_hang_id=$khach_hang_id, tong_tien=$tong_tien, trang_thai=$trang_thai WHERE id=$id";
    
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-orders');
        exit();
    } else {
        $error_message = "Lỗi cập nhật: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cập Nhật Đơn Hàng</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);}
        .form-group { margin-bottom: 15px; } 
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #10B981; color: white; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
        .error { color: red; font-size: 14px; margin-bottom: 15px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">SỬA ĐƠN HÀNG #<?php echo $id; ?></h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Khách hàng</label>
                <select name="khach_hang_id" required>
                    <option value="" disabled <?php if(empty($dh['khach_hang_id'])) echo 'selected'; ?>>-- Chọn khách hàng --</option>
                    <?php 
                    if($ds_khachhang && $ds_khachhang->num_rows > 0) {
                        while($kh = $ds_khachhang->fetch_assoc()) {
                            $selected = ($dh['khach_hang_id'] == $kh['id']) ? "selected" : "";
                            echo "<option value='{$kh['id']}' $selected>{$kh['ho_ten']} - {$kh['so_dien_thoai']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tổng tiền (VNĐ)</label>
                <input type="number" name="tong_tien" value="<?php echo $dh['tong_tien']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai">
                    <option value="0" <?php if($dh['trang_thai'] == 0) echo 'selected'; ?>>Chờ xử lý</option>
                    <option value="1" <?php if($dh['trang_thai'] == 1) echo 'selected'; ?>>Đang giao</option>
                    <option value="2" <?php if($dh['trang_thai'] == 2) echo 'selected'; ?>>Hoàn thành</option>
                    <option value="3" <?php if($dh['trang_thai'] == 3) echo 'selected'; ?>>Đã hủy</option>
                </select>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Cập Nhật Đơn Hàng</button>
            <a href="admin.php?tab=tab-orders" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>