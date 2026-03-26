<?php
session_start();
include 'connect.php';

// --- PHẦN XỬ LÝ TÌM KIẾM ---
$search_all = isset($_GET['search_all']) ? $_GET['search_all'] : '';
$search_sp = isset($_GET['search_sp']) ? $_GET['search_sp'] : $search_all;
$search_dh = isset($_GET['search_dh']) ? $_GET['search_dh'] : $search_all;
$search_kh = isset($_GET['search_kh']) ? $_GET['search_kh'] : $search_all;
$search_nv = isset($_GET['search_nv']) ? $_GET['search_nv'] : $search_all;
$search_bc = isset($_GET['search_bc']) ? $_GET['search_bc'] : $search_all;

// --- TRUY VẤN DỮ LIỆU (Sắp xếp tăng dần từ 1 trở lên) ---

// 1. SẢN PHẨM 
$sql_sp = "SELECT sp.id, sp.ten_sp, sp.gia_ban, sp.so_luong_ton, 
           IFNULL(dm.ten_danh_muc, 'Chưa có') AS ten_danh_muc, 
           IFNULL(th.ten_thuong_hieu, 'Chưa có') AS ten_thuong_hieu, 
           IFNULL(img.file_name, 'default.png') AS hinh_anh 
           FROM san_pham sp 
           LEFT JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
           LEFT JOIN thuong_hieu th ON sp.thuong_hieu_id = th.id 
           LEFT JOIN quan_ly_hinh_anh img ON sp.thumbnail_id = img.id 
           WHERE sp.ten_sp LIKE '%$search_sp%' OR dm.ten_danh_muc LIKE '%$search_sp%' 
           ORDER BY sp.id ASC";
$result_sp = $conn->query($sql_sp);

// 2. ĐƠN HÀNG
$sql_dh = "SELECT dh.id, IFNULL(kh.ho_ten, 'Khách vãng lai') AS ho_ten, dh.ngay_dat, dh.tong_tien, dh.trang_thai 
           FROM don_hang dh 
           LEFT JOIN khach_hang kh ON dh.khach_hang_id = kh.id 
           WHERE dh.id LIKE '%$search_dh%' OR kh.ho_ten LIKE '%$search_dh%' 
           ORDER BY dh.id ASC";
$result_dh = $conn->query($sql_dh);

// 3. KHÁCH HÀNG 
$sql_kh = "SELECT id, ho_ten, email, so_dien_thoai, mat_khau 
           FROM khach_hang 
           WHERE ho_ten LIKE '%$search_kh%' OR email LIKE '%$search_kh%' OR so_dien_thoai LIKE '%$search_kh%' 
           ORDER BY id ASC";
$result_kh = $conn->query($sql_kh);

// 4. NHÂN VIÊN 
$sql_nv = "SELECT nv.id, nv.ho_ten, nv.email, nv.mat_khau, 
           IFNULL(r.role_name, 'Nhân viên') AS role_name, 
           IFNULL(img.file_name, 'avatar.jpg') AS avatar 
           FROM nhan_vien nv 
           LEFT JOIN roles r ON nv.role_id = r.id 
           LEFT JOIN quan_ly_hinh_anh img ON nv.avatar_id = img.id 
           WHERE nv.ho_ten LIKE '%$search_nv%' OR nv.email LIKE '%$search_nv%' OR r.role_name LIKE '%$search_nv%' 
           ORDER BY nv.id ASC";
$result_nv = $conn->query($sql_nv);

// 5. THỐNG KÊ BÁO CÁO 
$sql_bc = "SELECT * FROM baocao 
           WHERE thang LIKE '%$search_bc%' OR tinh_trang LIKE '%$search_bc%' 
           ORDER BY CAST(REPLACE(LOWER(thang), 'tháng ', '') AS UNSIGNED) ASC";
$result_bc = $conn->query($sql_bc);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản trị T-HEX</title>
    <link rel="stylesheet" href="Admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar { background-color: #17eded !important; }
        .menu::-webkit-scrollbar { display: none; }
        .menu { -ms-overflow-style: none; scrollbar-width: none; }
        .search-form { display: flex; gap: 10px; width: 100%; border: none; background: transparent; padding: 0; margin-bottom: 24px; }
        .header .search-container { position: relative; flex: 1; max-width: 500px; display: flex; align-items: center; }
        .header .search-container form { width: 100%; display: flex; align-items: center; position: relative; margin: 0; }
        #search-results-dropdown { position: absolute; top: 110%; left: 0; width: 100%; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 9999; display: none; max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; }
        .search-item { padding: 12px 15px; border-bottom: 1px solid #f3f4f6; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .search-item:hover { background: #f9fafb; }
        .search-item .type-tag { font-size: 9px; padding: 2px 6px; border-radius: 4px; font-weight: 800; text-transform: uppercase; min-width: 35px; text-align: center; }
        .tag-sp { background: #dbeafe; color: #1e40af; } .tag-dh { background: #fef3c7; color: #92400e; } .tag-kh { background: #dcfce7; color: #166534; } .tag-nv { background: #f3e8ff; color: #6b21a8; } .tag-bc { background: #fee2e2; color: #991b1b; }
        .search-item .title { font-weight: 600; font-size: 13px; color: #111827; } .search-item .sub { font-size: 11px; color: #6b7280; }
        .avatar-thumb { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; display: block; margin: 0 auto; }
        
        .export-dropdown { position: relative; display: inline-block; }
        .export-menu { 
            display: none; position: absolute; top: 110%; right: 0; 
            background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            z-index: 1000; min-width: 170px; border: 1px solid #e5e7eb; overflow: hidden;
        }
        .export-menu a { 
            display: block; padding: 12px 16px; color: #374151; 
            text-decoration: none; font-size: 14px; transition: background 0.2s; text-align: left;
        }
        .export-menu a:hover { background: #f3f4f6; }
        .export-menu a:not(:last-child) { border-bottom: 1px solid #f3f4f6; }
    </style>
</head>
<body>
    
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo-section">
                <div class="logo-image"><img src="./image/logodn.png" alt="T-HEX Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/906/906343.png'"></div>
                <div class="logo-text"><p class="company-name">T-HEX</p><p class="company-subtitle">PC-WORLD</p></div>
            </div>
            <nav class="menu">
                <button class="menu-item active" onclick="openTab(event, 'tab-overview')">
                    <img src="./image/Nha.png" alt=""><span>Tổng quan</span>
                </button>
                <button class="menu-item" onclick="openTab(event, 'tab-products')">
                    <img src="./image/store.png" alt=""><span>Quản lý sản phẩm</span>
                </button>
                <button class="menu-item" onclick="openTab(event, 'tab-orders')">
                    <img src="./image/dathang.png" alt=""><span>Quản lý đơn hàng</span>
                </button>
                <button class="menu-item" onclick="openTab(event, 'tab-customers')">
                    <img src="./image/customer.png" alt=""><span>Quản lý khách hàng</span>
                </button>
                <button class="menu-item" onclick="openTab(event, 'tab-employees')">
                    <img src="./image/nhanvien.png" alt=""><span>Quản lý nhân viên</span>
                </button>
                <button class="menu-item" onclick="openTab(event, 'tab-stats')">
                    <img src="./image/transaction.png" alt=""><span>Thống kê</span>
                </button>
            </nav>
            <div class="logout-section">
                <button class="menu-item" onclick="if(confirm('Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?')) window.location.href='nutdx.php'">
                    <img src="./image/logout.png" alt=""><span>ĐĂNG XUẤT</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="search-container">
                    <form action="admin.php" method="GET" id="mainSearchForm">
                        <img src="./image/find.jpg" alt="" class="search-icon">
                        <input type="text" name="search_all" id="globalSearchInput" placeholder="Tìm kiếm nhanh mọi thứ..." class="search-input" autocomplete="off" value="<?php echo htmlspecialchars($search_all); ?>">
                    </form>
                    <div id="search-results-dropdown"></div>
                </div>
                <div class="user-profile">
                    <div class="notification-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span class="notification-badge"></span>
                    </div>
                    <div class="user-info">
                        <div class="user-details">
                            <p class="user-name"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Xuân Phát"; ?></p>
                            <p class="user-role"><?php echo isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "Admin"; ?></p>
                        </div>
                        <img src="./image/avatar.jpg" alt="User Avatar" class="user-avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/147/147142.png'">
                    </div>
                </div>
            </header>

            <div class="content-area">
                
                <div id="tab-overview" class="tab-content" style="display: block;">
                    <div class="page-title">
                        <h1>Tổng quan</h1>
                        <p>Chào mừng bạn quay lại! Nhấn vào các biểu tượng bên dưới để xem chi tiết.</p>
                    </div>
                    
                    <div class="stats-grid clickable-grid">
                        <div class="stat-card" onclick="showDetail('detail-inventory', this)">
                            <img src="./image/store.png" class="stat-icon-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/869/869636.png'">
                            <p class="stat-label">Số lượng tồn kho</p>
                            <p class="stat-value text-orange"></p>
                        </div>
                        <div class="stat-card" onclick="showDetail('detail-orders', this)">
                            <img src="./image/dathang.png" class="stat-icon-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3500/3500833.png'">
                            <p class="stat-label">Tổng số đơn hàng</p> 
                            <p class="stat-value"></p>
                        </div>
                        <div class="stat-card" onclick="showDetail('detail-revenue', this)">
                            <img src="./image/doanhthu.png" class="stat-icon-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/2454/2454282.png'">
                            <p class="stat-label">Doanh thu tháng</p>
                            <p class="stat-value text-green"></p>
                        </div>
                        <div class="stat-card" onclick="showDetail('detail-customers', this)">
                            <img src="./image/customer.png" class="stat-icon-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3126/3126647.png'">
                            <p class="stat-label">Khách hàng</p>
                            <p class="stat-value text-green"></p>
                        </div>
                    </div>

                    <div class="details-container">
                        <div id="detail-inventory" class="detail-section" style="display: none;">
                            <div class="order-header-blue">Tình trạng kho hàng</div>
                            <div class="product-table-container">
                                <table class="product-table">
                                    <thead><tr><th class="w-1">Hình ảnh</th><th>Sản phẩm</th><th>Danh mục</th><th>Thương hiệu</th><th>Giá bán</th><th>Tồn kho</th><th class="w-1">Hành động</th></tr></thead>
                                    <tbody>
                                        <?php
                                        if ($result_sp && $result_sp->num_rows > 0) {
                                            mysqli_data_seek($result_sp, 0);
                                            while($row_sp = $result_sp->fetch_assoc()) {
                                                echo "<tr>
                                                        <td><img src='./image/{$row_sp['hinh_anh']}' alt='img' style='width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;' onerror=\"this.src='https://via.placeholder.com/50'\"></td>
                                                        <td class='font-bold'>{$row_sp['ten_sp']}</td>
                                                        <td>{$row_sp['ten_danh_muc']}</td>
                                                        <td>{$row_sp['ten_thuong_hieu']}</td>
                                                        <td>".number_format($row_sp['gia_ban'], 0, ',', '.')."đ</td>
                                                        <td>{$row_sp['so_luong_ton']}</td>
                                                        <td class='w-1'><div class='action-buttons'><a href='suasp.php?id={$row_sp['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoasp.php?id={$row_sp['id']}' onclick=\"return confirm('Xóa sản phẩm này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td>
                                                      </tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="detail-orders" class="detail-section" style="display: none;">
                            <div class="order-header-blue">Danh sách đơn hàng</div>
                            <div class="product-table-container">
                                <table class="orders-table">
                                    <thead><tr><th>ID</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th class="text-center">Trạng thái</th><th class="w-1">Hành động</th></tr></thead>
                                    <tbody>
                                        <?php
                                        if ($result_dh && $result_dh->num_rows > 0) {
                                            mysqli_data_seek($result_dh, 0);
                                            $stt = 1;
                                            while($row_dh = $result_dh->fetch_assoc()) {
                                                $tt = $row_dh['trang_thai']; $txt = 'Chờ xử lý'; if($tt==1) $txt='Đang giao'; elseif($tt==2) $txt='Hoàn thành'; elseif($tt==3) $txt='Đã hủy';
                                                echo "<tr><td class='order-id-text'>#{$stt}</td><td>{$row_dh['ho_ten']}</td><td>".date('d/m/Y H:i', strtotime($row_dh['ngay_dat']))."</td><td>".number_format($row_dh['tong_tien'], 0, ',', '.')."đ</td><td class='text-center'><span class='badge'>$txt</span></td><td class='w-1'><div class='action-buttons'><a href='suadh.php?id={$row_dh['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoadh.php?id={$row_dh['id']}' onclick=\"return confirm('Xóa đơn hàng này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td></tr>";
                                                $stt++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="detail-revenue" class="detail-section" style="display: none;">
                            <div class="order-header-blue">Phân tích doanh thu gần đây</div>
                            <div class="product-table-container">
                                <table class="data-table">
                                    <thead><tr><th>Tháng</th><th>Số đơn hàng</th><th>Doanh thu</th><th>Lợi nhuận ước tính</th><th class="text-center">Tình trạng</th></tr></thead>
                                    <tbody>
                                        <?php
                                        if ($result_bc && $result_bc->num_rows > 0) {
                                            mysqli_data_seek($result_bc, 0);
                                            while($row_bc = $result_bc->fetch_assoc()) {
                                                echo "<tr><td class='font-bold'>{$row_bc['thang']}</td><td>{$row_bc['so_don']} đơn</td><td class='font-bold'>".number_format($row_bc['doanh_thu'], 0, ',', '.')."đ</td><td class='text-green'>".number_format($row_bc['loi_nhuan'], 0, ',', '.')."đ</td><td class='text-center'><span class='badge'>{$row_bc['tinh_trang']}</span></td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="detail-customers" class="detail-section" style="display: none;">
                            <div class="order-header-blue">Quản lý khách hàng</div>
                            <div class="product-table-container">
                                <table class="data-table">
                                    <thead><tr><th>ID</th><th>Họ tên</th><th>Tài khoản</th><th>Mật khẩu</th><th>Số điện thoại</th><th class="w-1">Hành động</th></tr></thead>
                                    <tbody>
                                        <?php
                                        if ($result_kh && $result_kh->num_rows > 0) {
                                            mysqli_data_seek($result_kh, 0);
                                            $stt = 1;
                                            while($row_kh = $result_kh->fetch_assoc()) {
                                                echo "<tr><td class='col-id'>#{$stt}</td><td class='font-bold'>{$row_kh['ho_ten']}</td><td>{$row_kh['email']}</td><td>{$row_kh['mat_khau']}</td><td>{$row_kh['so_dien_thoai']}</td><td class='w-1'><div class='action-buttons'><a href='suakh.php?id={$row_kh['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoakh.php?id={$row_kh['id']}' onclick=\"return confirm('Xóa khách hàng này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td></tr>";
                                                $stt++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="white-section mt-24">
                        <div class="cat-header"><h3>Danh mục bán chạy</h3></div>
                        <div class="cat-list">
                            <div class="cat-item"><div class="cat-info"><span>PC Gaming</span><span>59%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 59%;"></div></div></div>
                            <div class="cat-item"><div class="cat-info"><span>PC Văn phòng</span><span>73%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 73%;"></div></div></div>
                            <div class="cat-item"><div class="cat-info"><span>Linh kiện</span><span>46%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 46%;"></div></div></div>
                            <div class="cat-item"><div class="cat-info"><span>Phụ kiện</span><span>60%</span></div><div class="progress-bar"><div class="progress-fill" style="width: 60%;"></div></div></div>
                            <div class="cat-item"><div class="cat-info"><span>Khác</span><span>26%</span></div><div class="progress-bar"><div class="progress-fill gray" style="width: 26%;"></div></div></div>
                        </div>
                    </div>
                </div>

                <div id="tab-products" class="tab-content" style="display: none;">
                    <div class="product-page-header">
                        <div class="page-title" style="margin-bottom: 0;"><h1>Quản lý sản phẩm</h1><p>Quản lý kho sản phẩm</p></div>
                        <button class="btn-add-product" onclick="window.location.href='themsp.php'">
                            <img src="./image/nutthem.png" alt="Thêm" style="width: 20px; height: 20px; object-fit: contain;"> Thêm sản phẩm
                        </button>
                    </div>
                    
                    <form action="admin.php" method="GET" class="search-form">
                        <input type="hidden" name="tab" value="tab-products">
                        <div class="search-wrapper">
                            <svg width="20" height="20" fill="none" stroke="#333" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search_sp" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($search_sp); ?>">
                        </div>
                        <button type="submit" class="btn-search-dark">Tìm Kiếm</button>
                    </form>
                    
                    <div class="product-table-container">
                        <div class="order-header-blue">Danh sách sản phẩm</div>
                        <table class="product-table">
                            <thead><tr><th class="w-1">Hình ảnh</th><th>Sản phẩm</th><th>Danh mục</th><th>Thương hiệu</th><th>Giá bán</th><th>Tồn kho</th><th class="w-1">Hành động</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_sp && $result_sp->num_rows > 0) {
                                    mysqli_data_seek($result_sp, 0);
                                    while($row_sp = $result_sp->fetch_assoc()) {
                                        echo "<tr>
                                                <td><img src='./image/{$row_sp['hinh_anh']}' alt='img' style='width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;' onerror=\"this.src='https://via.placeholder.com/50'\"></td>
                                                <td class='font-bold'>{$row_sp['ten_sp']}</td>
                                                <td>{$row_sp['ten_danh_muc']}</td>
                                                <td>{$row_sp['ten_thuong_hieu']}</td>
                                                <td>".number_format($row_sp['gia_ban'], 0, ',', '.')."đ</td>
                                                <td>{$row_sp['so_luong_ton']}</td>
                                                <td class='w-1'><div class='action-buttons'><a href='suasp.php?id={$row_sp['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoasp.php?id={$row_sp['id']}' onclick=\"return confirm('Xóa sản phẩm này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td>
                                              </tr>";
                                    }
                                } else { echo "<tr><td colspan='7' class='text-center'>Chưa có sản phẩm nào.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-orders" class="tab-content" style="display: none;">
                    <div class="product-page-header">
                        <div class="page-title" style="margin-bottom: 0;"><h1>Quản lý đơn hàng</h1><p>Theo dõi và xử lý các đơn hàng</p></div>
                        <button class="btn-add-product" onclick="window.location.href='themdh.php'">
                            <img src="./image/nutthem.png" alt="Thêm" style="width: 20px; height: 20px; object-fit: contain;"> Thêm đơn hàng
                        </button>
                    </div>
                    
                    <form action="admin.php" method="GET" class="search-form">
                        <input type="hidden" name="tab" value="tab-orders">
                        <div class="search-wrapper">
                            <svg width="20" height="20" fill="none" stroke="#333" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search_dh" placeholder="Tìm ID đơn hoặc tên khách hàng..." value="<?php echo htmlspecialchars($search_dh); ?>">
                        </div>
                        <button type="submit" class="btn-search-dark">Tìm Kiếm</button>
                    </form>
                    
                    <div class="product-table-container">
                        <div class="order-header-blue">Danh sách đơn hàng</div>
                        <table class="orders-table">
                            <thead><tr><th>ID</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th class="text-center">Trạng thái</th><th class="w-1">Hành động</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_dh && $result_dh->num_rows > 0) {
                                    mysqli_data_seek($result_dh, 0);
                                    $stt = 1;
                                    while($row_dh = $result_dh->fetch_assoc()) {
                                        $tt = $row_dh['trang_thai']; $txt = 'Chờ xử lý'; if($tt==1) $txt='Đang giao'; elseif($tt==2) $txt='Hoàn thành'; elseif($tt==3) $txt='Đã hủy';
                                        echo "<tr><td class='order-id-text'>#{$stt}</td><td>{$row_dh['ho_ten']}</td><td>".date('d/m/Y H:i', strtotime($row_dh['ngay_dat']))."</td><td>".number_format($row_dh['tong_tien'], 0, ',', '.')."đ</td><td class='text-center'><span class='badge'>$txt</span></td><td class='w-1'><div class='action-buttons'><a href='suadh.php?id={$row_dh['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoadh.php?id={$row_dh['id']}' onclick=\"return confirm('Xóa đơn hàng này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td></tr>";
                                        $stt++;
                                    }
                                } else { echo "<tr><td colspan='6' class='text-center'>Chưa có đơn hàng nào.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-customers" class="tab-content" style="display: none;">
                    <div class="product-page-header">
                        <div class="page-title" style="margin-bottom: 0;"><h1>Quản lý khách hàng</h1><p>Danh sách khách hàng</p></div>
                    </div>
                    
                    <form action="admin.php" method="GET" class="search-form">
                        <input type="hidden" name="tab" value="tab-customers">
                        <div class="search-wrapper">
                            <svg width="20" height="20" fill="none" stroke="#333" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search_kh" placeholder="Tìm họ tên, tài khoản hoặc SĐT..." value="<?php echo htmlspecialchars($search_kh); ?>">
                        </div>
                        <button type="submit" class="btn-search-dark">Tìm Kiếm</button>
                    </form>
                    
                    <div class="product-table-container">
                        <div class="order-header-blue">Danh sách khách hàng</div>
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Họ tên</th><th>Tài khoản</th><th>Mật khẩu</th><th>Số điện thoại</th><th class="w-1">Hành động</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_kh && $result_kh->num_rows > 0) {
                                    mysqli_data_seek($result_kh, 0);
                                    $stt = 1;
                                    while($row_kh = $result_kh->fetch_assoc()) {
                                        echo "<tr><td class='col-id'>#{$stt}</td><td class='font-bold'>{$row_kh['ho_ten']}</td><td>{$row_kh['email']}</td><td>{$row_kh['mat_khau']}</td><td>{$row_kh['so_dien_thoai']}</td><td class='w-1'><div class='action-buttons'><a href='suakh.php?id={$row_kh['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoakh.php?id={$row_kh['id']}' onclick=\"return confirm('Xóa khách hàng này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td></tr>";
                                        $stt++;
                                    }
                                } else { echo "<tr><td colspan='6' class='text-center'>Chưa có khách hàng nào.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-employees" class="tab-content" style="display: none;">
                    <div class="product-page-header">
                        <div class="page-title" style="margin-bottom: 0;"><h1>Quản lý nhân viên</h1><p>Quản lý hồ sơ nhân sự</p></div>
                        <button class="btn-add-product" onclick="window.location.href='themnv.php'">
                            <img src="./image/nutthem.png" alt="Thêm" style="width: 20px; height: 20px; object-fit: contain;"> Thêm nhân viên
                        </button>
                    </div>
                    
                    <form action="admin.php" method="GET" class="search-form">
                        <input type="hidden" name="tab" value="tab-employees">
                        <div class="search-wrapper">
                            <svg width="20" height="20" fill="none" stroke="#333" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search_nv" placeholder="Tìm tài khoản hoặc tên nhân viên..." value="<?php echo htmlspecialchars($search_nv); ?>">
                        </div>
                        <button type="submit" class="btn-search-dark">Tìm Kiếm</button>
                    </form>
                    
                    <div class="product-table-container">
                        <div class="order-header-blue">Danh sách nhân viên</div>
                        <table class="orders-table">
                            <thead><tr><th>ID</th><th class="w-1">Ảnh</th><th>Họ tên</th><th>Tài khoản</th><th>Mật khẩu</th><th>Vai trò</th><th class="w-1">Hành động</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_nv && $result_nv->num_rows > 0) {
                                    mysqli_data_seek($result_nv, 0);
                                    $stt = 1;
                                    while($row_nv = $result_nv->fetch_assoc()) {
                                        echo "<tr>
                                                <td class='col-id'>#{$stt}</td>
                                                <td><img src='./image/{$row_nv['avatar']}' class='avatar-thumb' onerror=\"this.src='https://via.placeholder.com/50'\"></td>
                                                <td class='font-bold'>{$row_nv['ho_ten']}</td>
                                                <td>{$row_nv['email']}</td>
                                                <td>{$row_nv['mat_khau']}</td>
                                                <td>{$row_nv['role_name']}</td>
                                                <td class='w-1'><div class='action-buttons'><a href='suanv.php?id={$row_nv['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoanv.php?id={$row_nv['id']}' onclick=\"return confirm('Xóa nhân viên này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td>
                                              </tr>";
                                        $stt++;
                                    }
                                } else { echo "<tr><td colspan='7' class='text-center'>Chưa có nhân viên nào.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-stats" class="tab-content" style="display: none;">
                    <div class="product-page-header">
                        <div class="page-title" style="margin-bottom: 0;"><h1>Thống kê doanh thu</h1><p>Báo cáo tài chính chi tiết 12 tháng</p></div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn-add-product" onclick="window.location.href='thembc.php'" style="background-color: #f59e0b;">
                                <img src="./image/nutthem.png" alt="Thêm" style="width: 20px; height: 20px; object-fit: contain;"> Thêm báo cáo
                            </button>
                            
                            <div class="export-dropdown">
                                <button class="btn-blue" onclick="toggleExportMenu()">
                                    <svg width="20" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Xuất báo cáo...
                                </button>
                                <div id="exportMenu" class="export-menu">
                                    <a href="xuatbc.php">📁 Xuất Excel (.csv)</a>
                                    <a href="xuatword.php">📄 Xuất Word (.doc)</a>
                                    <a href="xuatpdf.php">📕 Xuất PDF / In</a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <form action="admin.php" method="GET" class="search-form">
                        <input type="hidden" name="tab" value="tab-stats">
                        <div class="search-wrapper">
                            <svg width="20" height="20" fill="none" stroke="#333" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search_bc" placeholder="Tìm kiếm theo tháng hoặc tình trạng..." value="<?php echo htmlspecialchars($search_bc); ?>">
                        </div>
                        <button type="submit" class="btn-search-dark">Tìm Kiếm</button>
                    </form>

                    <div class="product-table-container">
                        <div class="order-header-blue">Doanh thu tháng</div>
                        <table class="data-table">
                            <thead><tr><th>Tháng</th><th>Số đơn hàng</th><th>Doanh thu</th><th>Lợi nhuận ước tính</th><th class="text-center">Tình trạng</th><th class="w-1">Hành động</th></tr></thead>
                            <tbody>
                                <?php
                                if ($result_bc && $result_bc->num_rows > 0) {
                                    mysqli_data_seek($result_bc, 0);
                                    while($row_bc = $result_bc->fetch_assoc()) {
                                        echo "<tr><td class='font-bold'>{$row_bc['thang']}</td><td>{$row_bc['so_don']} đơn</td><td class='font-bold'>".number_format($row_bc['doanh_thu'], 0, ',', '.')."đ</td><td class='text-green'>".number_format($row_bc['loi_nhuan'], 0, ',', '.')."đ</td><td class='text-center'><span class='badge'>{$row_bc['tinh_trang']}</span></td><td class='w-1'><div class='action-buttons'><a href='suabc.php?id={$row_bc['id']}'><img src='./image/butsua.png' class='action-icon' title='Sửa'></a><a href='xoabc.php?id={$row_bc['id']}' onclick=\"return confirm('Xóa báo cáo này?');\"><img src='./image/thungrac.png' class='action-icon' title='Xóa'></a></div></td></tr>";
                                    }
                                } else { echo "<tr><td colspan='6' class='text-center'>Không tìm thấy báo cáo nào.</td></tr>"; }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function toggleExportMenu() {
            var menu = document.getElementById('exportMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        window.onclick = function(event) {
            if (!event.target.closest('.export-dropdown')) {
                var menu = document.getElementById('exportMenu');
                if (menu) menu.style.display = 'none';
            }
        }

        const masterData = {
            sp: <?php mysqli_data_seek($result_sp, 0); $a=[]; while($r=$result_sp->fetch_assoc()) $a[]=$r; echo json_encode($a); ?>,
            dh: <?php mysqli_data_seek($result_dh, 0); $a=[]; while($r=$result_dh->fetch_assoc()) $a[]=$r; echo json_encode($a); ?>,
            kh: <?php mysqli_data_seek($result_kh, 0); $a=[]; while($r=$result_kh->fetch_assoc()) $a[]=$r; echo json_encode($a); ?>,
            nv: <?php mysqli_data_seek($result_nv, 0); $a=[]; while($r=$result_nv->fetch_assoc()) $a[]=$r; echo json_encode($a); ?>,
            bc: <?php mysqli_data_seek($result_bc, 0); $a=[]; while($r=$result_bc->fetch_assoc()) $a[]=$r; echo json_encode($a); ?>
        };

        const gInp = document.getElementById('globalSearchInput');
        const gBox = document.getElementById('search-results-dropdown');

        gInp.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            gBox.innerHTML = '';
            if (query === '') { gBox.style.display = 'none'; return; }

            let has = false;
            masterData.sp.forEach(i => { if (i.ten_sp.toLowerCase().includes(query)) { createItem(i.ten_sp, 'Tồn kho: ' + i.so_luong_ton, 'tag-sp', 'SP', 'tab-products'); has = true; }});
            masterData.dh.forEach(i => { if (i.id.toLowerCase().includes(query) || i.ho_ten.toLowerCase().includes(query)) { createItem('Đơn hàng #' + i.id, 'Khách: ' + i.ho_ten, 'tag-dh', 'ĐH', 'tab-orders'); has = true; }});
            masterData.kh.forEach(i => { if (i.ho_ten.toLowerCase().includes(query)) { createItem(i.ho_ten, 'Tài khoản: ' + i.email, 'tag-kh', 'KH', 'tab-customers'); has = true; }});
            masterData.nv.forEach(i => { if (i.ho_ten.toLowerCase().includes(query)) { createItem(i.ho_ten, 'Vai trò: ' + i.role_name, 'tag-nv', 'NV', 'tab-employees'); has = true; }});
            masterData.bc.forEach(i => { if (i.thang.toLowerCase().includes(query) || i.tinh_trang.toLowerCase().includes(query)) { createItem(i.thang, 'Doanh thu: ' + parseInt(i.doanh_thu).toLocaleString('vi-VN') + 'đ', 'tag-bc', 'BC', 'tab-stats'); has = true; }});

            gBox.style.display = has ? 'block' : 'none';
        });

        function createItem(title, sub, colorClass, label, tab) {
            const div = document.createElement('div');
            div.className = 'search-item';
            div.innerHTML = `<div><span class="type-tag ${colorClass}">${label}</span></div><div><div class="title">${title}</div><div class="sub">${sub}</div></div>`;
            div.onclick = () => { openTab(null, tab); gBox.style.display = 'none'; gInp.value = ''; };
            gBox.appendChild(div);
        }

        document.addEventListener('click', (e) => { if (!gInp.contains(e.target)) gBox.style.display = 'none'; });

        function openTab(evt, tabName) {
            if(evt) evt.preventDefault();
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
            tablinks = document.getElementsByClassName("menu-item");
            for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
            
            var selectedTab = document.getElementById(tabName);
            if(selectedTab) {
                selectedTab.style.display = "block";
                selectedTab.style.animation = 'fadeIn 0.5s ease-in-out';
            }
            if(evt) evt.currentTarget.classList.add("active");
            else {
                const allBtns = document.querySelectorAll('.menu-item');
                allBtns.forEach(btn => { if(btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(tabName)) btn.classList.add('active'); });
            }
        }

        function showDetail(detailId, cardElement) {
            var details = document.getElementsByClassName("detail-section");
            for (var i = 0; i < details.length; i++) { details[i].style.display = "none"; }
            var cards = document.querySelectorAll(".stat-card");
            cards.forEach(card => card.classList.remove("active-card"));
            document.getElementById(detailId).style.display = "block";
            if(cardElement) cardElement.classList.add("active-card");
        }

        window.addEventListener('DOMContentLoaded', (event) => {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if(tab) openTab(null, tab);
        });
    </script>
</body>
</html>