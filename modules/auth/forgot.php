<?php
/*
 * Chứa chức năng quên mật khẩu
 * Tạo forgot token
 * Gửi email
 * Xác thực token, hiện form reset
 * Xử lí submit reset password
 */
?>
<?php

// Xử lí trạng thái đăng nhập
// Nếu đang login thì chuyển hướng về quản lí người dùng
if (isLogin()) {
    redirect('?module=users');
}
if (isPost()) {
    // lấy ra dữ liệu form
    $body = getBody();
    $errors = [];
    validateEmail($body['email'], $errors, true);
    if (empty($errors)) {
        // Xử lí
        $email = $body['email'];
        // truy vấn
        $queryUser = firstDraw("SELECT id, fullname FROM users WHERE email='$email'");
        if (!empty($queryUser)) {
            $userId = $queryUser['id'];
            $fullname = $queryUser['fullname'];
            // tạo forgot token
            $forgotToken = sha1(uniqid() . time());
            $dataUpdate = [
                'forgotToken' => $forgotToken
            ];
            $updateStatus = update('users', $dataUpdate, "id='$userId'");
            if ($updateStatus) {
                // Tạo link khôi phục
                $linkReset = _WEB_HOST_ROOT . '?module=auth&action=reset&token=' . $forgotToken;

                // Gửi mail
                $subject = 'Yêu cầu khôi phục mật khẩu';
                $content = "Chào bạn $fullname <br>";
                $content .= "Chúng tôi nhận được yêu cầu khôi phục mật khẩu từ bạn vui lòng click vào link sau để khôi phục <br>";
                $content .= $linkReset . '<br>';
                $content .= "Cảm ơn bạn!";

                // Tiến hành gửi mail
                $sendStatus = sendMail($email, $subject, $content);
                if ($sendStatus) {
                    setFlashData('msg', 'Vui lòng kiểm tra email để tiến hành đặt lại mật khẩu');
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
            setFlashData('msg', 'Email không tồn tại trong hệ thống');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('pre_data', $body);
        setFlashData('errors', $errors);
        setFlashData('msg', 'Vui lòng kiểm tra lại thông tin');
        setFlashData('msg_type', 'danger');
    }
    // Xử lí load lại trang để bên ngoài cũng được vì khi thực hiện redirect bên dưới sẽ dừng hết 
    redirect('?module=auth&action=forgot');
}
$msg = getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$preData = getFlashData('pre_data');
$data = [
    'pageTitle' => 'Quên mật khẩu'
];
?>

<?php layout('header_login', $data) ?>

<body>
    <div class="form-wrapper">
        <form action="" method="post">
            <h2 class="mb-4 text-uppercase text-primary fw-bold">Quên mật khẩu</h2>
            <?= getMessage($msg, $msgType) ?>
            <!-- Email input -->
            <div class="form-outline mb-3">
                <label class="form-label fw-bold" for="form1Example13">Email</label>
                <input value="<?= $preData['email'] ?? ''  ?>" name="email" type="email" id="form1Example13" class="<?= errorBorder($errors, 'email') ?>  form-control form-control-lg fs-6" placeholder="Địa chỉ email..." />
                <span class="error"><?= $errors['email'] ?? '' ?></span>
            </div>
            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-lg w-100 btn-block fw-bold">Gửi yêu cầu</button>
            <div class="d-flex flex-column justify-content-between align-items-center mb-4 mt-3 gap-3">
                <a href="?module=auth&action=login" class="text-decoration-none">Đăng nhập</a>
                <span>
                    Bạn chưa có tài khoản?
                    <a href="?module=auth&action=register" class="text-decoration-none">Đăng ký</a>
                </span>
            </div>
        </form>
    </div>

</body>

<?php layout('footer_login') ?>