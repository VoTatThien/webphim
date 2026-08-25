<?php
// Prevent redeclaration
if (!function_exists('pdo_get_connection')) {
    function pdo_get_connection(){
        static $conn = null;
        static $working_port = null;
        
        // Nếu đã có kết nối đang mở trong phiên request, tái sử dụng ngay (Singleton)
        if ($conn !== null) {
            try {
                $conn->query("SELECT 1");
                return $conn;
            } catch (Exception $e) {
                $conn = null;
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
            $servername = "127.0.0.1";
            $dbname = "cinepass";
            $username = "root";
            $password = "";
            $last_e = null;
            
            foreach ($ports as $p) {
                try {
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 1
                    ];
                    $conn = new PDO("mysql:host=$servername;port=$p;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
                    $working_port = $p;
                    return $conn;
                } catch (PDOException $e) {
                    $last_e = $e;
                }
            }
            
            // Thử tất cả các port còn lại nếu port đã lưu bị đổi
            $fallback_ports = ['3306', '3307'];
            foreach ($fallback_ports as $p) {
                try {
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 1
                    ];
                    $conn = new PDO("mysql:host=$servername;port=$p;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
                    $working_port = $p;
                    return $conn;
                } catch (PDOException $e) {
                    $last_e = $e;
                }
            }
            
            throw $last_e;
        } else {
            // Chạy production (live server)
            $servername = "localhost";
            $port = "3306";
            $dbname = "u508775056_cinepass";
            $username = "u508775056_cinepass";
            $password = "Kpy123456@@";
            try {
                $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $conn;
            } catch(PDOException $e) {
                throw $e;
            }
        }
    }
}

if (!function_exists('pdo_execute')) {
    function pdo_execute($sql){
        $sql_args=array_slice(func_get_args(),1);
        try{
            $conn=pdo_get_connection();
            $stmt=$conn->prepare($sql);
            $stmt->execute($sql_args);
        }
        catch(PDOException $e){
            throw $e;
        }
    }
}

if (!function_exists('pdo_execute_return_interlastid')) {
    function pdo_execute_return_interlastid($sql) {
        $sql_args = array_slice(func_get_args(), 1);
        try {
            $conn = pdo_get_connection();
            $stmt = $conn->prepare($sql);
            $stmt->execute($sql_args);
            return $conn->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}

if (!function_exists('pdo_query')) {
    // truy vấn nhiều dữ liệu
    function pdo_query($sql){
        $sql_args=array_slice(func_get_args(),1);
        try{
            $conn=pdo_get_connection();
            $stmt=$conn->prepare($sql);
            $stmt->execute($sql_args);
            $rows=$stmt->fetchAll();
            return $rows;
        }
        catch(PDOException $e){
            throw $e;
        }
    }
}

if (!function_exists('pdo_query_one')) {
    // truy vấn  1 dữ liệu
    function pdo_query_one($sql){
        $sql_args=array_slice(func_get_args(),1);
        try{
            $conn=pdo_get_connection();
            $stmt=$conn->prepare($sql);
            $stmt->execute($sql_args);
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        catch(PDOException $e){
            throw $e;
        }
    }
}

if (!function_exists('pdo_get_connection')) {
    pdo_get_connection();
}