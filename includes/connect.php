<?php
try {
    $dns = _DRIVER . ':dbname=' . _DB . ';host=' . _HOST;
    $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    $conn = new PDO($dns, _USER, _PASS, $options);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
