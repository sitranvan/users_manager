<?php
// Không phải login thì chuyển hướng về trang login để ở header vì dùng chung header
if (!isLogin()) {
    redirect('?module=auth&action=login');
}
saveActivity(); // lưu lại thời gian hoạt động cuối cùng
autoLogout();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?= _WEB_HOST_TEMPLATE ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= _WEB_HOST_TEMPLATE ?>/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?= _WEB_HOST_TEMPLATE ?>/css/myStyle.css">
    <script src="<?= _WEB_HOST_TEMPLATE ?>/js/bootstrap.min.js"></script>
    <script src="<?= _WEB_HOST_TEMPLATE ?>/js/custom.js"></script>
    <title><?= !empty($data['pageTitle']) ? $data['pageTitle'] : 'Quản lý người dùng'; ?></title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-primary">
        <div class="container">
            <a class="navbar-brand text-white fw-bold text-uppercase" href="#">
                <i class="fa-sharp fa-regular fa-users"></i>
                Users Manager
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="#">Home</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold href=" #" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-shield mb-1"></i>
                            Tran Van Si
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Hồ sơ</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="?module=auth&action=logout">
                                    Đăng xuất
                                    <i class="fa-light fa-right-from-bracket ms-2"></i>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>