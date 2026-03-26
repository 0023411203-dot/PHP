<?php
include 'connect.php';
$id = $_GET['id'];
$bc = $conn->query("SELECT * FROM baocao WHERE id = $id")->fetch_assoc();

if(isset($_POST['submit'])) {
    $thang = $_POST['thang']; $so_don = $_POST['so_don'];
    $doanh_thu = $_POST['doanh_thu']; $loi_nhuan = $_POST['loi_nhuan']; $tinh_trang = $_POST['tinh_trang'];

    $conn->query("UPDATE baocao SET thang='$thang', so_don=$so_don, doanh_thu=$doanh_thu, loi_nhuan=$loi_nhuan, tinh_trang='$tinh_trang' WHERE id=$id");
    header('Location: admin.php?tab=tab-stats');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sửa Báo Cáo</title>
    <style>
        body {background: #f3f4f6; font-family: 'Inter', sans-serif;}
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px;}
        .form-group { margin-bottom: 15px; } .form-group label { display: block; font-weight: bold;}
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        .btn-submit { background: #10B981; color: white; border: none; padding: 10px; font-weight: bold; border-radius: 4px; width: 100%; cursor: pointer;}
        .btn-cancel { background: #e5e7eb; padding: 10px; display: block; text-align: center; color: black; text-decoration: none; margin-top: 10px; border-radius: 4px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center;">SỬA BÁO CÁO</h2>
        <form method="POST">
            <div class="form-group"><label>Tháng</label><input type="text" name="thang" value="<?php echo $bc['thang']; ?>" required></div>
            <div class="form-group"><label>Số đơn</label><input type="number" name="so_don" value="<?php echo $bc['so_don']; ?>" required></div>
            <div class="form-group"><label>Doanh thu</label><input type="number" name="doanh_thu" value="<?php echo $bc['doanh_thu']; ?>" required></div>
            <div class="form-group"><label>Lợi nhuận</label><input type="number" name="loi_nhuan" value="<?php echo $bc['loi_nhuan']; ?>" required></div>
            <div class="form-group">
                <label>Tình trạng</label>
                <select name="tinh_trang">
                    <option value="Ổn định" <?php if($bc['tinh_trang']=='Ổn định') echo 'selected';?>>Ổn định</option>
                    <option value="Tăng trưởng" <?php if($bc['tinh_trang']=='Tăng trưởng') echo 'selected';?>>Tăng trưởng</option>
                    <option value="Sụt giảm" <?php if($bc['tinh_trang']=='Sụt giảm') echo 'selected';?>>Sụt giảm</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn-submit">Cập Nhật</button>
            <a href="admin.php?tab=tab-stats" class="btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>