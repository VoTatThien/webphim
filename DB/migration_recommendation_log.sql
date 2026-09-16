-- ============================================================================
-- Migration: Tạo bảng recommendation_log
-- Mục đích: Tracking hiệu quả của hệ thống gợi ý combo đồ ăn
-- Ngày tạo: 2026-08-23
-- ============================================================================

CREATE TABLE IF NOT EXISTS `recommendation_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_user` INT DEFAULT NULL COMMENT 'ID tài khoản (NULL nếu khách vãng lai)',
    `id_phim` INT NOT NULL COMMENT 'ID phim đang đặt vé',
    `id_combo_suggested` INT NOT NULL COMMENT 'ID combo được hệ thống gợi ý',
    `reco_type` VARCHAR(30) NOT NULL COMMENT 'Loại gợi ý: family/couple/kid/sweet_girl/solo_king/healthy/solo',
    `reco_score` DECIMAL(6,1) DEFAULT NULL COMMENT 'Tổng điểm scoring (Weighted Sum)',
    `scoring_factors` TEXT DEFAULT NULL COMMENT 'JSON chi tiết điểm từng yếu tố: F1_keyword, F2_popularity, F3_genre, F4_time, F5_price, F6_cf_movie',
    `was_accepted` TINYINT(1) DEFAULT 0 COMMENT '1 = khách chọn đúng combo được gợi ý, 0 = không',
    `combo_actually_chosen` VARCHAR(500) DEFAULT NULL COMMENT 'Combo(s) khách thực sự chọn',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    KEY `idx_combo` (`id_combo_suggested`),
    KEY `idx_phim` (`id_phim`),
    KEY `idx_accepted` (`was_accepted`),
    KEY `idx_date` (`created_at`),
    KEY `idx_reco_type` (`reco_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Bảng log tracking hiệu quả hệ thống Hybrid Recommendation cho Combo đồ ăn';
