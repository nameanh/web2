<?php
require_once __DIR__ . '/config.php';

function get_db() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset(DB_CHARSET);
        if ($conn->connect_error) {
            die('Kết nối DB thất bại: ' . $conn->connect_error);
        }
    }
    return $conn;
}
?>