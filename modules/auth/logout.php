<?php
/*
 * Chứa chức năng đăng xuất
 */
if (isLogin()) {
    // lấy ra login token
    $token = getSession('login_token');
    deleteRecord('login_token', "token='$token'");
    redirect('?module=auth&action=login');
}
