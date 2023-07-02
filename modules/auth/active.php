<?php
/*
 * Chứa chức năng kích hoạt tài khoản
 */
$data = [
    'pageTitle' => 'Kích hoạt tài khoản'
];
layout('header_login', $data);
// Lấy ra token
$token = getBody()['token'];
if (!empty($token)) {
    // truy vấn kiểm tra token với database
    $tokenQuery = firstDraw("SELECT id, fullname, email FROM users WHERE activeToken='$token'");
    if (!empty($tokenQuery)) {
        $userId = $tokenQuery['id'];

        // Tiến hành thay đổi trạng thái status
        $dataUpdate = [
            'status' => 1,
            'activeToken' => null
        ];
        $updateStatus = update('users', $dataUpdate, "id=$userId");
        // Đẩy thông báo sang login
        if ($updateStatus) {
            setFlashData('msg', 'Kích hoạt tài khoản thành công, Vui lòng đăng nhập');
            setFlashData('msg_type', 'success');
            // Tạo link login
            $loginLink = _WEB_HOST_ROOT . '?module=auth&action=login';
            // Gửi mail thông báo nếu kích hoạt thành công
            $subject = 'Kích hoạt tài khoản thành công';
            $content = "Chúc mừng {$tokenQuery['fullname']} đã kích hoạt thành công! <br>";
            $content .= "Bạn có thể đăng nhập vào link sau {$loginLink} <br>";
            $content .= "Cảm ơn bạn";
            sendMail($tokenQuery['email'], $subject, $content);
        } else {
            setFlashData('msg', 'Kích hoạt không thành công, Vui lòng liên hệ quản trị viên');
            setFlashData('msg_type', 'danger');
        }
        // điều hướng sang trang login
        redirect('?module=auth&action=login');
    } else {
        getMessage('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
    }
} else {
    getMessage('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
}
?>
<?php layout('footer_login') ?>