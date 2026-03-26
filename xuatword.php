<?php
include 'connect.php';
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=bao_cao_doanh_thu.doc");

// Sắp xếp thông minh theo Tháng từ 1 đến 12
$sql = "SELECT * FROM baocao ORDER BY CAST(REPLACE(LOWER(thang), 'tháng ', '') AS UNSIGNED) ASC";
$result = $conn->query($sql);

$stt = 1; // Khởi tạo biến đếm để cột ID nhảy từ 1 trở lên
?>
<meta charset="utf-8"> 
<h2 style="text-align: center;">BÁO CÁO DOANH THU T-HEX PC-WORLD</h2>
<table border="1" style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, Arial; text-align: center;">
    <tr style="background-color: #f2f2f2;">
        <th>ID</th>
        <th>Tháng</th>
        <th>Số đơn</th>
        <th>Doanh thu</th>
        <th>Lợi nhuận</th>
        <th>Tình trạng</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $stt++; ?></td> <td><?php echo $row['thang']; ?></td>
        <td><?php echo $row['so_don']; ?></td>
        <td style="text-align: right; padding-right: 10px;"><?php echo number_format($row['doanh_thu'], 0, ',', '.'); ?>đ</td>
        <td style="text-align: right; padding-right: 10px;"><?php echo number_format($row['loi_nhuan'], 0, ',', '.'); ?>đ</td>
        <td><?php echo $row['tinh_trang']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>