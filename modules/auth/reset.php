<?php
/*
 * Chứa chức năng đặt lại mật khẩu
 */
$data = [
    'pageTitle' => 'Đặt lại mật khẩu'
];

layout('header_login', $data);
// Lấy ra token getBody của phương thức GET (khi được chuyển hướng đến trang này thông qua email)
$token = getBody()['token'];

if (!empty($token)) {
    $tokenQuery = firstDraw("SELECT id, fullname, email FROM users WHERE forgotToken='$token'");
    if (!empty($tokenQuery)) {
        $userId = $tokenQuery['id'];
        $email = $tokenQuery['email'];
        // xử lí trường hợp khi submit mặc dù liên kết vẫn còn nhưng báo liên kết không tồn tại
        // vì khi lấy ra là dùng GET nhưng khi submit là dùng POST
        if (isPost()) {
            // này là getBody của phương thức POST
            $body = getBody();
            $errors = [];
            // validate
            validatePassword($body['password'], $errors);
            validateConfirmPassword($body['password'], $body['confirm_password'], $errors);
            if (empty($errors)) {
                // xử lí update mật khẩu
                $passwordHash = password_hash($body['password'], PASSWORD_DEFAULT);
                $dataUpdate = [
                    'password' => $passwordHash,
                    'forgotToken' => null,
                    'updateAt' => date('Y-m-d H:i:s')
                ];
                $updateStatus = update('users', $dataUpdate, "id='$userId'");
                if ($updateStatus) {
                    setFlashData('msg', 'Thay đổi mật khẩu thành công');
                    setFlashData('msg_type', 'success');
                    // gửi mail thông báo
                    $subject = 'Bạn vừa đổi mật khẩu';
                    $content = 'Chúc mừng bạn đã đổi mật khẩu thành công. Vui lòng sử dụng mật khẩu mới để đăng nhập';
                    sendMail($email, $subject, $content);
                    redirect('?module=auth&action=login');
                } else {
                    setFlashData('msg', 'Hệ thống đang gặp sự cố! Vui lòng thử lại sau');
                    setFlashData('msg_type', 'danger');
                    redirect('?module=auth&action=reset&token=' . $token);
                }
            } else {
                setFlashData('errors', $errors);
                setFlashData('msg', 'Vui lòng kiểm tra lại thông tin');
                setFlashData('msg_type', 'danger');
                redirect('?module=auth&action=reset&token=' . $token);
            }
        }
        $msg = getFlashData('msg');
        $msgType = getFlashData('msg_type');
        $errors = getFlashData('errors');

?>
        <!-- Tạo form -->
        <div class="form-wrapper">
            <form action="" method="post">
                <h2 class="mb-4 text-uppercase text-primary fw-bold">Đặt lại mật khẩu</h2>
                <?= getMessage($msg, $msgType) ?>
                <!-- Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label fw-bold" for="form1Example23">Mật khẩu mới</label>
                    <input name="password" type="password" id="form1Example23" class="<?= errorBorder($errors, 'password') ?> form-control fs-6 form-control-lg" placeholder="Mật khẩu mới..." />
                    <span class="error"><?= $errors['password'] ?? '' ?></span>
                </div>
                <!-- Confirm Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label fw-bold" for="form1Example23">Xác nhận mật khẩu mới</label>
                    <input name="confirm_password" type="password" id="form1Example23" class="<?= !empty($errors['confirm_password']) ? 'border-danger' : '' ?>  form-control fs-6 form-control-lg" placeholder="Xác nhận mật khẩu mới..." />
                    <span class="error"><?= $errors['confirm_password'] ?? '' ?></span>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-lg w-100 btn-block fw-bold">Xác nhận</button>
                <div class="d-flex flex-column justify-content-between align-items-center mb-4 mt-3 gap-3">
                    <a href="?module=auth&action=login" class="text-decoration-none">Đăng nhập</a>
                    <span>
                        Bạn chưa có tài khoản?
                        <a href="?module=auth&action=register" class="text-decoration-none">Đăng ký</a>
                        <!-- Xử dụng input ẩn để lưu lại token -->
                        <input type="hidden" value="<?= $token ?>" name="token">
                    </span>
                </div>
            </form>
        </div>
<?php
    } else {
        getMessage('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
    }
} else {
    getMessage('Liên kết không tồn tại hoặc đã hết hạn', 'danger');
}
?>
<?php layout('footer_login')  ?>