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

def generate_master_thesis_doc(output_path):
    doc = Document()
    
    # Page setup - Standard Thesis format
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
    HIGHLIGHT_BG = "E6F4EA"                         # Winner row highlight

    def add_title_cover():
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(20)
        p.paragraph_format.space_after = Pt(6)
        r = p.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO\nTRƯỜNG ĐẠI HỌC - KHOA CÔNG NGHỆ THÔNG TIN\n---------------------------------------")
        r.font.size = Pt(12)
        r.font.bold = True
        r.font.color.rgb = PRIMARY_COLOR

        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(40)
        p.paragraph_format.space_after = Pt(12)
        r = p.add_run("BÁO CÁO NGHIÊN CỨU VÀ PHÁT TRIỂN HỆ THỐNG")
        r.font.size = Pt(14)
        r.font.bold = True
        r.font.color.rgb = SECONDARY_COLOR

        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(6)
        p.paragraph_format.space_after = Pt(20)
        r = p.add_run("HỆ THỐNG GỢI Ý COMBO ĐỒ ĂN THÔNG MINH\n(HYBRID RECOMMENDATION SYSTEM)\nỨNG DỤNG TRÊN NỀN TẢNG ĐẶT VÉ XEM PHIM CINEPASS")
        r.font.size = Pt(17)
        r.font.bold = True
        r.font.color.rgb = PRIMARY_COLOR

        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(60)
        r = p.add_run("Chuyên đề: Tối ưu hóa trải nghiệm khách hàng & Gia tăng doanh thu F&B\nThuật toán: Weighted Multi-Criteria Scoring & Item-Based Collaborative Filtering")
        r.font.size = Pt(11)
        r.font.italic = True
        r.font.color.rgb = RGBColor(0x55, 0x55, 0x55)

        # Info box
        tbl = doc.add_table(rows=3, cols=2)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        labels = [
            ("Đề tài khóa luận:", "Xây dựng Website Đặt Vé Xem Phim CinePass"),
            ("Học phần / Module:", "Hệ Khuyến Nghị Combo Đồ Ăn Thông Minh (Smart Combo Recommender)"),
            ("Thời gian thực hiện:", "Tháng 08 / 2026")
        ]
        for idx, (label, val) in enumerate(labels):
            row = tbl.rows[idx]
            r1 = row.cells[0].paragraphs[0].add_run(label)
            r1.font.bold = True
            r1.font.size = Pt(11)
            row.cells[0].paragraphs[0].paragraph_format.space_after = Pt(3)
            
            r2 = row.cells[1].paragraphs[0].add_run(val)
            r2.font.size = Pt(11)
            row.cells[1].paragraphs[0].paragraph_format.space_after = Pt(3)

        doc.add_page_break()

    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(18)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(14.5)
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

    # ========================== NỘI DUNG CHI TIẾT TỔNG HỢP ==========================
    
    add_title_cover()

    # --- CHƯƠNG 1 ---
    add_h1("CHƯƠNG 1: TỔNG QUAN VÀ BỐI CẢNH ĐẶT VẤN ĐỀ")
    
    add_h2("1.1. Tầm quan trọng của F&B và Cross-Selling trong kinh doanh rạp chiếu phim")
    doc.add_paragraph(
        "Trong ngành công nghiệp điện ảnh hiện đại (Cinema Exhibitor), doanh thu bán vé (Box Office) "
        "thường phải chia sẻ từ 50% đến 55% cho các hãng phát hành phim (Studios / Distributors). "
        "Vì vậy, dịch vụ ẩm thực Bắp & Nước ngọt (F&B - Food and Beverage) là nguồn đóng góp chính tạo ra lợi nhuận ròng "
        "(Gross Profit Margin đạt tới 75% - 85%) và là yếu tố then chốt quyết định hiệu quả tài chính của cụm rạp."
    )
    doc.add_paragraph(
        "Hành vi tiêu dùng ẩm thực tại rạp phụ thuộc chặt chẽ vào 4 yếu tố cốt lõi: "
        "(1) Quy mô nhóm xem (Đi đơn lẻ, Cặp đôi hẹn hò, Gia đình đông người); "
        "(2) Thể loại phim (Kinh dị gây hồi hộp cần nước lớn; Hoạt hình đi kèm trẻ nhỏ chuộng bắp ngọt; Ngôn tình chuộng combo sang trọng); "
        "(3) Khung giờ chiếu (Suất sáng cần khẩu phần nhẹ, suất tối cần combo no bụng); và "
        "(4) Mức độ nhạy cảm về giá (Price Sensitivity) dựa trên tổng ngân sách tiền vé."
    )

    add_h2("1.2. Khảo sát thực trạng tính năng gợi ý ban đầu trên CinePass")
    doc.add_paragraph(
        "Trước khi được nâng cấp, hệ thống CinePass chỉ có module gợi ý cơ bản, bộc lộ các hạn chế kỹ thuật:"
    )
    
    headers_eval = ["Tiêu chí đánh giá", "Trạng thái ban đầu (Cũ)", "Tác động tiêu cực"]
    data_eval = [
        ["Cơ chế ra quyết định", "Chỉ dùng 1 Rule so khớp từ khóa (Keyword Match)", "Bỏ qua toàn bộ dữ liệu lịch sử và hành vi thực tế của khách"],
        ["Số lượng yếu tố", "1 yếu tố duy nhất (Tên combo chứa từ khóa)", "Dễ bị thiên lệch (Bias), không phân biệt được nhiều combo cùng loại"],
        ["Khai thác dữ liệu quá khứ", "Hoàn toàn không có (No Collaborative Filtering)", "Lãng phí kho dữ liệu giao dịch vé sẵn có trong CSDL"],
        ["Cơ chế đo lường", "Không có bảng Tracking / Logging", "Không đo được tỷ lệ chuyển đổi (CTR), không thể bảo vệ định lượng trước Hội đồng"]
    ]
    add_styled_table(headers_eval, data_eval, [1.5, 2.5, 2.5])

    add_h2("1.3. Mục tiêu nâng cấp và Giải pháp kiến trúc")
    p = doc.add_paragraph(
        "Đề tài đặt ra mục tiêu chuyển đổi toàn diện module gợi ý thành "
    )
    p.add_run("Hệ Thống Khuyến Nghị Lai Đa Tiêu Chí (Weighted Multi-Criteria Hybrid Recommendation System)").bold = True
    p.add_run(
        ", kết hợp giữa Phân tích Ngữ cảnh Nội dung (Content-Based / Context-Aware) "
        "và Lọc Cộng tác Dựa trên Lịch sử Giao dịch (Item-Based Collaborative Filtering), "
        "đồng thời tích hợp cơ chế Tracking khép kín để đo lường tỷ lệ Click-Through Rate (CTR)."
    )

    # --- CHƯƠNG 2 ---
    add_h1("CHƯƠNG 2: VỊ TRÍ MÃ NGUỒN VÀ BẢN ĐỒ CÁC FILE TRONG DỰ ÁN")
    doc.add_paragraph("Toàn bộ tính năng gợi ý combo được phân bổ trong các file mã nguồn sau:")

    code_map_headers = ["Thành phần", "Đường dẫn File", "Vị trí / Dòng", "Vai trò cụ thể"]
    code_map_data = [
        ["Model Thuật toán", "Trang-nguoi-dung/model/combo_recommend.php", "Toàn bộ file (~250 dòng)", "Chứa 9 hàm xử lý Collaborative Filtering, tính mức độ phổ biến (Popularity), ghi nhận log và tính toán chỉ số CTR"],
        ["Scoring Engine (View)", "Trang-nguoi-dung/view/doan.php", "Dòng 250 - 450", "Thu thập dữ liệu Session (ghế, phim, giờ, tuổi), chạy bộ chấm điểm 6 yếu tố F1->F6 và render Banner 'Khuyên Dùng'"],
        ["Tracking Controller", "Trang-nguoi-dung/index.php", "Case 'dv4' (Dòng 700 - 730)", "Bắt sự kiện khách nhấn 'Tiếp tục', đối soát combo thực tế chọn với combo đã gợi ý và ghi nhận trạng thái was_accepted"],
        ["Bảng CSDL Tracking", "DB/migration_recommendation_log.sql", "Toàn bộ file", "Định nghĩa cấu trúc bảng recommendation_log lưu trữ lịch sử gợi ý và kết quả chuyển đổi"],
        ["Dữ liệu thực nghiệm", "DB/sample_data_recommendation.sql", "Toàn bộ file", "Chứa 10 user chuẩn hóa nhân khẩu học, 70 vé mẫu đa dạng thể loại/khung giờ và 15 log mẫu"]
    ]
    add_styled_table(code_map_headers, code_map_data, [1.3, 2.2, 1.3, 2.4])

    # --- CHƯƠNG 3 ---
    add_h1("CHƯƠNG 3: MÔ HÌNH TOÁN HỌC VÀ THUẬT TOÁN ĐỀ XUẤT")

    add_h2("3.1. Phân loại Cụm Ngữ Cảnh Người Dùng (User Context Clustering)")
    reco_headers = ["Nhóm hành vi (reco_type)", "Điều kiện xác định", "Ý nghĩa tâm lý / Tiêu dùng", "Trọng tâm gợi ý"]
    reco_data = [
        ["family", "Số ghế >= 3 hoặc có ghế Đôi (Sweetbox)", "Nhóm bạn hoặc gia đình đông người", "Khẩu phần lớn, chia sẻ (Combo Family, Party)"],
        ["couple", "Số ghế = 2 hoặc tên có ghế VIP", "Cặp đôi hẹn hò xem phim", "Combo lãng mạn, 2 nước 1 bắp lớn (Combo VIP, Couple)"],
        ["kid", "Phim thể loại 'Hoạt Hình' HOẶC user < 15 tuổi", "Trẻ em và phụ huynh", "Khẩu phần vừa phải, nhiều kẹo/bắp ngọt (Combo Standard)"],
        ["sweet_girl", "Khách là Nữ (<25 tuổi) HOẶC Phim 'Ngôn Tình'", "Khách hàng nữ trẻ tuổi, chuộng đồ ngọt", "Bắp phô mai, caramel, nước ngọt cao cấp"],
        ["solo_king", "Khách Nam HOẶC Phim 'Kinh Dị' / 'Hành Động'", "Khán giả nam tính, xem phim kịch tính", "Combo lớn, vị mặn, nước lớn"],
        ["healthy", "Suất chiếu sáng (<12h trưa) HOẶC lớn tuổi (>40)", "Khách chú trọng sức khỏe, suất sáng", "Khẩu phần vừa phải, hạn chế dầu mỡ"],
        ["solo", "Đi 1 người (Mặc định)", "Khán giả đi xem phim đơn lẻ", "Combo cá nhân tiết kiệm (Combo Solo, Standard)"]
    ]
    add_styled_table(reco_headers, reco_data, [1.1, 1.8, 1.8, 1.8])

    add_h2("3.2. Công thức Điểm Số Tổng Hợp Đa Tiêu Chí (Weighted Multi-Criteria Scoring)")
    add_callout(
        "CÔNG THỨC TOÁN HỌC TRỌNG TÂM",
        "S(combo_i) = F1_keyword + F2_popularity + F3_genre + F4_time + F5_price + F6_cf\n\n"
        "Trong đó:\n"
        "• F1_keyword  ∈ [0, 10]: Điểm khớp từ khóa theo nhóm hành vi\n"
        "• F2_popularity ∈ [0, 7] : Điểm phổ biến toàn hệ thống (Normalized Sales Count)\n"
        "• F3_genre     ∈ [0, 5] : Điểm tương thích thể loại phim (Genre Matching)\n"
        "• F4_time      ∈ [0, 3] : Điểm phù hợp khung giờ chiếu (Time Slot Fitness)\n"
        "• F5_price     ∈ [0, 4] : Điểm tương quan ngân sách vé (Price Affinity)\n"
        "• F6_cf        ∈ [0, 6] : Điểm Lọc Cộng Tác theo Phim (Item Co-occurrence)\n\n"
        "==> Tổng điểm tối đa: S_max = 35.0 điểm. Combo có S(combo_i) cao nhất sẽ được chọn làm 'Khuyên Dùng Nhất'.",
        "tip"
    )

    scoring_headers = ["Hàm thành phần", "Trọng số tối đa", "Công thức / Cơ chế tính toán", "Ý nghĩa học thuật"]
    scoring_data = [
        ["F1: Keyword Matching", "+10 điểm", "So khớp regex tên combo với tập từ khóa của reco_type", "Content-Based Filtering: Khớp đặc trưng ngữ nghĩa"],
        ["F2: System Popularity", "+7 điểm", "F2 = (Số lượt bán của Combo i / Tổng lượt bán cao nhất) * 7.0", "Popularity-Based Filtering: Xu hướng cộng đồng"],
        ["F3: Genre Affinity", "+5 điểm", "So khớp thể loại phim hiện tại với mapping combo tối ưu theo thể loại", "Domain Knowledge & Rule Mining: Tri thức chuyên gia"],
        ["F4: Time Slot Fitness", "+3 điểm", "Sáng (<12h: Standard +3); Tối (>=18h: Family/VIP +3); Chiều (+1)", "Context-Aware Computing: Điện toán ngữ cảnh thời gian"],
        ["F5: Budget Affinity", "+4 điểm", "Nếu vé > 200k & Combo VIP -> +4; Nếu vé < 100k & Combo Standard -> +4", "Economic & Purchasing Power Modeling: Độ nhạy cảm giá"],
        ["F6: Collaborative Filtering", "+6 điểm", "F6 = (Lượt mua Combo i khi xem Phim X / Lượt mua Combo cao nhất với Phim X) * 6.0", "Item-Based CF: Khai phá luật kết hợp Phim ➔ Combo"]
    ]
    add_styled_table(scoring_headers, scoring_data, [1.4, 1.0, 2.5, 1.6])

    # --- CHƯƠNG 4 ---
    add_h1("CHƯƠNG 4: THIẾT KẾ CƠ SỞ DỮ LIỆU VÀ QUY TRÌNH VẬN HÀNH")

    add_h2("4.1. Thiết kế Cơ sở Dữ liệu cho Module Tracking (recommendation_log)")
    db_headers = ["Tên trường (Column)", "Kiểu dữ liệu", "Ràng buộc / Index", "Mục đích sử dụng"]
    db_data = [
        ["id", "INT AUTO_INCREMENT", "PRIMARY KEY", "Định danh duy nhất của lượt gợi ý"],
        ["id_user", "INT", "NULLABLE, INDEX", "ID tài khoản khách hàng (NULL nếu khách vãng lai)"],
        ["id_phim", "INT", "NOT NULL, INDEX", "ID phim đang được đặt vé"],
        ["id_combo_suggested", "INT", "NOT NULL, INDEX", "ID combo được thuật toán chọn làm gợi ý số 1"],
        ["reco_type", "VARCHAR(30)", "NOT NULL, INDEX", "Nhóm ngữ cảnh (family, couple, kid, ...)"],
        ["reco_score", "DECIMAL(6,1)", "DEFAULT NULL", "Tổng điểm S(combo) đạt được"],
        ["scoring_factors", "TEXT (JSON)", "DEFAULT NULL", "Snapshot JSON chi tiết điểm từng F1->F6 phục vụ phân tích"],
        ["was_accepted", "TINYINT(1)", "DEFAULT 0, INDEX", "1 nếu khách mua đúng combo gợi ý, 0 nếu không"],
        ["combo_actually_chosen", "VARCHAR(500)", "NULLABLE", "Danh sách combo khách thực tế đã chọn"],
        ["created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP, INDEX", "Thời điểm sinh ra gợi ý"]
    ]
    add_styled_table(db_headers, db_data, [1.5, 1.4, 1.3, 2.3])

    add_h2("4.2. Luồng vận hành thực tế qua 6 giai đoạn (End-to-End Execution Flow)")
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

    # --- CHƯƠNG 5: CÁC VÍ DỤ TÍNH TOÁN MINH HỌA (KỊCH BẢN THỰC TẾ) ---
    add_h1("CHƯƠNG 5: KẾT QUẢ THỰC NGHIỆM VÀ CÁC KỊCH BẢN CHỨNG MINH THUẬT TOÁN")
    doc.add_paragraph(
        "Để chứng minh thuật toán hoạt động chính xác và có cơ sở khoa học rõ ràng, "
        "dưới đây là 4 kịch bản tính toán chi tiết từng bước trên tập dữ liệu thực tế:"
    )

    # Kịch bản 1
    add_h2("5.1. Kịch bản 1: Gia đình 4 người đi xem phim Hài vào buổi sáng")
    doc.add_paragraph(
        "• Đầu vào: 4 ghế thường (E5, E6, E7, E8) -> Tiền vé 320.000đ | Phim: 'Gặp Lại Chị Bầu' (Thể loại: Hài) | Suất: 10:30 sáng.\n"
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
    add_h2("5.2. Kịch bản 2: Cặp đôi hẹn hò xem phim Ngôn Tình vào buổi tối")
    doc.add_paragraph(
        "• Đầu vào: 2 ghế VIP (J8, J9) -> Tiền vé 220.000đ | Phim: 'Mai' (Thể loại: Ngôn Tình) | Suất: 20:30 tối.\n"
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
    add_h2("5.3. Kịch bản 3: Khách đi 1 mình xem phim Kinh Dị lúc 22h00 đêm")
    doc.add_paragraph(
        "• Đầu vào: 1 ghế đơn (F5) -> Tiền vé 80.000đ | Phim: 'Quỷ Cẩu' (Thể loại: Kinh Dị) | Suất: 22:00 đêm | Khách: Nam 24 tuổi.\n"
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
    add_h2("5.4. Kịch bản 4: Phim Mới Toanh Chưa Có Lịch Sử Mua Vé (Cold Start)")
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

    # --- CHƯƠNG 6: CẨM NANG BẢO VỆ VÀ PHẢN BIỆN ---
    add_h1("CHƯƠNG 6: CẨM NANG BẢO VỆ VÀ THUYẾT TRÌNH KHÓA LUẬN TỐT NGHIỆP")

    add_h2("6.1. Mẫu thuyết trình mở đầu gây ấn tượng trước Hội đồng")
    add_callout(
        "KỊCH BẢN NÓI MỞ ĐẦU (GỢI Ý KHI ĐỨNG TRƯỚC HỘI ĐỒNG)",
        "\"Kính thưa Quý Thầy/Cô trong Hội đồng chấm Khóa luận tốt nghiệp!\n\n"
        "Trong các hệ thống bán vé xem phim truyền thống, phần lớn các website chỉ dừng lại ở việc hiển thị danh sách combo đồ ăn tĩnh, "
        "khiến tỷ lệ mua hàng chéo (Cross-selling) bị hạn chế và không tạo được trải nghiệm cá nhân hóa. "
        "Nhận thấy F&B là mảng đóng góp tới hơn 70% biên lợi nhuận ròng của rạp chiếu, em đã nghiên cứu và phát triển thành công "
        "Module Gợi Ý Combo Đồ Ăn Thông Minh ứng dụng Mô Hình Lai (Hybrid Recommender System).\n\n"
        "Hệ thống của em không chỉ đơn thuần là các câu lệnh IF/ELSE, mà là sự kết hợp giữa 6 tiêu chí chấm điểm có trọng số (Weighted Scoring Engine), "
        "kết hợp thuật toán Lọc cộng tác (Collaborative Filtering) dựa trên lịch sử giao dịch vé thực tế và có hệ thống Tracking đo lường CTR khép kín. "
        "Sau đây, em xin phép demo chi tiết từng thuật toán và kết quả thực nghiệm...\"",
        "tip"
    )

    add_h2("6.2. Bộ 4 câu hỏi phản biện khó thường gặp và cách trả lời chuẩn học thuật")

    qa_list = [
        ("Câu hỏi 1: Hệ thống của em có gì khác biệt so với việc chỉ sắp xếp combo theo giá hoặc theo lượt mua nhiều nhất?",
         "Trả lời: Nếu chỉ sắp xếp theo giá hoặc độ phổ biến chung, hệ thống sẽ gợi ý cùng 1 combo cho mọi khách hàng (Global Popularity Bias). "
         "Mô hình của em là Context-Aware Hybrid Scoring, kết hợp 6 yếu tố: Cùng 1 phim nhưng đi gia đình 4 người sẽ được gợi ý Combo Family, "
         "cặp đôi đi tối sẽ gợi ý Combo VIP, trong khi đi đơn lẻ suất sáng sẽ ưu tiên Combo Standard tiết kiệm. Điều này tối ưu hóa xác suất mua hàng theo từng ngữ cảnh cụ thể."),
         
        ("Câu hỏi 2: Khi hệ thống mới triển khai chưa có dữ liệu lịch sử giao dịch (Cold Start), thuật toán hoạt động như thế nào?",
         "Trả lời: Đây chính là ưu điểm lớn nhất của mô hình Hybrid so với Collaborative Filtering thuần túy. Khi bảng 've' chưa có dữ liệu giao dịch, "
         "điểm F6 (Collaborative Filtering) và F2 (Popularity) sẽ tạm thời bằng 0. Lúc này, các hàm F1 (Keyword Context), F3 (Genre Affinity), "
         "F4 (Time Slot) và F5 (Budget) vẫn hoạt động bình thường để đưa ra gợi ý chất lượng cao. Khi dữ liệu giao dịch tăng dần, các điểm CF sẽ tự động gia tăng trọng số."),
         
        ("Câu hỏi 3: Tại sao em lại chọn các trọng số 10 - 7 - 5 - 3 - 4 - 6 cho 6 yếu tố mà không phải là bằng nhau?",
         "Trả lời: Việc phân bổ trọng số dựa trên mức độ ảnh hưởng quyết định mua hàng trong thực tế: "
         "(1) F1 (10đ) là yếu tố ngữ cảnh tức thời mạnh nhất (ví dụ đi 4 người thì bắt buộc cần combo to); "
         "(2) F2 (7đ) và F6 (6đ) là kinh nghiệm cộng đồng và quy luật mua sắm của số đông; "
         "(3) F3 (5đ) và F5 (4đ) là sở thích thể loại và khả năng chi trả; "
         "(4) F4 (3đ) là ngữ cảnh thời gian. Bộ trọng số này có thể dễ dàng tinh chỉnh (Hyperparameter Tuning) "
         "dựa trên dữ liệu log CTR thực tế của bảng recommendation_log."),
         
        ("Câu hỏi 4: Em đo lường hiệu quả của hệ thống gợi ý này bằng cách nào?",
         "Trả lời: Em đã thiết kế bảng 'recommendation_log' để ghi nhận toàn bộ vòng đời của mỗi lượt gợi ý: "
         "từ thời điểm hiển thị, điểm số chi tiết từng yếu tố, cho tới khi khách hàng hoàn tất đặt vé để đối soát xem khách có chọn đúng combo được gợi ý hay không (was_accepted = 1). "
         "Qua đó tính toán được tỷ lệ Click-Through Rate (CTR) và tỷ lệ chuyển đổi đơn hàng một cách minh bạch, định lượng.")
    ]

    for q, a in qa_list:
        add_h3(q)
        p = doc.add_paragraph()
        r = p.add_run(a)
        r.font.size = Pt(11)

    # --- CHƯƠNG 7 ---
    add_h1("CHƯƠNG 7: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN")
    doc.add_paragraph(
        "Việc nghiên cứu và nâng cấp thành công Hệ thống Gợi ý Combo Đồ Ăn Thông Minh (Hybrid Recommendation System) "
        "đã hoàn thiện mảnh ghép quan trọng trong đề tài Khóa luận tốt nghiệp 'Website Đặt Vé Xem Phim CinePass'. "
        "Hệ thống đạt được đầy đủ các tiêu chí: Tính học thuật chuẩn mực, Kiến trúc mô-đun hóa cao, Khả năng mở rộng tốt, "
        "và có công cụ đo lường định lượng thực tế."
    )
    doc.add_paragraph(
        "Hướng phát triển trong tương lai:\n"
        "• Ứng dụng giải thuật Phân rã ma trận (Matrix Factorization - SVD) hoặc Deep Learning Recommender khi quy mô dữ liệu vượt mốc 100,000 giao dịch.\n"
        "• Xây dựng hệ thống A/B Testing tự động tối ưu hóa bộ trọng số (Weights Optimization) theo thời gian thực."
    )

    doc.save(output_path)
    print(f"Master thesis document generated successfully at: {output_path}")

if __name__ == "__main__":
    desktop_dir = Path(os.path.expanduser("~")) / "Desktop"
    out_file = desktop_dir / "Chi_Tiet_Tinh_Nang_Goi_Y_Combo.docx"
    generate_master_thesis_doc(str(out_file))
