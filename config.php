<?php
/*
 * Chứa các hằng số cấu hình
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');

const _MODULE_DEFAULT = 'users'; // module mặc định
const _ACTION_DEFAULT = 'list'; // action mặc định
const _INCODE = true;

// Thiết lập host
define("_WEB_HOST_ROOT", 'http://' . $_SERVER['HTTP_HOST'] . '/php-unicode/module05/users_manager');

const _WEB_HOST_TEMPLATE = _WEB_HOST_ROOT . '/templates';

// Thiết lập path
const _WEB_PATH_ROOT = __DIR__;
const _WEB_PATH_TEMPLATE = _WEB_PATH_ROOT . '/templates';

// Thiết lập connect
const _DRIVER = 'mysql';
const _HOST = 'localhost';
const _USER = 'root';
const _PASS = '';
const _DB = 'php_user';
