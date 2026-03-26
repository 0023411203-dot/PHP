<?php
include 'connect.php';
if(isset($_POST['submit'])) {
    $thang = $_POST['thang'];
    $so_don = $_POST['so_don'];
    $doanh_thu = $_POST['doanh_thu'];
    $loi_nhuan = $_POST['loi_nhuan'];
    $tinh_trang = $_POST['tinh_trang'];

    $conn->query("INSERT INTO baocao (thang, so_don, doanh_thu, loi_nhuan, tinh_trang) VALUES ('$thang', $so_don, $doanh_thu, $loi_nhuan, '$tinh_trang')");
    header('Location: admin.php?tab=tab-stats');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm Báo Cáo</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px;}
        .form-group { margin-bottom: 15px; } .form-group label { display: block; font-weight: bold;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #F97316; color: white; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">THÊM BÁO CÁO</h2>
        <form method="POST">
            <div class="form-group"><label>Tháng</label><input type="text" name="thang" placeholder="VD: Tháng 1" required></div>
            <div class="form-group"><label>Số đơn hàng</label><input type="number" name="so_don" required></div>
            <div class="form-group"><label>Doanh thu (VNĐ)</label><input type="number" name="doanh_thu" required></div>
            <div class="form-group"><label>Lợi nhuận (VNĐ)</label><input type="number" name="loi_nhuan" required></div>
            <div class="form-group">
                <label>Tình trạng</label>
                <select name="tinh_trang">
                    <option value="Ổn định">Ổn định</option>
                    <option value="Tăng trưởng">Tăng trưởng</option>
                    <option value="Sụt giảm">Sụt giảm</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn-submit">Lưu Báo Cáo</button>
            <a href="admin.php?tab=tab-stats" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>