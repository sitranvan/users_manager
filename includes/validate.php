<?php
// VALIDATE

// Kiểm tra email
function isEmail($email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Kiểm tra số nguyên
function isNumberInt($number, $range = []): bool
{
    if (!empty($range)) {
        $options =  ['options' => $range];
        return  filter_var($number, FILTER_VALIDATE_INT, $options);
    } else {
        return  filter_var($number, FILTER_VALIDATE_INT);
    }
}

// Kiểm tra số thực
function isNumberFloat($number, $range = []): bool
{
    if (!empty($range)) {
        $options =  ['options' => $range];
        return  filter_var($number, FILTER_VALIDATE_FLOAT, $options);
    } else {
        return  filter_var($number, FILTER_VALIDATE_FLOAT);
    }
}

// Hiển thị border khi validate lỗi
function errorBorder($errors, $fieldName)
{
    if (!empty($errors[$fieldName])) {
        return 'border-danger';
    }
}

// validate tên [bắt buộc nhập, >=5 kí tự]
function validateFullname($fullname = '', &$errors = [])
{
    if (empty(trim($fullname))) {
        $errors['fullname'] = 'Họ tên bắt buộc nhập';
    } else {
        if (strlen(trim($fullname)) < 5) {
            $errors['fullname'] = 'Họ tên phải có ít nhất 5 kí tự';
        }
    }
}
// validate phone [bắt buộc nhập, đúng định dạng]
function validatePhone($phone, &$errors)
{
    if (empty(trim($phone))) {
        $errors['phone'] = 'Số điện thoại bắt buộc nhập';
    } else {
        if (!preg_match('/^(0[2-9]|84[2-9])([0-9]{8})$/', trim($phone))) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        } else {
            $phone = trim($phone);
        }
    }
}

// validate email [bắt buộc nhập, đúng định dạng, duy nhất]
function validateEmail($email, &$errors, $login = false, $userId = '')
{
    if (empty(trim($email))) {
        $errors['email'] = 'Email bắt buộc nhập';
    } else {
        if (!isEmail(trim($email))) {
            $errors['email'] = 'Email không hợp lệ';
        } else {
            // Kiểm tra email có tồn tại trong database
            // Xử lý email kiểm tra duy nhất trừ đi email của id đang sửa để không bị trùng (AND id<>$userId")
            $email = trim($email);
            if (!empty($userId)) {
                $sql = "SELECT id FROM users WHERE email='$email' AND id<>$userId ";
            } else {
                $sql = "SELECT id FROM users WHERE email='$email' ";
            }
            if (getRows($sql) > 0) {
                if (!$login) {
                    $errors['email'] = 'Email đã tồn tại';
                }
            } else {
                $email = trim($email);
            }
        }
    }
}


// validate mật khẩu [bắt buộc nhập, >=8 kí tự]
function validatePassword($password, &$errors)
{
    if (empty(trim($password))) {
        $errors['password'] = 'Mật khẩu bắt buộc nhập';
    } else {
        if (strlen(trim($password)) < 8) {
            $errors['password'] = 'Mật khẩu phải >= 8 kí tự';
        } else {
            $password = trim($password);
        }
    }
}

// validate nhập lại mật khẩu [bắt buộc nhập, giống trường mật khẩu]
function validateConfirmPassword($password, $confirmPassword, &$errors)
{
    if (empty(trim($confirmPassword))) {
        $errors['confirm_password'] = 'Xác nhận mật khẩu bắt buộc nhập';
    } else {
        if (trim($password) != trim($confirmPassword)) {
            $errors['confirm_password'] = 'Mật khẩu không trùng khớp';
        }
    }
}
