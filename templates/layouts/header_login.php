<?php
// tự động đăng xuất
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

<!-- Content -->