<?php
include 'connect.php';

// Lấy danh sách Khách hàng để đưa vào menu thả xuống
$ds_khachhang = $conn->query("SELECT id, ho_ten, so_dien_thoai FROM khach_hang ORDER BY ho_ten ASC");

$error_message = "";

if(isset($_POST['submit'])) {
    // Đã bắt buộc chọn nên không cần kiểm tra rỗng nữa
    $khach_hang_id = $_POST['khach_hang_id'];
    $tong_tien = $_POST['tong_tien'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "INSERT INTO don_hang (khach_hang_id, tong_tien, trang_thai) 
            VALUES ($khach_hang_id, $tong_tien, $trang_thai)";
    
    if($conn->query($sql)) {
        header('Location: admin.php?tab=tab-orders');
        exit();
    } else {
        $error_message = "Lỗi thêm đơn hàng: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm Đơn Hàng</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);}
        .form-group { margin-bottom: 15px; } 
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #17eded; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
        .error { color: red; font-size: 14px; margin-bottom: 15px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">THÊM ĐƠN HÀNG</h2>
        
        <?php if($error_message != "") echo "<div class='error'>$error_message</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Khách hàng</label>
                <select name="khach_hang_id" required>
                    <option value="" disabled selected>-- Chọn khách hàng --</option>
                    <?php 
                    if($ds_khachhang && $ds_khachhang->num_rows > 0) {
                        while($kh = $ds_khachhang->fetch_assoc()) {
                            echo "<option value='{$kh['id']}'>{$kh['ho_ten']} - {$kh['so_dien_thoai']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tổng tiền (VNĐ)</label>
                <input type="number" name="tong_tien" required>
            </div>
            
            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai">
                    <option value="0">Chờ xử lý</option>
                    <option value="1">Đang giao</option>
                    <option value="2">Hoàn thành</option>
                    <option value="3">Đã hủy</option>
                </select>
            </div>
            
            <button type="submit" name="submit" class="btn-submit">Lưu Đơn Hàng</button>
            <a href="admin.php?tab=tab-orders" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>