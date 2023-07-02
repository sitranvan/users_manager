<?php
/*
 * Sửa người dùng
 */
$data = [
    'pageTitle' => 'Sửa người dùng'
];
// Lấy dữ liệu cũ của người dùng
$body = getBody();
if (!empty($body['id'])) {
    $userId = $body['id'];

    $userDetail = firstDraw("SELECT * FROM users WHERE id='$userId'");
    if (!empty($userDetail)) {
        // Gán flashData để gửi yêu cầu không bị mất dữ liệu
        setFlashData('user_detail', $userDetail);
    } else {
        // không tồn tại quay về danh sách
        redirect('?module=users');
    }
} else {
    redirect('?module=users');
}

// xử lí sửa người dùng
if (isPost()) {
    // lấy tất cả dữ liệu trong form
    $body = getBody();
    // khởi tạo mảng chứa tất cả lỗi
    $errors = [];
    // validate
    validateFullname($body['fullname'], $errors);
    validatePhone($body['phone'], $errors);
    validateEmail($body['email'], $errors, $userId); // trường hợp xử lí sửa nhập lại tránh trùng với email trước đó


    // Xử lí đăng ký
    if (empty($errors)) {
        // Validate thành công
        $dataUpdate = [
            'fullname' => $body['fullname'],
            'phone' => $body['phone'],
            'email' => $body['email'],
            'status' => $body['status'],
            'updateAt' => date('Y-m-d H:i:s')
        ];
        $condition = "id=$userId";
        $updateStatus = update('users', $dataUpdate, $condition);

        // update thành công
        if ($updateStatus) {
            setFlashData('msg', 'Cập nhật người dùng thành công');
            setFlashData('msg_type', 'success');
            redirect('?module=users'); // chuyển hướng sang trang danh sách
        } else {
            setFlashData('msg', 'Hệ thống đang gặp sự cố! Vui lòng thử lại sau');
            setFlashData('msg_type', 'danger');
        }

        // redirect('?module=users&action=edit');
    } else {
        // Xử lí lỗi validate
        setFlashData('msg', 'Vui lòng kiểm tra lại dữ liệu');
        setFlashData('msg_type', 'danger');
        // Vì load trang sẽ yêu cầu lại nên các lỗi trước đó sẽ biến mất nên cần lưu lại
        setFlashData('errors', $errors);
        // do redirect load lại trang nên dữ liệu trước đó không được giữ lại nên cần phải lưu giá trị lại rồi xóa bỏ
        setFlashData('pre_data', $body);
        // load lại trang confirm, tránh trường hợp khi load lại trang hiển thị confirm
        // redirect('?module=users&action=edit');
    }
    redirect('?module=users&action=edit&id=' . $userId);
}
// vì là flash nên khi gọi ra khi submit là xóa luôn nên cần biến để lưu lại
$msg = getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$preData = getFlashData('pre_data');
$userDetail = getFlashData('user_detail');
if (!empty($userDetail)) {
    // tận dụng biến preData để không phải viết lại
    $preData = $userDetail;
}

?>

<?php layout('header', $data) ?>
<div class="container">
    <h1 class="fs-3 mt-5 mb-5 text-center fw-bold text-uppercase">
        <i class="fa-sharp fa-solid fa-list-check"></i>
        <?= $data['pageTitle'] ?? '' ?>
    </h1>
    <div class="w-100 d-flex justify-content-center">
        <div class="w-50">
            <?= getMessage($msg, $msgType) ?>
        </div>
    </div>

    <div class="w-100 d-flex justify-content-center">
        <form class="w-50" method="POST" action="">
            <div class="form-group">
                <label class="form-label fw-bold" for="form1Example13">Họ tên</label>
                <input value="<?= $preData['fullname'] ?? '' ?>" name="fullname" type="text" id="form1Example13" class="<?= errorBorder($errors, 'fullname') ?> form-control form-control-lg fs-6" placeholder="Tên đầy đủ..." />
                <span class="error"><?= $errors['fullname'] ?? '' ?></span>
            </div>
            <div class="form-group">
                <label class="form-label fw-bold" for="form1Example13">Số điện thoại</label>
                <input value="<?= $preData['phone'] ?? ''  ?>" name="phone" type="text" id="form1Example13" class="<?= errorBorder($errors, 'phone') ?>  form-control form-control-lg fs-6" placeholder="Số điện thoại..." />
                <span class="error"><?= $errors['phone'] ?? '' ?></span>
            </div>
            <div class="form-group">
                <label class="form-label fw-bold" for="form1Example13">Email</label>
                <input value="<?= $preData['email'] ?? ''  ?>" name="email" type="email" id="form1Example13" class="<?= errorBorder($errors, 'email') ?>  form-control form-control-lg fs-6" placeholder="Địa chỉ email..." />
                <span class="error"><?= $errors['email'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label class="form-label fw-bold" for="form1Example23">Trạng thái</label>
                <select name="status" class="form-control py-2">
                    <option <?= (!empty($preData['status']) && $preData['status'] == 0) ? 'selected' : false ?> value="0">Chưa kích hoạt</option>
                    <option <?= (!empty($preData['status']) && $preData['status'] == 1) ? 'selected' : false ?> value="1">Kích hoạt</option>
                </select>
            </div>
            <div class="d-flex align-items-center mt-4 gap-2">
                <button type="submit" class="btn btn-primary">Xác nhận
                    <i class="fa-regular fa-check"></i>
                </button>
                <a class="btn btn-success" href="?module=users">Quay lại danh sách
                    <i class="fa-thin fa-backward"></i>
                </a>
                <!-- Vì id lấy từ biến GET nên khi POST thì id sẽ mất nên cần input ẩn để lưu lại, nếu không có bước này sẽ không thể update được -->
                <input type="hidden" name="id" value="<?= $userId ?>">
            </div>
        </form>
    </div>
</div>


</div>

</div>

<?php layout('footer') ?>