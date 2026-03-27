// Hàm mở Modal xác nhận đăng xuất
function openLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

// Hàm đóng Modal
function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

// Hàm xử lý logic đăng xuất (chuyển hướng trang)
function handleLogout() {
    // Bạn có thể thay đổi đường dẫn redirect tùy theo file login của bạn
    window.location.href = 'login.html'; 
}

// Đóng modal khi nhấn ra ngoài vùng nội dung
window.onclick = function(event) {
    let logoutModal = document.getElementById('logoutModal');
    let orderModal = document.getElementById('orderModal'); // Nếu có modal khác
    
    if (event.target == logoutModal) {
        logoutModal.style.display = "none";
    }
    // Giữ lại logic cho các modal cũ nếu cần
    if (event.target == orderModal) {
        orderModal.style.display = "none";
    }
}