<?php
include 'connect.php';
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM san_pham WHERE id = $id");
}
header('Location: admin.php?tab=tab-products');
?>