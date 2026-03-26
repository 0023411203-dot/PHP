<?php
include 'connect.php';
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM nhan_vien WHERE id = $id");
}
header('Location: admin.php?tab=tab-employees');
exit();
?>