<?php
include 'connect.php';
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM don_hang WHERE id = $id");
}
header('Location: admin.php?tab=tab-orders');
?>