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
    validatePassword($body['password'], $errors);
    if (empty($errors)) {
        $email = $body['email'];
        $password = $body['password'];

        // truy vấn lấy thông tin user theo email
        $userQuery = firstDraw("SELECT id, password FROM users WHERE email='$email' AND status=1");
        // Nếu kết quả trả ra true -> có tồn tại id ứng với email truyền vào thì xử lí
        if (!empty($userQuery)) {
            // Lấy ra passwordHash trong csdl
            $passwordHash = $userQuery['password'];
            $userId = $userQuery['id'];
            if (password_verify($password, $passwordHash)) {
                // Tạo token login
                $tokenLogin = sha1(uniqid() . time());

                // insert dữ liệu vào bảng login_token
                $dataToken = [
                    'userId' => $userId,
                    'token' => $tokenLogin,
                    'createAt' =>  date('Y-m-d H:i:s')
                ];
                $insertTokenStatus = insert('login_token', $dataToken);

                if ($insertTokenStatus) {
                    // insert token thành công, lưu lại session chỉ cần lưu lại $tokenLogin
                    setSession('login_token', $tokenLogin);
                    // Chuyển hướng đến trang quản lí người dùng
                    redirect('?module=users');
                } else {
                    setFlashData('msg', 'Hệ thống đang gặp lỗi, Vui lòng đăng nhập lại sau');
                    setFlashData('msg_type', 'danger');
                }
            } else {
                setFlashData('msg', 'Mật khẩu không chính xác');
                setFlashData('msg_type', 'danger');
            }
        } else {
            setFlashData('msg', 'Email không tồn tại trong hệ thống hoặc chưa được kích hoạt');
            setFlashData('msg_type', 'danger');
        }
    } else {
        setFlashData('pre_data', $body);
        setFlashData('errors', $errors);
        setFlashData('msg', 'Vui lòng kiểm tra lại thông tin');
        setFlashData('msg_type', 'danger');
    }
    // Xử lí load lại trang để bên ngoài cũng được vì khi thực hiện redirect bên dưới sẽ dừng hết 
    redirect('?module=auth&action=login');
}
$msg = getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$preData = getFlashData('pre_data');
$data = [
    'pageTitle' => 'Đăng nhập'
];
?>

<?php layout('header_login', $data)  ?>

<body>
    <div class="form-wrapper">
        <form action="" method="post">
            <h2 class="mb-4 text-uppercase text-primary fw-bold">Đăng nhập</h2>
            <?= getMessage($msg, $msgType) ?>
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

            <div class="d-flex justify-content-end align-items-center mb-4">

                <a href="?module=auth&action=forgot" class="me-2 text-decoration-none">
                    <i class="fa-light fa-key"></i>
                    Quên mật khẩu?</a>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary btn-lg w-100 btn-block fw-bold">Đăng nhập</button>
            <p class="mt-4 text-center">
                Bạn chưa có tài khoản?
                <a href="?module=auth&action=register" class="text-decoration-none">Đăng ký ngay</a>
            </p>
        </form>
    </div>

</body>

<?php layout('footer_login')  ?>