<?php
include 'connect.php';
$result = $conn->query("SELECT * FROM baocao ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>In báo cáo PDF</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #17eded; color: black; }
        .header { text-align: center; margin-bottom: 30px; }
        /* Ẩn nút in khi đang in */
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>BÁO CÁO TÀI CHÍNH T-HEX PC-WORLD</h1>
        <p>Ngày xuất: <?php echo date('d/m/Y H:i'); ?></p>
        <button onclick="window.print()" class="no-print" style="padding: 10px 20px; cursor:pointer;">Bấm để Xuất PDF / In</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tháng</th>
                <th>Số đơn hàng</th>
                <th>Doanh thu</th>
                <th>Lợi nhuận</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo $row['thang']; ?></td>
                <td><?php echo $row['so_don']; ?> đơn</td>
                <td><?php echo number_format($row['doanh_thu'], 0, ',', '.'); ?>đ</td>
                <td><?php echo number_format($row['loi_nhuan'], 0, ',', '.'); ?>đ</td>
                <td><?php echo $row['tinh_trang']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>