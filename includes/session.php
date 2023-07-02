<?php
/*
 * Chứa các hàm liên quan đến thao tác session
 */

// Hàm gán session
function setSession($key, $value): bool
{
    if (!empty(session_id())) {
        $_SESSION[$key] = $value;
        return true;
    }
    return false;
}

// Hàm đọc session
function getSession($key = ''): mixed
{
    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    return false;
}

// Hàm xóa session
function removeSession($key = ''): mixed
{
    if (empty($key)) {
        session_destroy();
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
    }
    return false;
}

// Hàm gán flash data
function setFlashData($key, $value)
{
    $key = 'flash_' . $key;
    return setSession($key, $value);
}

// Hàm đọc flash data
function getFlashData($key)
{
    $key = 'flash_' . $key;
    $data = getSession($key);
    removeSession($key);
    return $data;
}
