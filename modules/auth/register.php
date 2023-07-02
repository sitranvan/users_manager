<?php
if (isPost()) {
    // lấy tất cả dữ liệu trong form
    $body = getBody();
    // khởi tạo mảng chứa tất cả lỗi
    $errors = [];
    // validate
    validateFullname($body['fullname'], $errors);
    validatePhone($body['phone'], $errors);
    validateEmail($body['email'], $errors);
    validatePassword($body['password'], $errors);
    validateConfirmPassword($body['password'], $body['confirm_password'], $errors);

    // Xử lí đăng ký
    if (empty($errors)) {
        // Validate thành công
        $activeToken  = sha1(uniqid() . time());
        $dataInsert = [
            'fullname' => $body['fullname'],
            'phone' => $body['phone'],
            'email' => $body['email'],
            'password' => password_hash($body['password'], PASSWORD_DEFAULT),
            'activeToken' => $activeToken,
            'createAt' => date('Y-m-d H:i:s')
        ];
        // insert thành công
        $insertStatus = insert('users', $dataInsert);
        if ($insertStatus) {
            // Tạo link kích hoạt
            $linkActive = _WEB_HOST_ROOT . '?module=auth&action=active&token=' . $activeToken;
            // Thiết lập giủi mail
            $subject = $body['fullname'] . ' vui lòng kích hoạt tài khoản';
            $content = 'Chào bạn ' . $body['fullname'] . '<br>';
            $content .= 'Vui lòng click dưới để kích hoạt tài khoản' . '<br>';
            $content .= $linkActive . '<br>';
            $content .= 'Xin cảm ơn!';
            // Tiến hành giửi mail
            $sendStatus = sendMail($body['email'], $subject, $content);
            if ($sendStatus) {
                setFlashData('msg', 'Đăng ký tài khoản thành công! Vui lòng kiểm tra email');
                setFlashData('msg_type', 'success');
            } else {
                setFlashData('msg', 'Hệ thống đang gặp sự cố! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
        }

        redirect('?module=auth&action=register');
    } else {
        // Xử lí lỗi validate
        setFlashData('msg', 'Vui lòng kiểm tra lại dữ liệu');
        setFlashData('msg_type', 'danger');
        // Vì load trang sẽ yêu cầu lại nên các lỗi trước đó sẽ biến mất nên cần lưu lại
        setFlashData('errors', $errors);
        // do redirect load lại trang nên dữ liệu trước đó không được giữ lại nên cần phải lưu giá trị lại rồi xóa bỏ
        setFlashData('pre_data', $body);
        // load lại trang confirm, tránh trường hợp khi load lại trang hiển thị confirm
        redirect('?module=auth&action=register');
    }
}
// vì là flash nên khi gọi ra khi submit là xóa luôn nên cần biến để lưu lại
$msg = getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$preData = getFlashData('pre_data');
$data = [
    'pageTitle' => 'Đăng ký'
];
?>

<?php layout('header_login')  ?>

<body>
    <div class="form-wrapper">
        <form action="" method="POST">
            <h2 class="mb-4 text-uppercase text-primary fw-bold">Đăng ký</h2>
            <?= getMessage($msg, $msgType) ?>
            <!-- FullName input -->
            <div class="form-outline mb-3 ">
                <label class="form-label fw-bold" for="form1Example13">Họ tên</label>
                <input value="<?= $preData['fullname'] ?? '' ?>" name="fullname" type="text" id="form1Example13" class="<?= errorBorder($errors, 'fullname') ?> form-control form-control-lg fs-6" placeholder="Tên đầy đủ..." />
                <span class="error"><?= $errors['fullname'] ?? '' ?></span>
            </div>
            <!-- Phone input -->
            <div class="form-outline mb-3">
                <label class="form-label fw-bold" for="form1Example13">Số điện thoại</label>
                <input value="<?= $preData['phone'] ?? ''  ?>" name="phone" type="text" id="form1Example13" class="<?= errorBorder($errors, 'phone') ?>  form-control form-control-lg fs-6" placeholder="Số điện thoại..." />
                <span class="error"><?= $errors['phone'] ?? '' ?></span>
            </div>

            <!-- Email input -->
            <div class="form-outline mb-3">
                <label class="form-label fw-bold" for="form1Example13">Email</label>
                <input value="<?= $preData['email'] ?? ''  ?>" name="email" type="email" id="form1Example13" class="<?= errorBorder($errors, 'email') ?>  form-control form-control-lg fs-6" placeholder="Địa chỉ email..." />
                <span class="error"><?= $errors['email'] ?? '' ?></span>
            </div>

            <!-- Password input -->
            <div class="form-outline mb-4">
                <label class="form-label fw-bold" for="form1Example23">Mật khẩu</label>
                <input name="password" type="password" id="form1Example23" class="<?= errorBorder($errors, 'password') ?> form-control fs-6 form-control-lg" placeholder="Mật khẩu..." />
                <span class="error"><?= $errors['password'] ?? '' ?></span>
            </div>
            <!-- Confirm Password input -->
            <div class="form-outline mb-4">
                <label class="form-label fw-bold" for="form1Example23">Xác nhận mật khẩu</label>
                <input name="confirm_password" type="password" id="form1Example23" class="<?= !empty($errors['confirm_password']) ? 'border-danger' : '' ?>  form-control fs-6 form-control-lg" placeholder="Xác nhận mật khẩu..." />
                <span class="error"><?= $errors['confirm_password'] ?? '' ?></span>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-lg w-100 btn-block fw-bold">Đăng ký</button>
            <p class="mt-4 text-center">
                Bạn đã có tài khoản?
                <a href="?module=auth&action=login" class="text-decoration-none">Đăng nhập ngay</a>
            </p>
        </form>
    </div>
</body>

<?php layout('footer_login') ?>