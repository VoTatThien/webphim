<?php

/**
 * Mở kết nối đến CSDL sử dụng PDO
 */

function pdo_get_connection(){
    static $conn = null;
    static $working_port = null;
    
    // Nếu đã có kết nối đang mở trong phiên request, tái sử dụng ngay (Singleton)
    if ($conn !== null) {
        try {
            // Kiểm tra kết nối còn sống không
            $conn->query("SELECT 1");
            return $conn;
        } catch (Exception $e) {
            $conn = null; // Kết nối bị đứt, tạo lại bên dưới
        }
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    $server_addr = $_SERVER['SERVER_ADDR'] ?? '';
    
    $is_local = false;
    if (DIRECTORY_SEPARATOR === '\\' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $is_local = true;
    } elseif (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '192.168.') !== false ||
        strpos($host, '10.') === 0 ||
        strpos($host, '172.16.') !== false ||
        strpos($host, '172.17.') !== false ||
        strpos($host, '172.18.') !== false ||
        strpos($host, '172.19.') !== false ||
        strpos($host, '172.20.') !== false ||
        strpos($host, '172.21.') !== false ||
        strpos($host, '172.22.') !== false ||
        strpos($host, '172.23.') !== false ||
        strpos($host, '172.24.') !== false ||
        strpos($host, '172.25.') !== false ||
        strpos($host, '172.26.') !== false ||
        strpos($host, '172.27.') !== false ||
        strpos($host, '172.28.') !== false ||
        strpos($host, '172.29.') !== false ||
        strpos($host, '172.30.') !== false ||
        strpos($host, '172.31.') !== false ||
        $server_addr === '127.0.0.1' ||
        $server_addr === '::1'
    ) {
        $is_local = true;
    }

    if ($is_local) {
        // Tự động phát hiện cổng: Thử port đã lưu trước, nếu chưa có thử 3306 và 3307
        $ports = $working_port !== null ? [$working_port] : ['3306', '3307'];
        $username = 'root';
        $password = '';
        $last_exception = null;
        
        foreach ($ports as $port) {
            try {
                $dburl = "mysql:host=127.0.0.1;port=$port;dbname=cinepass;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 1
                ];
                $conn = new PDO($dburl, $username, $password, $options);
                $working_port = $port;
                return $conn;
            } catch (PDOException $e) {
                $last_exception = $e;
            }
        }
        
        // Nếu port đã nhớ bị đổi, thử tất cả các port còn lại
        $fallback_ports = ['3306', '3307'];
        foreach ($fallback_ports as $port) {
            try {
                $dburl = "mysql:host=127.0.0.1;port=$port;dbname=cinepass;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 1
                ];
                $conn = new PDO($dburl, $username, $password, $options);
                $working_port = $port;
                return $conn;
            } catch (PDOException $e) {
                $last_exception = $e;
            }
        }
        
        throw $last_exception;
    } else {
        // Chạy production (live server)
        $dburl = "mysql:host=localhost;port=3306;dbname=u508775056_cinepass;charset=utf8mb4";
        $username = 'u508775056_cinepass';
        $password = 'Kpy123456@@';
        $conn = new PDO($dburl, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
/**
 * Thực thi câu lệnh sql thao tác dữ liệu (INSERT, UPDATE, DELETE)
 * @param string $sql câu lệnh sql
 * @param array $args mảng giá trị cung cấp cho các tham số của $sql
 * @throws PDOException lỗi thực thi câu lệnh
 */
function pdo_execute($sql)
{
    $sql_args = array_slice(func_get_args(), 1);
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($sql_args);
    } catch (PDOException $e) {
        throw $e;
    }
}

function pdo_execute_return_interlastid($sql)
{
    $sql_args = array_slice(func_get_args(), 1);
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($sql_args);
        return $conn->lastInsertID();
    } catch (PDOException $e) {
        throw $e;
    }
}
/**
 * Thực thi câu lệnh sql truy vấn dữ liệu (SELECT)
 * @param string $sql câu lệnh sql
 * @param array $args mảng giá trị cung cấp cho các tham số của $sql
 * @return array mảng các bản ghi
 * @throws PDOException lỗi thực thi câu lệnh
 */
function pdo_query($sql)
{
    $sql_args = array_slice(func_get_args(), 1);
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($sql_args);
        $rows = $stmt->fetchAll();
        return $rows;
    } catch (PDOException $e) {
        throw $e;
    }
}
/**
 * Thực thi câu lệnh sql truy vấn một bản ghi
 * @param string $sql câu lệnh sql
 * @param array $args mảng giá trị cung cấp cho các tham số của $sql
 * @return array mảng chứa bản ghi
 * @throws PDOException lỗi thực thi câu lệnh
 */
function pdo_query_one($sql)
{
    $sql_args = array_slice(func_get_args(), 1);
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($sql_args);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } catch (PDOException $e) {
        throw $e;
    }
}
/**
 * Thực thi câu lệnh sql truy vấn một giá trị
 * @param string $sql câu lệnh sql
 * @param array $args mảng giá trị cung cấp cho các tham số của $sql
 * @return *giá trị
 * @throws PDOException lỗi thực thi câu lệnh
 */
function pdo_query_value($sql)
{
    $sql_args = array_slice(func_get_args(), 1);
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($sql_args);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? array_values($row)[0] : null;
    } catch (PDOException $e) {
        throw $e;
    }
}
