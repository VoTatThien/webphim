-- ============================================================================
-- DEPLOYMENT PATCH - Khắc phục vấn đề Kế Hoạch Chiếu
-- ============================================================================
-- Ngày tạo: 2025-12-08
-- 
-- VẤN ĐỀ:
-- 1. Status hiển thị "?" - trang_thai_duyet = NULL
-- 2. Quản lý cụm không thấy kế hoạch để duyệt
--
-- GIẢI PHÁP:
-- 1. Cập nhật tất cả NULL trang_thai_duyet = 'Chờ duyệt'
-- 2. Thêm cột id_cum vào bảng rap_chieu (nếu chưa có)
-- ============================================================================

-- 1. Cập nhật tất cả NULL trang_thai_duyet thành 'Chờ duyệt'
UPDATE lichchieu 
SET trang_thai_duyet = 'Chờ duyệt'
WHERE trang_thai_duyet IS NULL 
   OR trang_thai_duyet = '';

-- 2. Kiểm tra xem cột trang_thai_duyet có nullable chưa - nếu có thì đổi thành NOT NULL
-- (An toàn: chỉ thay đổi nếu cần)
-- ALTER TABLE lichchieu 
-- MODIFY COLUMN trang_thai_duyet VARCHAR(50) NOT NULL DEFAULT 'Chờ duyệt';

-- Lưu ý: Nếu bạn chắc chắn muốn thay đổi constraint, hãy bỏ comment dòng trên
-- Nhưng nên backup database trước khi chạy

-- 3. Kiểm tra xem bảng rap_chieu có cột id_cum chưa (nếu chưa có thì thêm)
-- CẢNH BÁO: Chỉ chạy 2 dòng dưới nếu bảng rap_chieu CHƯA CÓ cột id_cum
-- ALTER TABLE rap_chieu ADD COLUMN id_cum INT DEFAULT NULL AFTER id;
-- ALTER TABLE rap_chieu ADD FOREIGN KEY (id_cum) REFERENCES cum(id) ON DELETE SET NULL;

-- ============================================================================
-- KIỂM TRA KẾT QUẢ
-- ============================================================================
-- Chạy câu lệnh này để kiểm tra:
--
-- SELECT id, ma_ke_hoach, trang_thai_duyet, COUNT(*) as so_lich
-- FROM lichchieu 
-- GROUP BY ma_ke_hoach 
-- HAVING trang_thai_duyet IS NULL OR trang_thai_duyet = '';
-- 
-- Kết quả nên trống (0 bản ghi)
-- 
-- ============================================================================


-- --------------------------------------------------------
-- Seed data added by Antigravity AI on 2026-06-08 04:36:18
-- --------------------------------------------------------

INSERT INTO `phim` (`id`, `tieu_de`, `daodien`, `dienvien`, `img`, `mo_ta`, `date_phat_hanh`, `thoi_luong_phim`, `id_loai`, `quoc_gia`, `gia_han_tuoi`, `link_trailer`, `trang_thai_duyet`) VALUES
(39, 'Thỏ ơi!', 'Trấn Thành', 'Trấn Thành, Uyển Ân, Lê Giang', 'tho_oi_hq.png', 'Bộ phim hài tình cảm gia đình đậm chất Trấn Thành dịp Tết Bính Ngọ 2026.', '2026-02-17', 120, 3, 'Việt Nam', 13, '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'da_duyet'),
(40, 'Nhà ba tôi một phòng', 'Trường Giang', 'Trường Giang, Nhã Phương, Phát La', 'nha_ba_toi_hq.png', 'Câu chuyện dở khóc dở cười về cuộc sống chung của gia đình ba thế hệ trong căn hộ chỉ có một phòng ngủ độc nhất.', '2026-02-17', 115, 3, 'Việt Nam', 13, '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'da_duyet'),
(41, 'Quỷ Nhập Tràng 2', 'Lý Hải', 'Quang Tuấn, Khả Như, Hoàng Yến Chibi', 'quy_nhap_trang_2_hq.png', 'Tiếp nối thành công của phần tiền truyện, khai thác câu chuyện tâm linh ghê rợn miền sông nước Nam Bộ.', '2026-03-20', 105, 1, 'Việt Nam', 18, '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'da_duyet'),
(42, 'Tài', 'Mai Tài Phến', 'Mai Tài Phến, Mỹ Tâm, Song Luân', 'tai_movie_hq.png', 'Bộ phim hành động kịch tính xen lẫn tình cảm gia đình ấm áp, là dự án tâm huyết đánh dấu sự kết hợp của Mai Tài Phến và Mỹ Tâm.', '2026-03-05', 110, 2, 'Việt Nam', 16, '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', 'da_duyet');

INSERT INTO `phim_rap` (`id_phim`, `id_rap`) VALUES
(39, 1), (39, 2), (39, 3), (39, 4), (39, 5), (39, 7),
(40, 1), (40, 2), (40, 3), (40, 4), (40, 5), (40, 7),
(41, 1), (41, 2), (41, 3), (41, 4), (41, 5), (41, 7),
(42, 1), (42, 2), (42, 3), (42, 4), (42, 5), (42, 7);
