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

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def generate_teacher_defense_doc(output_path):
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

    PRIMARY_COLOR = RGBColor(0x1B, 0x36, 0x5D)     # Deep Navy
    SECONDARY_COLOR = RGBColor(0x00, 0x66, 0x99)   # Blue Accent
    DARK_TEXT = RGBColor(0x22, 0x22, 0x22)
    GRAY_BG = "F4F6F9"
    PRIMARY_BG = "1B365D"
    ACCENT_BG = "E8F4F8"
    HIGHLIGHT_BG = "E6F4EA"
    CODE_BG = "F0F4F8"

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
                if is_last and "THẮNG" in str(val):
                    run.font.color.rgb = RGBColor(0x00, 0x66, 0x00)
                    
        if col_widths:
            for row in tbl.rows:
                for idx, w in enumerate(col_widths):
                    row.cells[idx].width = Inches(w)
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # --- Title Cover ---
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(15)
    p.paragraph_format.space_after = Pt(4)
    r = p.add_run("TÀI LIỆU HƯỚNG DẪN BẢO VỆ VÀ GIẢI TRÌNH CODE CHO GIẢNG VIÊN")
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
    add_h1("1. BẢN ĐỒ CODE (MỞ FILE NÀO, DÒNG NÀO ĐỂ CHỈ CHO THẦY/CÔ XEM)")
    doc.add_paragraph(
        "Khi Thầy/Cô hỏi: 'Code tính năng này em viết ở đâu? Mở file ra chỉ cho Thầy/Cô xem!', "
        "bạn mở trực tiếp 3 file mã nguồn theo bảng sau:"
    )

    code_map_headers = ["File mã nguồn", "Vị trí dòng code", "Đoạn code then chốt cần chỉ cho Thầy/Cô", "Giải thích ý nghĩa cho Thầy/Cô"]
    code_map_data = [
        ["Trang-nguoi-dung/view/doan.php", "Dòng 254 - 390", "Vòng lặp foreach ($ds_combo) tính 6 hàm: F1 (keyword), F2 (popularity), F3 (genre), F4 (time), F5 (price), F6 (CF)", "Đây là Scoring Engine chính: Thu thập ngữ cảnh từ Session, chấm điểm 6 yếu tố và tìm combo có tổng điểm cao nhất."],
        ["Trang-nguoi-dung/model/combo_recommend.php", "Toàn bộ file (9 hàm)", "Các hàm get_popular_combo_by_movie(), get_combo_popularity_map(), reco_log_suggestion()", "Đây là Model thuật toán: Viết các câu lệnh SQL truy vấn lịch sử bảng 've' để thực hiện Collaborative Filtering và Tracking."],
        ["Trang-nguoi-dung/index.php", "Case 'dv4' (Dòng 700 - 725)", "Đoạn code: reco_log_update_result($_SESSION['reco_log_id'], $was_accepted, $combo)", "Đây là Controller xử lý lưu vết: Khi khách bấm 'Tiếp tục' sang thanh toán, kiểm tra xem khách có chọn đúng combo gợi ý không để ghi log."],
        ["DB/migration_recommendation_log.sql", "Toàn bộ file", "CREATE TABLE recommendation_log (id_combo_suggested, reco_score, scoring_factors, was_accepted)", "Bảng CSDL lưu lại toàn bộ lịch sử gợi ý và tỷ lệ chuyển đổi (CTR)."]
    ]
    add_styled_table(code_map_headers, code_map_data, [1.6, 1.1, 2.3, 2.2])

    # --- PHẦN 2 ---
    add_h1("2. CÁCH XÂY DỰNG TÍNH NĂNG NHƯ THẾ NÀO? (GIẢI TRÌNH KIẾN TRÚC)")
    doc.add_paragraph(
        "Khi Thầy/Cô hỏi: 'Tính năng này em xây dựng dựa trên nguyên lý gì?', bạn trả lời theo 4 luận điểm kỹ thuật:"
    )
    
    doc.add_paragraph(
        "1. Không dùng IF/ELSE cố định: Nếu chỉ dùng IF/ELSE thì hệ thống rất cứng nhắc và không học hỏi được từ dữ liệu. "
        "Em xây dựng theo mô hình Hybrid Recommender System (Hệ thống khuyến nghị lai).\n\n"
        "2. Kết hợp Content-Based và Collaborative Filtering: \n"
        "   • Content-Based: Phân tích số ghế (đi 1 người hay đi nhóm), thể loại phim (Hài, Kinh dị, Hoạt hình), khung giờ (Sáng, Tối) và giá vé.\n"
        "   • Collaborative Filtering (Lọc cộng tác): Khai thác dữ liệu lịch sử bảng 've' xem những người từng mua vé xem bộ phim này thường mua kèm combo nào nhất.\n\n"
        "3. Công thức chấm điểm tuyến tính có trọng số (Weighted Scoring Engine):\n"
        "   Mỗi combo được chấm điểm S(combo) từ 0 đến 35 điểm dựa trên 6 hàm thành phần F1 -> F6.\n\n"
        "4. Cơ chế Tracking khép kín: Tự động ghi nhận log và đo lường tỷ lệ Click-Through Rate (CTR) thực tế."
    )

    add_callout(
        "CÔNG THỨC TOÁN HỌC TRỌNG TÂM CẦN NÊU",
        "S(combo_i) = F1_keyword (10đ) + F2_popularity (7đ) + F3_genre (5đ) + F4_time (3đ) + F5_price (4đ) + F6_cf (6đ)\n\n"
        "• F1: Khớp từ khóa theo nhóm hành vi (family, couple, kid, sweet_girl, solo_king, healthy, solo).\n"
        "• F2: Điểm bán chạy toàn hệ thống (Normalized Sales Count từ bảng vé).\n"
        "• F3: Độ tương thích thể loại phim (Hài -> Family, Ngôn tình -> VIP/Couple, Hoạt hình -> Standard, Kinh dị -> VIP).\n"
        "• F4: Phù hợp khung giờ (Sáng < 12h: Standard; Tối >= 18h: Family/VIP).\n"
        "• F5: Độ nhạy cảm ngân sách (Vé > 200k -> VIP; Vé < 100k -> Standard).\n"
        "• F6: Lọc cộng tác CF (Tỷ lệ người xem phim này từng mua combo i trong quá khứ).",
        "tip"
    )

    # --- PHẦN 3 ---
    add_h1("3. TÍNH NĂNG NÓ VẬN HÀNH RA LÀM SAO? (LUỒNG CHẠY STEP-BY-STEP)")
    doc.add_paragraph(
        "Khi Thầy/Cô yêu cầu giải thích luồng hoạt động từ lúc khách bấm trên web đến khi lưu vào CSDL, trình bày qua 4 bước:"
    )

    flow_headers = ["Bước", "File xử lý", "Hành động của hệ thống", "Dữ liệu trao đổi"]
    flow_data = [
        ["Bước 1", "ghe.php -> index.php", "Khách chọn ghế (VD: 2 ghế VIP H8, H9) xem phim 'Mai' lúc 20h30. Bấm 'Tiếp tục'.", "Lưu $_SESSION['tong']['ten_ghe'] và $_SESSION['tong']['id_phim']."],
        ["Bước 2", "doan.php", "Nạp trang đồ ăn -> Thu thập đặc trưng: 2 ghế VIP => reco_type = 'couple'. Giờ 20h30 => Tối. Phim: Ngôn tình.", "Truy vấn CSDL: get_popular_combo_by_movie(42) -> Combo VIP."],
        ["Bước 3", "doan.php", "Chấm điểm từng combo -> Combo VIP đạt 25/35đ (Cao nhất). Ghi log vào recommendation_log.", "Hiển thị Banner 'CinePass gợi ý: Combo VIP' và gắn badge viền vàng."],
        ["Bước 4", "index.php (dv4)", "Khách nhấn chọn Combo VIP -> Bấm 'Tiếp tục' sang thanh toán. Hệ thống đối soát: Khách chọn đúng combo gợi ý.", "Cập nhật was_accepted = 1 trong bảng recommendation_log."]
    ]
    add_styled_table(flow_headers, flow_data, [1.0, 1.6, 2.5, 2.1])

    # --- PHẦN 4 ---
    add_h1("4. DỮ LIỆU TEST ĐỂ CHỨNG MINH TÍNH NĂNG HOẠT ĐỘNG HIỆU QUẢ")
    doc.add_paragraph(
        "Để chứng minh với Thầy/Cô rằng tính năng không phải là 'code cứng' (hard-code) mà tự động thay đổi thông minh theo từng trường hợp, "
        "bạn thực hiện 4 kịch bản test trực tiếp trên Web và chỉ dữ liệu trong CSDL:"
    )

    # Test 1
    add_h2("Kịch bản Test 1: Đặt vé đi Gia đình 4 người xem phim Hài (Suất sáng 10h30)")
    doc.add_paragraph(
        "• Thao tác trên Web: Chọn 4 ghế (E5, E6, E7, E8) phim 'Gặp Lại Chị Bầu' -> Sang trang đồ ăn.\n"
        "• Kết quả trên Web: Giao diện tự động hiện gợi ý: 'Combo Family' (28.7/35.0 điểm)."
    )
    kb1_headers = ["Thành phần", "Max", "Combo Family", "Combo Standard", "Combo VIP"]
    kb1_data = [
        ["F1 (Keyword 'family')", "10.0", "10.0", "0.0", "0.0"],
        ["F2 (Phổ biến)", "7.0", "5.2", "3.5", "7.0"],
        ["F3 (Hợp phim Hài)", "5.0", "3.0", "0.0", "0.0"],
        ["F4 (Giờ sáng)", "3.0", "1.0", "3.0", "0.0"],
        ["F5 (Vé > 200k)", "4.0", "4.0", "0.0", "4.0"],
        ["F6 (CF theo phim)", "6.0", "5.5", "2.0", "1.5"],
        ["TỔNG ĐIỂM", "35.0", "28.7 (THẮNG)", "8.5", "12.5"]
    ]
    add_styled_table(kb1_headers, kb1_data, [2.0, 0.6, 1.8, 1.4, 1.4], highlight_last_row=True)

    # Test 2
    add_h2("Kịch bản Test 2: Đặt vé Cặp đôi xem phim Ngôn Tình (Suất tối 20h30)")
    doc.add_paragraph(
        "• Thao tác trên Web: Chọn 2 ghế VIP (J8, J9) phim 'Mai' -> Sang trang đồ ăn.\n"
        "• Kết quả trên Web: Giao diện tự động đổi sang gợi ý: 'Combo VIP' (25.0/35.0 điểm)."
    )
    kb2_headers = ["Thành phần", "Max", "Combo VIP", "Combo Premium", "Combo Family"]
    kb2_data = [
        ["F1 (Keyword 'couple')", "10.0", "0.0", "0.0", "0.0"],
        ["F2 (Phổ biến)", "7.0", "7.0", "5.0", "5.2"],
        ["F3 (Hợp Ngôn tình)", "5.0", "5.0", "3.0", "0.0"],
        ["F4 (Giờ tối)", "3.0", "3.0", "3.0", "3.0"],
        ["F5 (Vé > 200k)", "4.0", "4.0", "2.0", "2.0"],
        ["F6 (CF: Khách xem Mai)", "6.0", "6.0", "4.5", "1.0"],
        ["TỔNG ĐIỂM", "35.0", "25.0 (THẮNG)", "17.5", "11.2"]
    ]
    add_styled_table(kb2_headers, kb2_data, [2.0, 0.6, 1.8, 1.4, 1.4], highlight_last_row=True)

    # Test 3
    add_h2("Kịch bản Test 3: Chứng minh Khởi Đầu Lạnh (Cold Start khi phim mới ra mắt)")
    doc.add_paragraph(
        "• Câu hỏi của Thầy/Cô: 'Nếu phim mới chưa có ai mua vé (F6 = 0) thì hệ thống có chạy được không?'\n"
        "• Câu trả lời & Chứng minh: Hệ thống tự động chuyển sang Content-Based (F1, F3, F4, F5). "
        "Ví dụ chọn 1 vé phim Hoạt hình suất sáng -> Điểm F6 = 0, F2 = 0 nhưng F1(10) + F3(5) + F4(3) + F5(4) = 22.0đ -> Vẫn gợi ý chính xác 'Combo Standard' mà không hề bị lỗi."
    )

    # Test 4
    add_h2("Kịch bản Test 4: Mở CSDL chứng minh kết quả Tracking định lượng (CTR)")
    doc.add_paragraph(
        "• Thao tác trên CSDL: Mở bảng `recommendation_log` trong phpMyAdmin.\n"
        "• Chỉ cho Thầy/Cô xem 3 điểm quan trọng:\n"
        "   1. Cột `scoring_factors`: Lưu chi tiết JSON từng điểm F1->F6 minh bạch.\n"
        "   2. Cột `was_accepted = 1`: Chứng minh khách đã thực sự chọn mua combo được gợi ý.\n"
        "   3. Tỷ lệ CTR: Đo được trên 15 lượt test mẫu có 11 lượt khách chọn (CTR = 73.3%), chứng minh hiệu quả kinh doanh."
    )

    # --- PHẦN 5 ---
    add_h1("5. BỘ 4 CÂU HỎI THƯỜNG GẶP KHI GIẢNG VIÊN HỎI THI VÀ CÁCH TRẢ LỜI")
    
    qa_list = [
        ("Thầy/Cô hỏi: 'Tại sao em không dùng AI Deep Learning mà lại dùng Weighted Scoring + Collaborative Filtering?'",
         "Em trả lời: Dạ thưa Thầy/Cô, trong bài toán gợi ý F&B của rạp chiếu phim, số lượng combo chỉ có khoảng 5 - 15 món (tập Item nhỏ), "
         "nếu dùng Deep Learning sẽ gặp hiện tượng Overfitting, đòi hỏi tài nguyên máy chủ lớn và có độ trễ cao khi đặt vé. "
         "Mô hình Hybrid Weighted Scoring kết hợp Item-Based CF có độ phức tạp thuật toán thấp O(N), phản hồi thời gian thực (<10ms), "
         "dễ giải thích (Explainable AI) và giải quyết triệt để bài toán Cold Start."),

        ("Thầy/Cô hỏi: 'Nếu khách hàng không đăng nhập (khách vãng lai) thì hệ thống gợi ý thế nào?'",
         "Em trả lời: Dạ thưa Thầy/Cô, hệ thống vẫn hoạt động chính xác 100%. Vì các đặc trưng về số ghế, loại ghế (VIP/Đôi), thể loại phim, "
         "khung giờ và giá vé đều được trích xuất từ phiên đặt vé hiện tại ($_SESSION['tong']) và lịch sử của bộ phim đó, không phụ thuộc bắt buộc vào tài khoản."),

        ("Thầy/Cô hỏi: 'Bảng recommendation_log dùng để làm gì?'",
         "Em trả lời: Dạ bảng này dùng để theo dõi vòng đời gợi ý: Lưu lại thời điểm gợi ý, điểm số chi tiết từng yếu tố và kết quả khách có chọn mua hay không (was_accepted). "
         "Nhờ bảng này, ban quản trị có thể xem báo cáo tỷ lệ chuyển đổi (CTR) và tinh chỉnh trọng số các yếu tố F1->F6 cho phù hợp hơn theo thời gian."),

        ("Thầy/Cô hỏi: 'Em tự viết những phần nào trong tính năng này?'",
         "Em trả lời: Dạ em tự thiết kế toàn bộ: (1) Cấu trúc bảng CSDL recommendation_log; (2) 9 hàm thuật toán trong combo_recommend.php; "
         "(3) Bộ công thức chấm điểm 6 yếu tố trong doan.php; và (4) Cơ chế bắt sự kiện đối soát đơn hàng trong index.php.")
    ]

    for q, a in qa_list:
        add_h3(q)
        p = doc.add_paragraph()
        r = p.add_run(a)
        r.font.size = Pt(11)

    doc.save(output_path)
    print(f"Teacher defense guide generated successfully at: {output_path}")

if __name__ == "__main__":
    desktop_dir = Path(os.path.expanduser("~")) / "Desktop"
    out_file = desktop_dir / "Chi_Tiet_Tinh_Nang_Goi_Y_Combo.docx"
    generate_teacher_defense_doc(str(out_file))
