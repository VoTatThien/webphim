import random
import os

the_loai_list = ['Hoạt hình', 'Hài', 'Kinh Dị', 'Ngôn Tình', 'Cổ Trang', 'Ca nhạc', 'Hành động', 'Phiêu lưu', 'Khoa học viễn tưởng', 'Tình cảm']
gio_chieu_list = ['sang', 'chieu', 'toi']
khoang_tuoi_list = ['duoi_18', '18_25', '26_35', '36_45', 'tren_45']
gioi_tinh_list = ['nam', 'nu']
thu_trong_tuan_list = ['weekday', 'weekend']
so_ghe_list = ['1', '2', '3+']
combo_list = ['Combo Kid', 'Combo Healthy', 'Combo Couple', 'Combo Family', 'Combo Solo King', 'Combo Sweet Girl', 'Combo Standard', 'Bắp rang bơ']

def generate_row():
    # randomly select features
    the_loai = random.choice(the_loai_list)
    gio_chieu = random.choices(gio_chieu_list, weights=[0.25, 0.35, 0.40])[0]
    khoang_tuoi = random.choices(khoang_tuoi_list, weights=[0.15, 0.35, 0.25, 0.15, 0.10])[0]
    gioi_tinh = random.choices(gioi_tinh_list, weights=[0.50, 0.50])[0]
    thu_trong_tuan = random.choices(thu_trong_tuan_list, weights=[0.60, 0.40])[0]
    so_ghe = random.choices(so_ghe_list, weights=[0.30, 0.45, 0.25])[0]
    
    # --- QUY LUẬT TÂM LÝ & KIẾN THỨC ĐỜI SỐNG (LIFE-KNOWLEDGE RULES) ---
    
    # 1. Khách hàng lớn tuổi (tren_45): Kiêng ngọt, tránh nước có gas, chọn Combo Healthy (nước suối/nước lọc)
    if khoang_tuoi == 'tren_45':
        combo = random.choices(['Combo Healthy', 'Combo Standard'], weights=[0.90, 0.10])[0]
        
    # 2. Khách hàng nhỏ tuổi / Học sinh (duoi_18): Bắp size nhỏ, nước hoa quả/sữa, tránh lãng phí -> Combo Kid
    elif khoang_tuoi == 'duoi_18':
        combo = random.choices(['Combo Kid', 'Bắp rang bơ'], weights=[0.90, 0.10])[0]
        
    # 3. Khách đi theo nhóm / Gia đình (3+ ghế): Tiết kiệm, khẩu phần lớn cho nhiều người -> Combo Family
    elif so_ghe == '3+':
        combo = random.choices(['Combo Family', 'Combo Standard'], weights=[0.90, 0.10])[0]
        
    # 4. Khách đi 2 người (Cặp đôi / Hẹn hò): Bắp 2 vị chia sẻ + 2 ly nước riêng -> Combo Couple
    elif so_ghe == '2':
        combo = random.choices(['Combo Couple', 'Combo Family'], weights=[0.90, 0.10])[0]
        
    # 5. Khách đi 1 mình (1 ghế) trong độ tuổi thanh niên/trung niên:
    else:
        # Nam giới (18-35 tuổi): Sức ăn lớn, xem hành động/kinh dị hoặc xem ca tối -> Combo Solo King (kèm hotdog)
        if gioi_tinh == 'nam':
            if the_loai in ['Kinh Dị', 'Hành động', 'Khoa học viễn tưởng'] or (gio_chieu == 'toi' and khoang_tuoi in ['18_25', '26_35']):
                combo = random.choices(['Combo Solo King', 'Combo Standard'], weights=[0.88, 0.12])[0]
            else:
                combo = random.choices(['Combo Standard', 'Combo Solo King'], weights=[0.85, 0.15])[0]
        # Nữ giới (18-35 tuổi): Thích bắp phô mai/caramel ngọt béo vừa vặn -> Combo Sweet Girl
        else:
            if khoang_tuoi in ['18_25', '26_35']:
                combo = random.choices(['Combo Sweet Girl', 'Bắp rang bơ'], weights=[0.88, 0.12])[0]
            else:
                combo = random.choices(['Combo Standard', 'Combo Sweet Girl'], weights=[0.85, 0.15])[0]
            
    return f"('{the_loai}', '{gio_chieu}', '{khoang_tuoi}', '{gioi_tinh}', '{thu_trong_tuan}', '{so_ghe}', '{combo}')"

rows = [generate_row() for _ in range(1000)]

out_file = r'd:\KHOALUAN\BaoCaoKLTN_SV_HD\KHOALUAN_HUNG\webphim_hung\DB\training_data_1000.sql'
os.makedirs(os.path.dirname(out_file), exist_ok=True)

with open(out_file, 'w', encoding='utf-8') as f:
    f.write("-- Dữ liệu huấn luyện mẫu (1000 dòng)\n")
    
    for i in range(0, 1000, 50):
        batch = rows[i:i+50]
        f.write(f"-- Batch {i//50 + 1}: Mô phỏng phân phối đa dạng các nhóm khán giả\n")
        f.write("INSERT INTO `combo_training_data` (`the_loai`, `gio_chieu`, `khoang_tuoi`, `gioi_tinh`, `thu_trong_tuan`, `so_ghe`, `combo_chon`) VALUES\n")
        f.write(",\n".join(batch))
        f.write(";\n\n")
