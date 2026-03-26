<?php
include 'connect.php';

// Thiết lập header để trình duyệt hiểu đây là file tải về
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=baocao_doanhthu.csv');

// Tạo luồng ghi file
$output = fopen('php://output', 'w');

// Ghi dòng tiêu đề của các cột (Có hỗ trợ tiếng Việt UTF-8)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Fix lỗi font tiếng Việt trên Excel
fputcsv($output, array('ID', 'Tháng', 'Số đơn hàng', 'Doanh thu (VNĐ)', 'Lợi nhuận (VNĐ)', 'Tình trạng'));

// Lấy dữ liệu
$query = "SELECT * FROM baocao ORDER BY id DESC";
$result = $conn->query($query);

// Ghi từng dòng dữ liệu vào file Excel
while($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
?>