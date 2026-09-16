-- Thêm cột khoang_tuoi vào bảng taikhoan nếu chưa tồn tại
DELIMITER //
CREATE PROCEDURE AddKhoangTuoiColumn()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'taikhoan' AND COLUMN_NAME = 'khoang_tuoi' AND TABLE_SCHEMA = DATABASE()
    ) THEN
        ALTER TABLE taikhoan ADD COLUMN khoang_tuoi VARCHAR(20) DEFAULT NULL;
    END IF;
END //
DELIMITER ;
CALL AddKhoangTuoiColumn();
DROP PROCEDURE AddKhoangTuoiColumn();

-- Tạo bảng combo_training_data
CREATE TABLE IF NOT EXISTS combo_training_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    the_loai VARCHAR(50) NOT NULL,
    gio_chieu VARCHAR(10) NOT NULL,
    khoang_tuoi VARCHAR(20) NOT NULL,
    gioi_tinh VARCHAR(10) NOT NULL,
    thu_trong_tuan VARCHAR(10) NOT NULL,
    so_ghe VARCHAR(5) NOT NULL,
    combo_chon VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng decision_tree_model
CREATE TABLE IF NOT EXISTS decision_tree_model (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(100) NOT NULL DEFAULT 'combo_recommender',
    model_data LONGTEXT NOT NULL,
    accuracy DECIMAL(5,2) DEFAULT NULL,
    trained_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    training_samples INT DEFAULT 0,
    features_used TEXT DEFAULT NULL,
    UNIQUE KEY (model_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
