# -*- coding: utf-8 -*-
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
import os
from pathlib import Path

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def create_focused_document(output_path):
    doc = Document()
    
    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(0.8)
        
    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Times New Roman'
    normal_style.font.size = Pt(12)
    normal_style.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
    normal_style.paragraph_format.line_spacing = 1.25
    normal_style.paragraph_format.space_after = Pt(4)

    PRIMARY_COLOR = RGBColor(0x1B, 0x36, 0x5D)
    SECONDARY_COLOR = RGBColor(0x00, 0x66, 0x99)
    DARK_TEXT = RGBColor(0x22, 0x22, 0x22)
    GRAY_BG = "F4F6F9"
    PRIMARY_BG = "1B365D"
    ACCENT_BG = "E8F4F8"
    HIGHLIGHT_BG = "E6F4EA" # Light green for winning rows/cells

    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(16)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(14)
        run.font.bold = True
        run.font.color.rgb = PRIMARY_COLOR
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(12)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(12.5)
        run.font.bold = True
        run.font.color.rgb = SECONDARY_COLOR
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(8)
        p.paragraph_format.space_after = Pt(2)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(11.5)
        run.font.bold = True
        run.font.color.rgb = DARK_TEXT
        return p

    def add_callout(title, text, style_type="note"):
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        cell = tbl.cell(0, 0)
        set_cell_background(cell, ACCENT_BG if style_type == "note" else "FFF4E5")
        set_cell_margins(cell, top=120, bottom=120, left=160, right=160)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(3)
        r_title = p.add_run(f"📌 {title}\n" if style_type == "note" else f"💡 {title}\n")
        r_title.font.bold = True
        r_title.font.size = Pt(11)
        r_title.font.color.rgb = PRIMARY_COLOR if style_type == "note" else RGBColor(0x99, 0x4D, 0x00)
        r_body = p.add_run(text)
        r_body.font.size = Pt(10.5)
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    def add_styled_table(headers, data, col_widths=None, highlight_last_row=False):
        tbl = doc.add_table(rows=len(data) + 1, cols=len(headers))
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        hdr_row = tbl.rows[0]
        for col_idx, text in enumerate(headers):
            cell = hdr_row.cells[col_idx]
            set_cell_background(cell, PRIMARY_BG)
            set_cell_margins(cell, top=100, bottom=100, left=120, right=120)
            p = cell.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.space_after = Pt(2)
            run = p.add_run(text)
            run.font.bold = True
            run.font.size = Pt(10)
            run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            
        for row_idx, row_data in enumerate(data):
            row = tbl.rows[row_idx + 1]
            is_last = (row_idx == len(data) - 1) and highlight_last_row
            bg_color = HIGHLIGHT_BG if is_last else (GRAY_BG if row_idx % 2 == 1 else "FFFFFF")
            for col_idx, val in enumerate(row_data):
                cell = row.cells[col_idx]
                set_cell_background(cell, bg_color)
                set_cell_margins(cell, top=80, bottom=80, left=100, right=100)
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                run = p.add_run(str(val))
                run.font.size = Pt(9.5)
                if col_idx == 0 or is_last:
                    run.font.bold = True
                if is_last and col_idx == 2: # Winner cell
                    run.font.color.rgb = RGBColor(0x00, 0x66, 0x00)
                    
        if col_widths:
            for row in tbl.rows:
                for idx, w in enumerate(col_widths):
                    row.cells[idx].width = Inches(w)
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # --- Header Cover ---
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(10)
    p.paragraph_format.space_after = Pt(4)
    r = p.add_run("TÀI LIỆU KỸ THUẬT VÀ MINH HỌA THUẬT TOÁN")
    r.font.size = Pt(13)
    r.font.bold = True
    r.font.color.rgb = SECONDARY_COLOR

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(16)
    r = p.add_run("TÍNH NĂNG GỢI Ý COMBO ĐỒ ĂN THÔNG MINH\n(HYBRID COMBO RECOMMENDER SYSTEM)")
    r.font.size = Pt(16)
    r.font.bold = True
    r.font.color.rgb = PRIMARY_COLOR

    # --- PHẦN 1 ---
    add_h1("1. TỔNG QUAN VỀ TÍNH NĂNG")
    doc.add_paragraph(
        "Tính năng Gợi ý Combo Đồ Ăn là hệ thống khuyến nghị thông minh (Hybrid Recommendation System) "
        "được kích hoạt tự động tại bước đặt bắp nước (sau khi khách đã chọn ghế và chuẩn bị thanh toán). "
        "Hệ thống phân tích đồng thời thông tin vé xem phim, bối cảnh thời gian, hồ sơ khách hàng "
        "và lịch sử mua hàng của cộng đồng để tự động đề xuất 01 Combo phù hợp nhất và làm nổi bật trên giao diện với nhãn 'Khuyên dùng nhất'."
    )

    # --- PHẦN 2 ---
    add_h1("2. CODE NẰM Ở ĐÂU? (BẢN ĐỒ CÁC FILE & VỊ TRÍ MÃ NGUỒN)")
    
    code_map_headers = ["Thành phần", "Đường dẫn File", "Vị trí / Dòng", "Vai trò cụ thể"]
    code_map_data = [
        ["Model Thuật toán", "Trang-nguoi-dung/model/combo_recommend.php", "Toàn bộ file (~250 dòng)", "Chứa 9 hàm xử lý Collaborative Filtering, tính mức độ phổ biến (Popularity), ghi nhận log và tính toán chỉ số CTR"],
        ["Scoring Engine (View)", "Trang-nguoi-dung/view/doan.php", "Dòng 250 - 450", "Thu thập dữ liệu Session (ghế, phim, giờ, tuổi), chạy bộ chấm điểm 6 yếu tố F1->F6 và render Banner 'Khuyên Dùng'"],
        ["Tracking Controller", "Trang-nguoi-dung/index.php", "Case 'dv4' (Dòng 700 - 730)", "Bắt sự kiện khách nhấn 'Tiếp tục', đối soát combo thực tế chọn với combo đã gợi ý và ghi nhận trạng thái was_accepted"],
        ["Bảng CSDL Tracking", "DB/migration_recommendation_log.sql", "Toàn bộ file", "Định nghĩa cấu trúc bảng recommendation_log lưu trữ lịch sử gợi ý và kết quả chuyển đổi"],
        ["Dữ liệu thực nghiệm", "DB/sample_data_recommendation.sql", "Toàn bộ file", "Chứa 10 user chuẩn hóa nhân khẩu học, 70 vé mẫu đa dạng thể loại/khung giờ và 15 log mẫu"]
    ]
    add_styled_table(code_map_headers, code_map_data, [1.3, 2.2, 1.3, 2.4])

    # --- PHẦN 3 ---
    add_h1("3. CÁCH XÂY DỰNG TÍNH NĂNG NHƯ THẾ NÀO?")
    doc.add_paragraph(
        "Tính năng được xây dựng theo mô hình Hybrid Recommendation kết hợp giữa Content-Based Filtering "
        "và Item-Based Collaborative Filtering, qua 5 bước:"
    )

    add_h2("Bước 1: Thiết kế Cơ sở Dữ liệu & Bảng Tracking (recommendation_log)")
    doc.add_paragraph(
        "Tạo bảng `recommendation_log` để lưu vết mọi lượt gợi ý với các trường: id, id_user, id_phim, "
        "id_combo_suggested, reco_type, reco_score, scoring_factors (JSON snapshot), was_accepted (0/1), combo_actually_chosen, created_at."
    )

    add_h2("Bước 2: Xây dựng Module Lọc Cộng Tác (combo_recommend.php)")
    doc.add_paragraph(
        "Khai thác dữ liệu từ bảng `ve` (lịch sử bán vé):"
    )
    doc.add_paragraph(
        "• get_combo_popularity_map(): Đếm tổng số lượt bán của từng combo trong quá khứ.\n"
        "• get_popular_combo_by_movie($id_phim): Tìm combo nào được mua nhiều nhất khi khán giả xem bộ phim này (Item-Based CF).\n"
        "• get_popular_combo_by_genre($id_loai): Thống kê combo phổ biến nhất theo thể loại phim.\n"
        "• get_popular_combo_by_timeslot($gio): Thống kê combo chuộng nhất theo khung giờ chiếu (Sáng, Tối).\n"
        "• reco_log_suggestion() & reco_log_update_result(): Ghi nhận log gợi ý và cập nhật kết quả khi đặt vé."
    )

    add_h2("Bước 3: Xây dựng Bộ Chấm Điểm Đa Tiêu Chí (Weighted Scoring Engine trong doan.php)")
    add_callout(
        "CÔNG THỨC CHẤM ĐIỂM ĐA TIÊU CHÍ (WEIGHTED SUM)",
        "S(combo_i) = F1_keyword + F2_popularity + F3_genre + F4_time + F5_price + F6_cf\n\n"
        "• F1_keyword (Max 10đ): Điểm khớp từ khóa theo cụm hành vi (reco_type: family, couple, kid, sweet_girl, solo_king, healthy, solo).\n"
        "• F2_popularity (Max 7đ): Điểm độ phổ biến toàn hệ thống = (Số lượt bán combo / Lượt bán cao nhất) * 7.0\n"
        "• F3_genre (Max 5đ): Điểm tương thích thể loại phim (Hài -> Family; Ngôn tình -> VIP/Couple; Hoạt hình -> Standard; Kinh dị -> VIP).\n"
        "• F4_time (Max 3đ): Điểm khung giờ (Sáng < 12h: ưu tiên Standard; Tối >= 18h: ưu tiên Family/VIP).\n"
        "• F5_price (Max 4đ): Điểm ngân sách (Tiền vé > 200k -> ưu tiên combo cao cấp; Tiền vé < 100k -> ưu tiên combo tiết kiệm).\n"
        "• F6_cf (Max 6đ): Điểm Lọc Cộng Tác = (Số lượt mua combo i khi xem phim X / Lượt mua combo cao nhất với phim X) * 6.0\n\n"
        "==> Tổng điểm tối đa là 35.0 điểm. Combo có S(combo_i) lớn nhất sẽ được chọn làm 'Khuyên Dùng Nhất'.",
        "tip"
    )

    add_h2("Bước 4: Tích hợp Giao diện (doan.php)")
    doc.add_paragraph(
        "Combo có điểm cao nhất sẽ được đưa vào Banner Khuyên Dùng ở đầu trang kèm lý do gợi ý và gắn badge 'Khuyên dùng nhất' viền vàng trực tiếp trên thẻ sản phẩm."
    )

    add_h2("Bước 5: Cơ chế Đối soát & Tracking kết quả (index.php case dv4)")
    doc.add_paragraph(
        "Khi khách nhấn 'Tiếp tục', hệ thống kiểm tra combo khách chọn có chứa tên combo gợi ý hay không để cập nhật was_accepted = 1 hoặc 0 vào bảng recommendation_log."
    )

    # --- PHẦN 4 ---
    add_h1("4. CÁCH NÓ VẬN HÀNH RA LÀM SAO? (LUỒNG HOẠT ĐỘNG THỰC TẾ)")
    
    flow_headers = ["Giai đoạn", "Bên thực hiện", "Hành động / Xử lý chi tiết"]
    flow_data = [
        ["1. Khách chọn ghế", "Trang chọn ghế (ghe.php)", "Khách chọn 2 ghế VIP (H8, H9), suất 20h xem phim 'Mai' (Ngôn tình). Bấm 'Tiếp tục' -> Lưu thông tin vào $_SESSION['tong']."],
        ["2. Nạp trang đồ ăn", "doan.php (Engine)", "Hệ thống trích xuất: 2 ghế VIP -> reco_type = 'couple'. Giờ 20h -> Buổi tối. Thể loại: Ngôn tình. Đọc CSDL thấy khách xem 'Mai' 80% mua Combo VIP."],
        ["3. Chạy Scoring", "doan.php + combo_recommend.php", "Tính điểm cho từng combo: Combo VIP đạt F1(0) + F2(7.0) + F3(5.0) + F4(3.0) + F5(4.0) + F6(6.0) = 25.0 điểm (Cao nhất danh sách)."],
        ["4. Hiển thị gợi ý", "doan.php (View UI)", "Giao diện hiển thị Banner: 'CinePass gợi ý: Combo VIP - Hoàn hảo cho buổi hẹn hò xem phim lãng mạn'. Thẻ Combo VIP được gắn badge viền vàng."],
        ["5. Khách chọn mua", "Trang web (Browser)", "Khách hàng thấy gợi ý hợp lý -> Nhấn dấu [+] chọn 1 Combo VIP -> Nhấn 'Tiếp tục'."],
        ["6. Cập nhật Tracking", "index.php (dv4)", "Hệ thống phát hiện khách đã chọn đúng 'Combo VIP' -> Cập nhật was_accepted = 1 trong bảng recommendation_log. Luồng đặt vé chuyển sang thanh toán an toàn."]
    ]
    add_styled_table(flow_headers, flow_data, [1.4, 1.8, 4.0])

    # --- PHẦN 5: CÁC VÍ DỤ TÍNH TOÁN MINH HỌA ---
    add_h1("5. CÁC VÍ DỤ TÍNH TOÁN THỰC TẾ CHỨNG MINH THUẬT TOÁN (4 KỊCH BẢN)")
    doc.add_paragraph(
        "Dưới đây là 4 kịch bản tính toán chi tiết từng bước, chứng minh cơ chế ra quyết định của thuật toán trong các tình huống thực tế:"
    )

    # Kịch bản 1
    add_h2("Kịch bản 1: Gia đình 4 người đi xem phim Hài vào buổi sáng")
    doc.add_paragraph(
        "• Đầu vào: 4 ghế thường (E5, E6, E7, E8) -> Tiền vé 320.000đ | Phim: 'Gặp Lại Chị Bầu' (Hài) | Suất: 10:30 sáng.\n"
        "• Nhận diện cụm: Số ghế >= 3 => reco_type = 'family'."
    )
    kb1_headers = ["Thành phần điểm", "Max", "Combo Family (120k)", "Combo Standard (45k)", "Combo VIP (150k)"]
    kb1_data = [
        ["F1: Khớp từ khóa 'family'", "10.0", "10.0 (Chứa chữ 'Family')", "0.0", "0.0"],
        ["F2: Độ phổ biến lịch sử", "7.0", "5.2", "3.5", "7.0 (Bán chạy nhất)"],
        ["F3: Hợp thể loại phim Hài", "5.0", "3.0 (Hợp đi đông)", "0.0", "0.0"],
        ["F4: Khung giờ sáng 10h30", "3.0", "1.0", "3.0 (Nhẹ bụng)", "0.0"],
        ["F5: Ngân sách vé > 200k", "4.0", "4.0 (Khẩu phần lớn)", "0.0", "4.0"],
        ["F6: Lọc cộng tác CF theo phim", "6.0", "5.5 (Khách xem hài hay mua)", "2.0", "1.5"],
        ["TỔNG ĐIỂM S(combo)", "35.0", "28.7 điểm (THẮNG)", "8.5 điểm", "12.5 điểm"]
    ]
    add_styled_table(kb1_headers, kb1_data, [2.0, 0.6, 1.8, 1.4, 1.4], highlight_last_row=True)
    doc.add_paragraph("==> Kết quả: Gợi ý Combo Family. Lý do: Phù hợp cho nhóm/gia đình xem phim cùng nhau.")

    # Kịch bản 2
    add_h2("Kịch bản 2: Cặp đôi hẹn hò xem phim Ngôn Tình vào buổi tối")
    doc.add_paragraph(
        "• Đầu vào: 2 ghế VIP (J8, J9) -> Tiền vé 220.000đ | Phim: 'Mai' (Ngôn Tình) | Suất: 20:30 tối.\n"
        "• Nhận diện cụm: 2 ghế VIP => reco_type = 'couple'."
    )
    kb2_headers = ["Thành phần điểm", "Max", "Combo VIP (150k)", "Combo Premium (85k)", "Combo Family (120k)"]
    kb2_data = [
        ["F1: Khớp từ khóa 'couple'", "10.0", "0.0", "0.0", "0.0"],
        ["F2: Độ phổ biến lịch sử", "7.0", "7.0", "5.0", "5.2"],
        ["F3: Hợp thể loại Ngôn tình", "5.0", "5.0 (Lãng mạn)", "3.0", "0.0"],
        ["F4: Khung giờ tối 20h30", "3.0", "3.0 (Combo lớn)", "3.0", "3.0"],
        ["F5: Ngân sách vé > 200k", "4.0", "4.0 (Sẵn sàng chi tiêu)", "2.0", "2.0"],
        ["F6: Lọc cộng tác CF theo phim", "6.0", "6.0 (80% khách xem Mai chọn VIP)", "4.5", "1.0"],
        ["TỔNG ĐIỂM S(combo)", "35.0", "25.0 điểm (THẮNG)", "17.5 điểm", "11.2 điểm"]
    ]
    add_styled_table(kb2_headers, kb2_data, [2.0, 0.6, 1.8, 1.4, 1.4], highlight_last_row=True)
    doc.add_paragraph("==> Kết quả: Gợi ý Combo VIP. Lý do: Combo hoàn hảo cho buổi hẹn hò lãng mạn 2 người.")

    # Kịch bản 3
    add_h2("Kịch bản 3: Khách đi 1 mình xem phim Kinh Dị lúc 22h00 đêm")
    doc.add_paragraph(
        "• Đầu vào: 1 ghế đơn (F5) -> Tiền vé 80.000đ | Phim: 'Quỷ Cẩu' (Kinh Dị) | Suất: 22:00 đêm | Khách: Nam 24 tuổi.\n"
        "• Nhận diện cụm: 1 ghế + Nam + Phim Kinh Dị => reco_type = 'solo_king'."
    )
    kb3_headers = ["Thành phần điểm", "Max", "Combo VIP (150k)", "Combo Standard (45k)", "Bắp rang bơ (50k)"]
    kb3_data = [
        ["F1: Khớp từ khóa 'solo_king'", "10.0", "0.0", "0.0", "0.0"],
        ["F2: Độ phổ biến lịch sử", "7.0", "7.0", "3.5", "4.0"],
        ["F3: Hợp thể loại Kinh dị", "5.0", "5.0 (Nước lớn giải khát)", "0.0", "2.0"],
        ["F4: Khung giờ tối 22h00", "3.0", "3.0", "1.0", "1.0"],
        ["F5: Ngân sách vé 80k", "4.0", "0.0", "4.0 (Tiết kiệm)", "4.0 (Tiết kiệm)"],
        ["F6: Lọc cộng tác CF theo phim", "6.0", "6.0 (Phim kinh dị chuộng VIP)", "2.0", "3.0"],
        ["TỔNG ĐIỂM S(combo)", "35.0", "21.0 điểm (THẮNG)", "10.5 điểm", "14.0 điểm"]
    ]
    add_styled_table(kb3_headers, kb3_data, [2.0, 0.6, 1.8, 1.4, 1.4], highlight_last_row=True)
    doc.add_paragraph("==> Kết quả: Gợi ý Combo VIP (Up-selling thành công nhờ điểm CF và Thể loại áp đảo).")

    # Kịch bản 4
    add_h2("Kịch bản 4: Phim Mới Toanh Chưa Có Lịch Sử Mua Vé (Cold Start)")
    doc.add_paragraph(
        "• Đầu vào: Phim 'Doraemon Mới' vừa ra mắt (bảng vé chưa có dữ liệu) | 1 phụ huynh + 1 bé xem suất 09:00 sáng.\n"
        "• Xử lý Cold Start: F6 = 0.0, F2 = 0.0 (chưa có lịch sử). Hệ thống tự động dựa vào Content-Based Rules (F1, F3, F4, F5)."
    )
    kb4_headers = ["Thành phần điểm", "Max", "Combo Standard (45k)", "Combo Family (120k)"]
    kb4_data = [
        ["F1: Khớp từ khóa 'kid'", "10.0", "10.0 (Hợp trẻ em)", "0.0"],
        ["F2: Độ phổ biến lịch sử", "7.0", "0.0 (Cold Start)", "0.0 (Cold Start)"],
        ["F3: Hợp thể loại Hoạt hình", "5.0", "5.0 (Bắp ngọt cho bé)", "2.0"],
        ["F4: Suất sáng 9h00", "3.0", "3.0 (Khẩu phần vừa phải)", "0.0"],
        ["F5: Ngân sách vé", "4.0", "4.0", "2.0"],
        ["F6: Lọc cộng tác CF", "6.0", "0.0 (Cold Start)", "0.0 (Cold Start)"],
        ["TỔNG ĐIỂM S(combo)", "35.0", "22.0 điểm (THẮNG)", "4.0 điểm"]
    ]
    add_styled_table(kb4_headers, kb4_data, [2.2, 0.8, 2.1, 2.1], highlight_last_row=True)
    doc.add_paragraph("==> Kết quả: Gợi ý Combo Standard. Hệ thống hoạt động trơn tru không bị lỗi dù thiếu dữ liệu quá khứ.")

    # --- PHẦN 6 ---
    add_h1("6. TÓM TẮT ĐIỂM MẠNH KHI BẢO VỆ KHÓA LUẬN")
    doc.add_paragraph(
        "1. Tính cá nhân hóa cao: Thay đổi linh hoạt theo số ghế, thể loại phim, thời gian và giá vé.\n"
        "2. Khắc phục hoàn toàn Cold Start: Tự động chuyển đổi giữa Collaborative Filtering và Content-Based Rules linh hoạt.\n"
        "3. Có số liệu đo lường định lượng (CTR): Bảng log ghi nhận tỷ lệ chuyển đổi thực tế minh bạch."
    )

    doc.save(output_path)
    print(f"File updated successfully: {output_path}")

if __name__ == "__main__":
    desktop_dir = Path(os.path.expanduser("~")) / "Desktop"
    out_file = desktop_dir / "Chi_Tiet_Tinh_Nang_Goi_Y_Combo.docx"
    create_focused_document(str(out_file))
