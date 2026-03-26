// File: jvc-dn.js

document.addEventListener('DOMContentLoaded', function() {
    // Tìm các nút bấm trên giao diện
    const btnLogin = document.getElementById('btn-login');
    const btnLogout = document.getElementById('btn-logout');

    // Nếu trang hiện tại có chứa Header này thì mới chạy code
    if (btnLogin && btnLogout) {
        
        // Kiểm tra xem trình duyệt đã lưu trạng thái đăng nhập chưa
        let isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';

        if (isLoggedIn) {
            // NẾU ĐÃ ĐĂNG NHẬP:
            btnLogout.classList.remove('hidden'); // Hiện nút đăng xuất
            btnLogin.href = "thongtinkh.html";      // Đổi link sang trang Sổ địa chỉ
            btnLogin.title = "Quản lý tài khoản";
        } else {
            // NẾU CHƯA ĐĂNG NHẬP:
            btnLogout.classList.add('hidden');    // Ẩn nút đăng xuất
            btnLogin.href = "dangnhap.html";      // Giữ link là trang Đăng nhập
            btnLogin.title = "Tài khoản";
        }

        // Bắt sự kiện khi bấm vào nút Đăng xuất
        btnLogout.addEventListener('click', function(e) {
            e.preventDefault(); 
            alert('Bạn đã đăng xuất thành công!');
            
            // Xóa trạng thái đăng nhập
            localStorage.setItem('isLoggedIn', 'false'); 
            
            // Nếu đang ở trang thongtin.html mà đăng xuất thì đá văng về trang chủ
            if (window.location.pathname.includes('main.html')) {
                window.location.href = 'main.html';
            } else {
                window.location.reload(); // Còn ở trang khác thì chỉ cần tải lại trang
            }
        });
    }
});