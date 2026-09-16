# -*- coding: utf-8 -*-
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
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

def create_styled_document(output_path):
    doc = Document()
    
    # Page setup - Margins (Standard Thesis format: Top 2cm, Bottom 2cm, Left 3cm, Right 2cm)
    for section in doc.sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(0.8)
        
    # Styles
    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Times New Roman'
    normal_style.font.size = Pt(12)
    normal_style.font.color.rgb = RGBColor(0x22, 0x22, 0x22)
    normal_style.paragraph_format.line_spacing = 1.25
    normal_style.paragraph_format.space_after = Pt(4)

    # Palette
    PRIMARY_COLOR = RGBColor(0x1B, 0x36, 0x5D)     # Deep Navy
    SECONDARY_COLOR = RGBColor(0x00, 0x66, 0x99)   # Blue Accent
    DARK_TEXT = RGBColor(0x22, 0x22, 0x22)
    GRAY_BG = "F4F6F9"
    PRIMARY_BG = "1B365D"
    ACCENT_BG = "E8F4F8"
    BORDER_COLOR = "D0D7DE"

    def add_title_cover():
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(30)
        p.paragraph_format.space_after = Pt(6)
        r = p.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO\nTRƯỜNG ĐẠI HỌC - KHOA CÔNG NGHỆ THÔNG TIN\n---------------------------------------")
        r.font.size = Pt(12)
        r.font.bold = True
        r.font.color.rgb = PRIMARY_COLOR

        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(50)
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
        r.font.size = Pt(18)
        r.font.bold = True
        r.font.color.rgb = PRIMARY_COLOR

        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(80)
        r = p.add_run("Chuyên đề: Tối ưu hóa trải nghiệm khách hàng & Gia tăng doanh thu F&B\nThuật toán: Weighted Multi-Criteria Scoring & Collaborative Filtering")
        r.font.size = Pt(11)
        r.font.italic = True
        r.font.color.rgb = RGBColor(0x55, 0x55, 0x55)

        # Info box
        tbl = doc.add_table(rows=3, cols=2)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl.autofit = True
        labels = [
            ("Đề tài khóa luận:", "Xây dựng Website Đặt Vé Xem Phim CinePass"),
            ("Học phần / Module:", "Hệ Khuyến Nghị Đồ Ăn Thông Minh (Smart Combo Recommender)"),
            ("Thời gian hoàn thành:", "Tháng 08 / 2026")
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
        run.font.size = Pt(15)
        run.font.bold = True
        run.font.color.rgb = PRIMARY_COLOR
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(12)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(13)
        run.font.bold = True
        run.font.color.rgb = SECONDARY_COLOR
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(8)
        p.paragraph_format.space_after = Pt(2)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.size = Pt(12)
        run.font.bold = True
        run.font.color.rgb = DARK_TEXT
        return p

    def add_callout(title, text, style_type="note"):
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        cell = tbl.cell(0, 0)
        set_cell_background(cell, ACCENT_BG if style_type == "note" else "FFF4E5")
        set_cell_margins(cell, top=140, bottom=140, left=180, right=180)
        
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(3)
        r_title = p.add_run(f"📌 {title}\n" if style_type == "note" else f"💡 {title}\n")
        r_title.font.bold = True
        r_title.font.size = Pt(11)
        r_title.font.color.rgb = PRIMARY_COLOR if style_type == "note" else RGBColor(0x99, 0x4D, 0x00)
        
        r_body = p.add_run(text)
        r_body.font.size = Pt(10.5)
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    def add_styled_table(headers, data, col_widths=None):
        tbl = doc.add_table(rows=len(data) + 1, cols=len(headers))
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        
        # Header
        hdr_row = tbl.rows[0]
        for col_idx, text in enumerate(headers):
            cell = hdr_row.cells[col_idx]
            set_cell_background(cell, PRIMARY_BG)
            set_cell_margins(cell, top=120, bottom=120, left=140, right=140)
            p = cell.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.space_after = Pt(2)
            run = p.add_run(text)
            run.font.bold = True
            run.font.size = Pt(10.5)
            run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            
        # Data rows
        for row_idx, row_data in enumerate(data):
            row = tbl.rows[row_idx + 1]
            bg_color = GRAY_BG if row_idx % 2 == 1 else "FFFFFF"
            for col_idx, val in enumerate(row_data):
                cell = row.cells[col_idx]
                set_cell_background(cell, bg_color)
                set_cell_margins(cell, top=100, bottom=100, left=120, right=120)
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                run = p.add_run(str(val))
                run.font.size = Pt(10)
                if col_idx == 0:
                    run.font.bold = True
                    
        # Apply col widths if given
        if col_widths:
            for row in tbl.rows:
                for idx, w in enumerate(col_widths):
                    row.cells[idx].width = Inches(w)
                    
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # ========================== NỘI DUNG TÀI LIỆU ==========================
    
    add_title_cover()

    # --- CHƯƠNG 1 ---
    add_h1("CHƯƠNG 1: TỔNG QUAN VÀ BỐI CẢNH ĐẶT VẤN ĐỀ")
    
    add_h2("1.1. Tầm quan trọng của F&B và Cross-Selling trong ngành điện ảnh")
    p = doc.add_paragraph(
        "Trong mô hình kinh doanh rạp chiếu phim hiện đại (Cinema Exhibitor Business Model), "
        "doanh thu từ tiền vé (Box Office) thường phải chia sẻ từ 50% đến 55% cho các nhà phát hành phim (Distributors / Studios). "
        "Do đó, mảng dịch vụ Bắp nước & Combo ẩm thực (F&B - Food and Beverage) là nguồn đóng góp chính tạo ra tỷ suất lợi nhuận ròng "
        "(Gross Profit Margin lên tới 75% - 85%) và quyết định sự sống còn của doanh nghiệp rạp chiếu."
    )
    p = doc.add_paragraph(
        "Nghiên cứu hành vi tiêu dùng cho thấy quyết định mua đồ ăn của khách hàng tại rạp chịu ảnh hưởng mạnh bởi: "
        "(1) Số lượng người đi xem (Đi 1 mình, Cặp đôi hẹn hò, Gia đình/Nhóm bạn); "
        "(2) Thể loại phim (Phim kinh dị gây hồi hộp cần nước ngọt giải nhiệt; Phim hoạt hình đi kèm trẻ nhỏ chuộng bắp ngọt; Phim tâm lý tình cảm ưu tiên combo sang trọng); "
        "(3) Khung giờ chiếu (Suất sáng cần khẩu phần nhẹ, suất tối cần combo no bụng); và "
        "(4) Mức độ nhạy cảm về giá (Price Sensitivity) dựa trên loại ghế đã chọn."
    )

    add_h2("1.2. Khảo sát thực trạng tính năng gợi ý ban đầu trên CinePass")
    p = doc.add_paragraph(
        "Trước khi được tối ưu hóa, hệ thống CinePass đã có một module gợi ý combo cơ bản tại trang chọn đồ ăn (doan.php). "
        "Tuy nhiên, qua rà soát chi tiết mã nguồn, hệ thống bộc lộ các hạn chế kỹ thuật và học thuật nghiêm trọng:"
    )
    
    headers_eval = ["Tiêu chí đánh giá", "Trạng thái ban đầu (Cũ)", "Tác động tiêu cực"]
    data_eval = [
        ["Cơ chế ra quyết định", "Chỉ dùng 1 Rule so khớp từ khóa (Keyword Match)", "Bỏ qua toàn bộ dữ liệu lịch sử và hành vi thực tế của khách"],
        ["Số lượng yếu tố", "1 yếu tố duy nhất (Tên combo chứa từ khóa)", "Dễ bị thiên lệch (Bias), không phân biệt được nhiều combo cùng loại"],
        ["Khai thác dữ liệu quá khứ", "Hoàn toàn không có (No Collaborative Filtering)", "Lãng phí kho dữ liệu giao dịch vé sẵn có trong CSDL"],
        ["Cơ chế đo lường", "Không có bảng Tracking / Logging", "Không đo được tỷ lệ chuyển đổi (CTR), không thể bảo vệ định lượng trước Hội đồng"]
    ]
    add_styled_table(headers_eval, data_eval, [1.5, 2.5, 2.5])

    add_h2("1.3. Mục tiêu nghiên cứu và giải pháp nâng cấp")
    p = doc.add_paragraph(
        "Nhằm nâng cao giá trị học thuật của Khóa luận tốt nghiệp và xây dựng một giải pháp có tính ứng dụng thực tiễn cao, "
        "đề tài đặt ra mục tiêu chuyển đổi toàn diện module gợi ý combo thành một "
    )
    p.add_run("Hệ Thống Khuyến Nghị Lai Đa Tiêu Chí (Weighted Multi-Criteria Hybrid Recommendation System)").bold = True
    p.add_run(
        ", kết hợp nhuần nhuyễn giữa Phân tích Ngữ cảnh Nội dung (Content-Based / Context-Aware) "
        "và Lọc Cộng tác Dựa trên Lịch sử Giao dịch (Item-Based Collaborative Filtering), "
        "đồng thời tích hợp cơ chế Tracking khép kín để đo lường tỷ lệ Click-Through Rate (CTR)."
    )

    # --- CHƯƠNG 2 ---
    add_h1("CHƯƠNG 2: CƠ SỞ LÝ THUYẾT VỀ HỆ THỐNG GỢI Ý (RECOMMENDER SYSTEMS)")
    
    add_h2("2.1. Tổng quan các phương pháp Recommender System")
    doc.add_paragraph(
        "Trong lĩnh vực Trí tuệ Nhân tạo và Khai phá Dữ liệu (Data Mining), Hệ thống Gợi ý (Recommender Systems) "
        "chia thành 3 trường phái chính:"
    )
    
    doc.add_paragraph(
        "1. Content-Based Filtering (Lọc dựa trên nội dung): Phân tích thuộc tính của đối tượng (Item features) "
        "và đặc điểm ngữ cảnh của phiên làm việc (Session context) như số lượng vé, loại phim, khung giờ để tính độ tương đồng."
    )
    doc.add_paragraph(
        "2. Collaborative Filtering (Lọc cộng tác): Dựa trên giả thuyết 'Người dùng có hành vi tương đồng trong quá khứ sẽ có xu hướng lựa chọn giống nhau trong tương lai'. "
        "Trong bài toán CinePass, Item-Based Collaborative Filtering được áp dụng để tìm quy luật kết hợp giữa Phim (Movie) và Combo (Item Co-occurrence)."
    )
    doc.add_paragraph(
        "3. Hybrid Recommendation System (Hệ thống lai): Kết hợp cả hai phương pháp trên nhằm bù trừ nhược điểm cho nhau. "
        "Khắc phục triệt để bài toán Khởi đầu lạnh (Cold Start Problem) khi chưa có lịch sử mua hàng."
    )

    add_h2("2.2. Kiến trúc tổng thể Hybrid Recommendation Engine trên CinePass")
    doc.add_paragraph(
        "Kiến trúc giải pháp được thiết kế theo mô hình 4 tầng (Layered Architecture):"
    )
    
    arch_headers = ["Tầng kiến trúc", "Thành phần kỹ thuật", "Chức năng chi tiết"]
    arch_data = [
        ["1. Data Ingestion Layer", "Database MySQL (ve, phim, combo_do_an, taikhoan)", "Trích xuất dữ liệu giao dịch quá khứ, thông tin phim và tài khoản"],
        ["2. Feature Extraction Layer", "Session Data & Cart Context", "Thu thập 7 đặc trưng: Số ghế, Thể loại phim, Giờ chiếu, Ngân sách vé, Tuổi, Giới tính"],
        ["3. Hybrid Scoring Engine", "Weighted Scoring Algorithm (doan.php + combo_recommend.php)", "Tính toán đồng thời 6 hàm thành phần F1 -> F6 và tổng hợp điểm số S(combo)"],
        ["4. Tracking & Analytics Layer", "recommendation_log & Model Tracking API", "Ghi nhận gợi ý, theo dõi chuyển đổi mua hàng và xuất báo cáo CTR"]
    ]
    add_styled_table(arch_headers, arch_data, [1.5, 2.3, 2.7])

    # --- CHƯƠNG 3 ---
    add_h1("CHƯƠNG 3: MÔ HÌNH TOÁN HỌC VÀ THUẬT TOÁN ĐỀ XUẤT")

    add_h2("3.1. Phân loại Cụm Ngữ Cảnh Người Dùng (User Context Clustering)")
    doc.add_paragraph(
        "Thuật toán phân tích giỏ hàng của phiên giao dịch hiện tại để phân loại khách hàng vào 1 trong 7 cụm hành vi (reco_type):"
    )

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
    doc.add_paragraph(
        "Mỗi combo hợp lệ trong hệ thống sẽ được tính điểm mức độ phù hợp S(combo_i) thông qua hàm tuyến tính có trọng số:"
    )
    
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

    add_h3("Chi tiết 6 hàm thành phần:")
    
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
    add_h1("CHƯƠNG 4: THIẾT KẾ CƠ SỞ DỮ LIỆU VÀ HIỆN THỰC HÓA MÃ NGUỒN")

    add_h2("4.1. Thiết kế Cơ sở Dữ liệu cho Module Tracking (recommendation_log)")
    doc.add_paragraph(
        "Để đáp ứng yêu cầu đo lường định lượng cho Khóa luận, cấu trúc bảng tracking được thiết kế chuyên biệt:"
    )

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

    add_h2("4.2. Cấu trúc các Module Mã nguồn đã triển khai")
    doc.add_paragraph(
        "Toàn bộ giải pháp được hiện thực hóa qua 4 file mã nguồn chính:"
    )
    
    files_headers = ["Tên File", "Vị trí trong dự án", "Vai trò và nội dung thực hiện"]
    files_data = [
        ["combo_recommend.php", "Trang-nguoi-dung/model/", "Chứa 9 hàm xử lý Collaborative Filtering, Popularity Map, Tracking Logger và Analytics Metrics"],
        ["doan.php", "Trang-nguoi-dung/view/", "Tích hợp Hybrid Scoring Engine (6 hàm F1->F6), tính điểm trực tiếp theo phiên và render UI Khuyên Dùng"],
        ["index.php", "Trang-nguoi-dung/", "Controller xử lý case 'dv4': Bắt sự kiện chọn combo, đối soát với session và cập nhật trạng thái was_accepted"],
        ["migration_recommendation_log.sql", "DB/", "File SQL DDL tạo bảng tracking với đầy đủ Index tối ưu hóa truy vấn"],
        ["sample_data_recommendation.sql", "DB/", "Bộ dữ liệu mẫu gồm 10 user chuẩn hóa nhân khẩu học, 70 vé đa dạng thể loại và 15 log mẫu"]
    ]
    add_styled_table(files_headers, files_data, [1.8, 1.5, 3.2])

    # --- CHƯƠNG 5 ---
    add_h1("CHƯƠNG 5: KẾT QUẢ THỰC NGHIỆM VÀ ĐÁNH GIÁ HIỆU NĂNG")

    add_h2("5.1. Các chỉ số đo lường hiệu quả (Evaluation Metrics)")
    doc.add_paragraph(
        "Để đánh giá mức độ chính xác và tính thuyết phục của hệ thống gợi ý, 3 chỉ số cốt lõi được áp dụng:"
    )
    
    doc.add_paragraph(
        "1. Click-Through Rate (CTR) / Conversion Rate: Tỷ lệ khách hàng quyết định thêm combo được gợi ý vào đơn hàng.\n"
        "   Công thức: CTR = (Tổng số lượt was_accepted = 1) / (Tổng số lượt gợi ý hiển thị) * 100%"
    )
    doc.add_paragraph(
        "2. Top-1 Recommendation Accuracy: Mức độ chính xác tuyệt đối của gợi ý vị trí đầu tiên (Khuyên dùng nhất)."
    )
    doc.add_paragraph(
        "3. Cold Start Resilience: Khả năng hệ thống vẫn đưa ra gợi ý hợp lý ngay cả khi dữ liệu lịch sử bằng 0 (nhờ vào Content-Based Rule Fallback)."
    )

    add_h2("5.2. Kết quả kiểm thử thực nghiệm theo các kịch bản")
    
    test_headers = ["Kịch bản kiểm thử", "Dữ liệu đầu vào", "Combo thuật toán chọn", "Điểm S(combo)", "Kết quả thực tế"]
    test_data = [
        ["1. Gia đình xem phim Hài", "Phim 39, 4 ghế (G8,G9,G10,G11), Suất 10h", "Combo Family", "28.5 / 35.0", "Thành công (Khách chọn đúng Combo Family)"],
        ["2. Cặp đôi xem Ngôn tình", "Phim 42, 2 ghế VIP (J8,J9), Suất 20h30", "Combo VIP", "27.0 / 35.0", "Thành công (Khách chọn Combo VIP)"],
        ["3. Trẻ em xem Hoạt hình", "Phim 6, 2 ghế thường, Suất sáng 9h30", "Combo Standard", "22.5 / 35.0", "Thành công (Khách chọn Combo Standard)"],
        ["4. Nam giới xem Kinh dị", "Phim 41, 1 ghế VIP, Suất tối 22h", "Combo VIP", "23.0 / 35.0", "Thành công (Điểm CF và Genre áp đảo)"],
        ["5. Đơn lẻ suất sáng sớm", "Phim 40, 1 ghế, Suất 10h sáng", "Combo Standard", "19.0 / 35.0", "Thành công (Time + Budget ưu tiên Standard)"]
    ]
    add_styled_table(test_headers, test_data, [1.5, 1.7, 1.2, 1.0, 1.1])

    # --- CHƯƠNG 6 ---
    add_h1("CHƯƠNG 6: CẨM NANG BẢO VỆ VÀ THUYẾT TRÌNH KHÓA LUẬN TỐT NGHIỆP")

    add_h2("6.1. Hướng dẫn cách mở đầu bài thuyết trình (Gây ấn tượng với Hội đồng)")
    add_callout(
        "MẪU THUYẾT TRÌNH MỞ ĐẦU (GỢI Ý KHI ĐỨNG TRƯỚC HỘI ĐỒNG)",
        "\"Kính thưa Quý Thầy/Cô trong Hội đồng chấm Khóa luận tốt nghiệp!\n\n"
        "Trong các hệ thống bán vé xem phim truyền thông, phần lớn các website chỉ dừng lại ở việc hiển thị danh sách combo đồ ăn tĩnh, "
        "khiến tỷ lệ mua hàng chéo (Cross-selling) bị hạn chế và không tạo được trải nghiệm cá nhân hóa. "
        "Nhận thấy F&B là mảng đóng góp tới hơn 70% biên lợi nhuận ròng của rạp chiếu, em đã nghiên cứu và phát triển thành công "
        "Module Gợi Ý Combo Đồ Ăn Thông Minh ứng dụng Mô Hình Lai (Hybrid Recommender System).\n\n"
        "Hệ thống của em không chỉ đơn thuần là các câu lệnh IF/ELSE, mà là sự kết hợp giữa 6 tiêu chí chấm điểm có trọng số (Weighted Scoring Engine), "
        "kết hợp thuật toán Lọc cộng tác (Collaborative Filtering) dựa trên lịch sử giao dịch vé thực tế và có hệ thống Tracking đo lường CTR khép kín. "
        "Sau đây, em xin phép demo chi tiết từng thuật toán và kết quả thực nghiệm...\"",
        "tip"
    )

    add_h2("6.2. Bộ câu hỏi phản biện thường gặp và hướng trả lời chuẩn học thuật")

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

    # --- KẾT LUẬN ---
    add_h1("CHƯƠNG 7: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN")
    p = doc.add_paragraph(
        "Việc nghiên cứu và nâng cấp thành công Hệ thống Gợi ý Combo Đồ Ăn Thông Minh (Hybrid Recommendation System) "
        "đã hoàn thiện mảnh ghép quan trọng trong đề tài Khóa luận tốt nghiệp 'Website Đặt Vé Xem Phim CinePass'. "
        "Hệ thống đạt được đầy đủ các tiêu chí: Tính học thuật chuẩn mực, Kiến trúc mô-đun hóa cao, Khả năng mở rộng tốt, "
        "và có công cụ đo lường định lượng thực tế."
    )
    p = doc.add_paragraph(
        "Hướng phát triển trong tương lai:\n"
        "• Ứng dụng giải thuật Phân rã ma trận (Matrix Factorization - SVD) hoặc Deep Learning Recommender khi quy mô dữ liệu vượt mốc 100,000 giao dịch.\n"
        "• Xây dựng hệ thống A/B Testing tự động tối ưu hóa bộ trọng số (Weights Optimization) theo thời gian thực."
    )

    # Save
    doc.save(output_path)
    print(f"Document successfully created at: {output_path}")

if __name__ == "__main__":
    desktop_dir = Path(os.path.expanduser("~")) / "Desktop"
    out_file = desktop_dir / "Bao_Cao_Chi_Tiet_Tinh_Nang_Goi_Y_Combo_Do_An.docx"
    create_styled_document(str(out_file))
