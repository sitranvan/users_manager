<?php
/*
 * Xóa người dùng
 */

$body = getBody();
if (!empty($body['id'])) {
    $userId = $body['id'];
    // kiểm tra người dùng đã tồn tại chưa
    $userDetailRows = getRows("SELECT id FROM users WHERE id=$userId");
    if ($userDetailRows > 0) {
        // Thực hiện xóa vì ràng buộc khóa ngoại nên xóa bảng login_token trước
        // 1. Xóa login token
        $deleteToken = deleteRecord('login_token', "userId=$userId");
        if ($deleteToken) {
            // 2. Xóa users
            $deleteUser = deleteRecord('users', "id=$userId");
            if ($deleteUser) {
                setFlashData('msg', 'Xóa người dùng thành công');
                setFlashData('msg_type', 'success');
            } else {
                setFlashData('msg', 'Lỗi hệ thống vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
        } else {
            setFlashData('msg', 'Lỗi hệ thống vui lòng thử lại sau');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('msg', 'Không tồn tại');
        setFlashData('msg_type', 'danger');
    }
} else {
    setFlashData('msg', 'Không tồn tại');
    setFlashData('msg_type', 'danger');
}

redirect('?module=users');
