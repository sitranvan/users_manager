<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function layout($layoutName = 'header', $data = []): void
{
    if (file_exists(_WEB_PATH_TEMPLATE . '/layouts/' . $layoutName . '.php')) {
        require_once _WEB_PATH_TEMPLATE . '/layouts/' . $layoutName . '.php';
    }
}

function sendMail($to, $subject, $content)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sitran.dev@gmail.com';
        $mail->Password   = 'uwbmopdmozkwtdqg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('sitran.dev@gmail.com', 'Trần Sĩ');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        return $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


// Kiểm tra phương thức POST
function isPost(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

// Kiểm tra phương thức GET
function isGet(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

// Lấy phương thức POST, GET
function getBody()
{
    $bodyArr = [];
    if (isGet()) {
        /**
         * Đọc key của mảng $_GET, tiến hành lọc để xử lí chuỗi tránh vấn đề liên quan bảo mật khi người dùng cố ý nhập vào một chuỗi không đúng định dạng ví dụ <script></script>
         */
        if (!empty($_GET)) {

            foreach ($_GET as $key => $value) {
                // Loại bỏ các thẻ html => id/n=1, id<span></span>...
                $key = strip_tags($key);
                // Xử lí nếu param là mảng
                if (is_array($value)) {
                    $bodyArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    // FILTER_SANITIZE_SPECIAL_CHARS => loại bỏ các kí tự đặt biệt
                    $bodyArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    if (isPost()) {
        if (!empty($_POST)) {

            foreach ($_POST as $key => $value) {
                // Loại bỏ các thẻ html => id/n=1, id<span></span>...
                $key = strip_tags($key);
                // Xử lí nếu param là mảng
                if (is_array($value)) {
                    $bodyArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    // FILTER_SANITIZE_SPECIAL_CHARS => loại bỏ các kí tự đặt biệt
                    $bodyArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    return $bodyArr;
}

// Hàm tạo thông báo
function getMessage($msg, $msgType)
{
    if (!empty($msg)) {
        echo ' <div class="alert alert-' . $msgType . '">';
        echo '<p class="text-center m-0">' . $msg . '</p>';
        echo '</div>';
    }
}

// Hàm chuyển hướng
function redirect($path = 'index.php')
{
    header('Location: ' . $path);
    exit();
}

// Kiểm tra đăng nhập
function isLogin()
{
    $checkLogin = false;
    if (getSession('login_token')) {
        $loginToken = getSession('login_token');
        // truy vấn kiểm tra xem $loginToken có tồn tại trong bảng login_token
        $queryToken = firstDraw("SELECT id, userId FROM login_token WHERE token='$loginToken'");
        if (!empty($queryToken)) {
            // $checkLogin = true;
            $checkLogin = $queryToken;
        } else {
            // Nếu không tồn tại xóa session đi
            removeSession('login_token');
        }
    }
    return $checkLogin;
}

// tự động đăng xuất (xóa token login) 15p
function autoLogout()
{
    $allUsers = getDraw("SELECT * FROM users WHERE status=1");
    if (!empty($allUsers)) {
        foreach ($allUsers as $user) {
            // lấy thời gian hoạt động cuối cùng so sánh với thời gian hiện tại
            $now = date('Y-m-d H:i:s');
            $before = $user['lastActivity'];
            $diff = strtotime($now) - strtotime($before);
            $diff = floor($diff / 60);
            if ($diff >= 1) {
                deleteRecord('login_token', "userId={$user['id']}");
            }
        }
    }
}

// lưu lại thời gian cuối cùng hoạt động
function saveActivity()
{
    $userId = isLogin()['userId'];
    update('users', [
        'lastActivity' => date('Y-m-d H:i:s')
    ], "id=$userId");
}
