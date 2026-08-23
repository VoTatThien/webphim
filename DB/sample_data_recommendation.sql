-- ============================================================================
-- DỮ LIỆU MẪU CHO DEMO COLLABORATIVE FILTERING
-- ============================================================================
-- Mục đích: Thêm dữ liệu vé đa dạng để hệ thống gợi ý hoạt động chính xác
-- Bao gồm: Nhiều user, nhiều phim, nhiều combo, nhiều khung giờ
-- 
-- Chạy file này SAU KHI đã import cinepass.sql
-- ============================================================================

-- =====================================================================
-- BƯỚC 1: Thêm tài khoản mẫu với đầy đủ ngay_sinh + gioi_tinh
-- (để User-Based CF hoạt động)
-- =====================================================================

INSERT INTO `taikhoan` (`id`, `name`, `user`, `pass`, `email`, `phone`, `dia_chi`, `vai_tro`, `id_rap`, `img`, `ngay_tao`, `diem_tich_luy`, `hang_thanh_vien`, `ngay_sinh`, `gioi_tinh`) VALUES
(60, 'Nguyễn Minh Tuấn', 'minhtuan', '123456', 'minhtuan@gmail.com', '0901000001', 'Gò Vấp', 0, NULL, '', NOW(), 500, 'dong', '2000-03-15', 'nam'),
(61, 'Trần Thị Hương', 'thihuong', '123456', 'thihuong@gmail.com', '0901000002', 'Bình Thạnh', 0, NULL, '', NOW(), 300, 'dong', '1998-07-22', 'nu'),
(62, 'Lê Văn Khoa', 'vankhoa', '123456', 'vankhoa@gmail.com', '0901000003', 'Tân Bình', 0, NULL, '', NOW(), 800, 'bac', '1995-11-08', 'nam'),
(63, 'Phạm Ngọc Ánh', 'ngocanh', '123456', 'ngocanh@gmail.com', '0901000004', 'Quận 7', 0, NULL, '', NOW(), 200, 'dong', '2003-01-30', 'nu'),
(64, 'Hoàng Đức Long', 'duclong', '123456', 'duclong@gmail.com', '0901000005', 'Thủ Đức', 0, NULL, '', NOW(), 1200, 'vang', '1990-09-12', 'nam'),
(65, 'Võ Thị Mai', 'thimai', '123456', 'thimai@gmail.com', '0901000006', 'Quận 1', 0, NULL, '', NOW(), 600, 'dong', '2001-05-18', 'nu'),
(66, 'Đỗ Quang Huy', 'quanghuy', '123456', 'quanghuy@gmail.com', '0901000007', 'Quận 3', 0, NULL, '', NOW(), 400, 'dong', '1997-12-25', 'nam'),
(67, 'Bùi Thị Lan', 'thilan', '123456', 'thilan@gmail.com', '0901000008', 'Phú Nhuận', 0, NULL, '', NOW(), 150, 'dong', '2005-08-14', 'nu'),
(68, 'Ngô Thanh Sơn', 'thanhson', '123456', 'thanhson@gmail.com', '0901000009', 'Tuy Hòa', 0, NULL, '', NOW(), 900, 'bac', '1975-04-30', 'nam'),
(69, 'Phan Thị Ngọc', 'thingoc', '123456', 'thingoc@gmail.com', '0901000010', 'Gò Vấp', 0, NULL, '', NOW(), 350, 'dong', '2008-02-10', 'nu');

-- =====================================================================
-- BƯỚC 2: Thêm vé mẫu (70+ records) - Đa dạng combo, phim, giờ chiếu
-- =====================================================================
-- Phim: 6(Hoạt hình), 8(Hoạt hình), 24(Cổ trang), 29(Hoạt hình), 
--        33(Ngôn tình), 36(Hành động), 37(Ngôn tình), 39(Hài), 
--        40(Hài), 41(Kinh dị), 42(Ngôn tình)
-- Combo: Combo Standard(45k), Combo Premium(85k), Combo Family(120k), 
--         Combo VIP(150k), Bắp rang bơ(50k), Cocacola(20k)
-- Rạp: 1(Q.1), 2(Q.7), 7(Tuy Hòa)
-- =====================================================================

INSERT INTO `ve` (`id`, `id_phim`, `id_rap`, `id_thoi_gian_chieu`, `id_ngay_chieu`, `id_tk`, `ghe`, `combo`, `price`, `id_hd`, `trang_thai`, `ngay_dat`) VALUES

-- === PHIM HÀI (39, 40) → Khách thường chọn Combo Family/Premium (đi nhóm, vui vẻ) ===
(1001, 39, 1, 4398, 1638, 60, 'E5,E6,E7', 'Combo Family', '360000', 0, 4, '2026-07-20 10:30:00'),
(1002, 39, 1, 4398, 1638, 61, 'F5,F6', 'Combo Premium', '250000', 0, 4, '2026-07-20 10:45:00'),
(1003, 39, 1, 4458, 1668, 62, 'G8,G9,G10,G11', 'Combo Family, Combo Premium', '520000', 0, 4, '2026-07-20 14:00:00'),
(1004, 39, 1, 4458, 1668, 63, 'H5', 'Combo Standard', '125000', 0, 4, '2026-07-20 14:15:00'),
(1005, 40, 1, 4328, 1603, 64, 'D5,D6,D7', 'Combo Family', '360000', 0, 4, '2026-07-21 19:00:00'),
(1006, 40, 1, 4328, 1603, 65, 'E8,E9', 'Combo Premium', '250000', 0, 4, '2026-07-21 19:15:00'),
(1007, 40, 1, 4384, 1631, 66, 'F3,F4,F5,F6', 'Combo Family, Combo VIP', '480000', 0, 4, '2026-07-22 20:00:00'),
(1008, 40, 2, 5424, 2151, 60, 'C7,C8', 'Combo Premium', '250000', 0, 4, '2026-07-22 15:30:00'),
(1009, 39, 1, 4398, 1638, 68, 'I5,I6,I7', 'Combo Family', '360000', 0, 4, '2026-07-23 10:00:00'),
(1010, 40, 1, 4458, 1668, 69, 'J3,J4', 'Combo Standard', '170000', 0, 4, '2026-07-23 14:30:00'),

-- === PHIM NGÔN TÌNH (33, 37, 42) → Khách thường chọn Combo Premium/VIP (cặp đôi) ===
(1011, 33, 1, 4704, 1791, 61, 'J8,J9', 'Combo VIP', '380000', 0, 4, '2026-07-24 19:30:00'),
(1012, 33, 1, 4704, 1791, 63, 'K5,K6', 'Combo Premium', '250000', 0, 4, '2026-07-24 19:45:00'),
(1013, 37, 1, 4694, 1786, 65, 'I10,I11', 'Combo VIP', '380000', 0, 4, '2026-07-25 20:00:00'),
(1014, 37, 1, 4694, 1786, 61, 'H8,H9', 'Combo Premium', '250000', 0, 4, '2026-07-25 20:15:00'),
(1015, 42, 1, 6565, 2601, 63, 'G6,G7', 'Combo VIP', '380000', 0, 4, '2026-07-26 20:30:00'),
(1016, 42, 1, 6565, 2601, 65, 'F10,F11', 'Combo Premium', '250000', 0, 4, '2026-07-26 20:45:00'),
(1017, 33, 1, 4722, 1800, 60, 'E3,E4', 'Combo VIP', '380000', 0, 4, '2026-07-27 21:00:00'),
(1018, 42, 1, 6565, 2601, 66, 'D8,D9', 'Combo Premium', '250000', 0, 4, '2026-07-27 21:15:00'),
(1019, 37, 2, 5671, 2277, 61, 'C5,C6', 'Combo VIP', '380000', 0, 4, '2026-07-28 19:00:00'),
(1020, 42, 1, 6565, 2601, 67, 'B7,B8', 'Combo Standard', '170000', 0, 4, '2026-07-28 19:30:00'),

-- === PHIM KINH DỊ (41) → Khách thường chọn Combo VIP/Premium (mạnh mẽ, đậm đà) ===
(1021, 41, 1, 4385, 1631, 60, 'H3,H4', 'Combo VIP', '380000', 0, 4, '2026-07-29 21:00:00'),
(1022, 41, 1, 4385, 1631, 62, 'G5', 'Combo VIP', '230000', 0, 4, '2026-07-29 21:15:00'),
(1023, 41, 1, 4446, 1662, 64, 'F7,F8', 'Combo Premium', '250000', 0, 4, '2026-07-30 22:00:00'),
(1024, 41, 1, 4446, 1662, 66, 'E9', 'Combo VIP', '230000', 0, 4, '2026-07-30 22:15:00'),
(1025, 41, 1, 4508, 1693, 62, 'D10,D11', 'Combo VIP, Bắp rang bơ', '310000', 0, 4, '2026-07-31 20:30:00'),
(1026, 41, 2, 5952, 2417, 60, 'C3,C4', 'Combo Premium', '250000', 0, 4, '2026-07-31 21:00:00'),

-- === PHIM HOẠT HÌNH (6, 8, 29) → Khách thường chọn Combo Standard/Family (trẻ em, gia đình) ===
(1027, 6, 1, 4398, 1638, 63, 'B5,B6,B7', 'Combo Family', '360000', 0, 4, '2026-08-01 09:30:00'),
(1028, 6, 1, 4398, 1638, 67, 'A8,A9', 'Combo Standard', '170000', 0, 4, '2026-08-01 09:45:00'),
(1029, 8, 1, 6353, 2522, 69, 'C10,C11,C12', 'Combo Family', '360000', 0, 4, '2026-08-01 10:00:00'),
(1030, 8, 1, 6353, 2522, 63, 'D3,D4', 'Combo Standard', '170000', 0, 4, '2026-08-01 10:15:00'),
(1031, 29, 1, 4328, 1603, 67, 'E10,E11,E12,E13', 'Combo Family, Combo Standard', '405000', 0, 4, '2026-08-02 10:30:00'),
(1032, 29, 1, 4384, 1631, 69, 'F8,F9', 'Combo Standard', '170000', 0, 4, '2026-08-02 11:00:00'),
(1033, 6, 2, 5424, 2151, 63, 'G3,G4,G5', 'Combo Family', '360000', 0, 4, '2026-08-02 14:00:00'),
(1034, 29, 1, 4328, 1603, 67, 'H7,H8', 'Combo Standard, Bắp rang bơ', '220000', 0, 4, '2026-08-03 09:00:00'),

-- === BUỔI SÁNG (< 12h) → Combo nhẹ (Standard, Bắp rang bơ) chiếm đa số ===
(1035, 39, 1, 4398, 1638, 60, 'A3,A4', 'Combo Standard', '170000', 0, 4, '2026-08-04 09:30:00'),
(1036, 40, 1, 4328, 1603, 62, 'B3', 'Bắp rang bơ', '130000', 0, 4, '2026-08-04 10:00:00'),
(1037, 6, 1, 4398, 1638, 63, 'C3', 'Combo Standard', '125000', 0, 4, '2026-08-04 10:30:00'),
(1038, 29, 1, 4384, 1631, 65, 'D3,D4', 'Bắp rang bơ', '180000', 0, 4, '2026-08-05 11:00:00'),
(1039, 8, 1, 6353, 2522, 67, 'E3', 'Combo Standard', '125000', 0, 4, '2026-08-05 11:30:00'),

-- === BUỔI TỐI (>= 18h) → Combo đầy đủ (Premium, VIP, Family) chiếm đa số ===
(1040, 33, 1, 4722, 1800, 61, 'F3,F4', 'Combo VIP', '380000', 0, 4, '2026-08-06 19:00:00'),
(1041, 41, 1, 4508, 1693, 62, 'G3,G4', 'Combo Premium', '250000', 0, 4, '2026-08-06 20:00:00'),
(1042, 42, 1, 6565, 2601, 64, 'H3,H4,H5', 'Combo Family, Combo VIP', '510000', 0, 4, '2026-08-06 20:30:00'),
(1043, 37, 1, 4694, 1786, 65, 'I3,I4', 'Combo Premium', '250000', 0, 4, '2026-08-07 21:00:00'),
(1044, 39, 1, 4458, 1668, 66, 'J3,J4,J5,J6', 'Combo Family, Combo Premium', '560000', 0, 4, '2026-08-07 19:30:00'),

-- === ĐA DẠNG THÊM: Nhiều user mua cùng 1 phim → Tăng CF accuracy ===
(1045, 42, 1, 6565, 2601, 60, 'K3,K4', 'Combo Premium', '250000', 0, 4, '2026-08-08 20:00:00'),
(1046, 42, 1, 6565, 2601, 62, 'L3,L4', 'Combo VIP', '380000', 0, 4, '2026-08-08 20:30:00'),
(1047, 42, 1, 6565, 2601, 64, 'A5,A6', 'Combo Premium', '250000', 0, 4, '2026-08-09 19:00:00'),
(1048, 39, 1, 4398, 1638, 62, 'B5,B6,B7', 'Combo Family', '360000', 0, 4, '2026-08-09 10:00:00'),
(1049, 39, 1, 4458, 1668, 64, 'C5,C6', 'Combo Premium', '250000', 0, 4, '2026-08-09 14:00:00'),
(1050, 39, 1, 4458, 1668, 66, 'D5,D6,D7,D8', 'Combo Family', '480000', 0, 4, '2026-08-10 14:30:00'),

-- === RẠP KHÁC (rạp 2, rạp 7) → Đa dạng location ===
(1051, 33, 2, 5671, 2277, 61, 'E5,E6', 'Combo Premium', '250000', 0, 4, '2026-08-10 19:00:00'),
(1052, 40, 2, 5952, 2417, 62, 'F5,F6,F7', 'Combo Family', '360000', 0, 4, '2026-08-10 20:00:00'),
(1053, 41, 7, 5671, 2277, 64, 'G5,G6', 'Combo VIP', '380000', 0, 4, '2026-08-11 21:00:00'),
(1054, 6, 7, 5424, 2151, 67, 'H5,H6,H7', 'Combo Family', '360000', 0, 4, '2026-08-11 10:00:00'),
(1055, 42, 7, 6565, 2601, 65, 'I5,I6', 'Combo VIP', '380000', 0, 4, '2026-08-11 20:30:00'),

-- === THÊM DỮ LIỆU: Khách không chọn combo (để có dữ liệu đối chiếu) ===
(1056, 39, 1, 4398, 1638, 68, 'J5', '', '80000', 0, 4, '2026-08-12 10:00:00'),
(1057, 33, 1, 4704, 1791, 69, 'K5', '', '80000', 0, 4, '2026-08-12 19:00:00'),
(1058, 41, 1, 4385, 1631, 60, 'L5', '', '80000', 0, 4, '2026-08-12 21:00:00'),

-- === Thêm vé với Cocacola (rạp 2) để combo riêng rạp cũng có data ===
(1059, 40, 2, 5424, 2151, 60, 'A7,A8', 'Cocacola', '200000', 0, 4, '2026-08-13 15:00:00'),
(1060, 33, 2, 5952, 2417, 63, 'B7,B8', 'Cocacola', '200000', 0, 4, '2026-08-13 19:00:00'),

-- === Combo VIP trên Tuy Hòa (rạp 7) ===
(1061, 8, 7, 6353, 2522, 68, 'C7,C8', 'Combo VIP', '180000', 0, 4, '2026-08-14 10:00:00'),
(1062, 42, 7, 6565, 2601, 69, 'D7,D8', 'Combo Premium', '250000', 0, 4, '2026-08-14 20:00:00'),

-- === THÊM ĐỢT CUỐI: Tăng tổng lên ~70 vé mới ===
(1063, 39, 1, 4398, 1638, 64, 'E7,E8', 'Combo Premium', '250000', 0, 4, '2026-08-15 14:00:00'),
(1064, 40, 1, 4384, 1631, 61, 'F7,F8,F9', 'Combo Family', '360000', 0, 4, '2026-08-15 19:30:00'),
(1065, 33, 1, 4722, 1800, 63, 'G7,G8', 'Combo VIP', '380000', 0, 4, '2026-08-16 20:00:00'),
(1066, 41, 1, 4446, 1662, 60, 'H7,H8', 'Combo VIP', '380000', 0, 4, '2026-08-16 21:30:00'),
(1067, 6, 1, 4398, 1638, 69, 'I7,I8,I9,I10', 'Combo Family, Combo Standard', '405000', 0, 4, '2026-08-17 09:30:00'),
(1068, 42, 1, 6565, 2601, 66, 'J7,J8', 'Combo Premium', '250000', 0, 4, '2026-08-17 20:00:00'),
(1069, 37, 1, 4694, 1786, 63, 'K7,K8', 'Combo VIP', '380000', 0, 4, '2026-08-18 19:00:00'),
(1070, 39, 1, 4458, 1668, 68, 'L7,L8', 'Bắp rang bơ', '180000', 0, 4, '2026-08-18 14:00:00');

-- =====================================================================
-- BƯỚC 3: Thêm dữ liệu mẫu cho recommendation_log (demo tracking)
-- =====================================================================

CREATE TABLE IF NOT EXISTS `recommendation_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_user` INT DEFAULT NULL,
    `id_phim` INT NOT NULL,
    `id_combo_suggested` INT NOT NULL,
    `reco_type` VARCHAR(30) NOT NULL,
    `reco_score` DECIMAL(6,1) DEFAULT NULL,
    `scoring_factors` TEXT DEFAULT NULL,
    `was_accepted` TINYINT(1) DEFAULT 0,
    `combo_actually_chosen` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_combo` (`id_combo_suggested`),
    KEY `idx_phim` (`id_phim`),
    KEY `idx_accepted` (`was_accepted`),
    KEY `idx_date` (`created_at`),
    KEY `idx_reco_type` (`reco_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `recommendation_log` (`id_user`, `id_phim`, `id_combo_suggested`, `reco_type`, `reco_score`, `scoring_factors`, `was_accepted`, `combo_actually_chosen`, `created_at`) VALUES
-- Gợi ý Family cho phim Hài → Khách chọn đúng (accepted)
(60, 39, 3, 'family', 24.0, '{"F1_keyword":10,"F2_popularity":5.2,"F3_genre":3,"F4_time":3,"F5_price":4,"F6_cf_movie":0,"total":25.2}', 1, 'Combo Family', '2026-08-01 10:30:00'),
(62, 39, 3, 'family', 23.5, '{"F1_keyword":10,"F2_popularity":5.2,"F3_genre":3,"F4_time":1,"F5_price":4,"F6_cf_movie":0,"total":23.2}', 1, 'Combo Family, Combo Premium', '2026-08-02 14:00:00'),
(66, 40, 3, 'family', 25.0, '{"F1_keyword":10,"F2_popularity":5.2,"F3_genre":3,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":31.2}', 1, 'Combo Family, Combo VIP', '2026-08-03 20:00:00'),
-- Gợi ý VIP cho phim Ngôn tình → Khách chọn đúng
(61, 33, 4, 'couple', 28.0, '{"F1_keyword":0,"F2_popularity":7.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":25.0}', 1, 'Combo VIP', '2026-08-04 19:30:00'),
(63, 42, 4, 'couple', 26.5, '{"F1_keyword":0,"F2_popularity":7.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":25.0}', 1, 'Combo VIP', '2026-08-05 20:30:00'),
-- Gợi ý Premium cho Kinh dị → Khách chọn VIP thay vì Premium (not accepted)
(62, 41, 2, 'solo_king', 20.0, '{"F1_keyword":0,"F2_popularity":4.5,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":0,"total":16.5}', 0, 'Combo VIP', '2026-08-06 21:00:00'),
-- Gợi ý Standard cho Hoạt hình buổi sáng → Khách chọn đúng
(63, 6, 1, 'kid', 22.0, '{"F1_keyword":0,"F2_popularity":3.5,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":21.5}', 1, 'Combo Standard', '2026-08-07 09:30:00'),
(67, 29, 1, 'kid', 21.0, '{"F1_keyword":0,"F2_popularity":3.5,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":21.5}', 1, 'Combo Standard, Bắp rang bơ', '2026-08-08 10:30:00'),
-- Gợi ý combo nhưng khách không chọn combo nào
(68, 39, 3, 'solo', 18.0, '{"F1_keyword":0,"F2_popularity":5.2,"F3_genre":3,"F4_time":3,"F5_price":4,"F6_cf_movie":0,"total":15.2}', 0, '', '2026-08-09 10:00:00'),
(69, 33, 4, 'solo', 17.5, '{"F1_keyword":0,"F2_popularity":7.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":25.0}', 0, '', '2026-08-10 19:00:00'),
-- Thêm log để CTR đẹp
(60, 42, 2, 'couple', 26.0, '{"F1_keyword":0,"F2_popularity":5.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":23.0}', 1, 'Combo Premium', '2026-08-11 20:00:00'),
(64, 42, 4, 'couple', 27.0, '{"F1_keyword":0,"F2_popularity":7.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":25.0}', 1, 'Combo VIP', '2026-08-12 20:30:00'),
(65, 37, 4, 'sweet_girl', 25.0, '{"F1_keyword":0,"F2_popularity":7.0,"F3_genre":5,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":25.0}', 1, 'Combo VIP', '2026-08-13 19:00:00'),
(64, 40, 3, 'family', 28.0, '{"F1_keyword":10,"F2_popularity":5.2,"F3_genre":3,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":31.2}', 1, 'Combo Family', '2026-08-14 19:00:00'),
(66, 39, 2, 'couple', 22.0, '{"F1_keyword":0,"F2_popularity":4.5,"F3_genre":3,"F4_time":3,"F5_price":4,"F6_cf_movie":6,"total":20.5}', 1, 'Combo Premium', '2026-08-15 14:00:00');

-- =====================================================================
-- KẾT QUẢ: 
-- + 10 tài khoản mẫu (có ngay_sinh + gioi_tinh)
-- + 70 vé mẫu (đa dạng combo, phim, giờ chiếu, rạp)
-- + 15 bản ghi recommendation_log (CTR = 11/15 = 73.3%)
-- =====================================================================
