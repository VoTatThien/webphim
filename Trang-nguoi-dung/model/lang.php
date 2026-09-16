<?php

function init_language() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_GET['lang'])) {
        $lang = strtolower($_GET['lang']);
        if (in_array($lang, ['vi', 'en'])) {
            $_SESSION['lang'] = $lang;
        }
    }
    
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang'] = 'vi'; // Mặc định Tiếng Việt
    }
}

function get_current_lang() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
}

function get_lang_url($lang) {
    $params = $_GET;
    $params['lang'] = $lang;
    return 'index.php?' . http_build_query($params);
}

function __($text) {
    static $translations = null;
    if ($translations === null) {
        $translations = [
            // Menu chính
            'Trang chủ' => 'Home',
            'Phim' => 'Movies',
            'Tất cả Phim' => 'All Movies',
            'Thể loại' => 'Genres',
            'Rạp chiếu' => 'Cinemas',
            'Khuyến mãi' => 'Promotions',
            'Liên hệ' => 'Contact',
            'Tin tức' => 'News',
            
            // Dropdown người dùng & Đăng nhập
            'Đăng nhập' => 'Login',
            'Đăng xuất' => 'Logout',
            'Lịch sử điểm' => 'Points History',
            'Vé của tôi' => 'My Tickets',
            'Thông tin cá nhân' => 'Personal Info',
            
            // Trang chủ & Heading
            'Phim hot nhất' => 'Trending Movies',
            'Phim mới nhất' => 'Latest Movies',
            'Chi tiết' => 'Details',
            'KIỂM TRA TẤT CẢ CÁC PHIM ĐANG CHIẾU' => 'CHECK ALL CURRENT SHOWING MOVIES',
            'Thời Lượng Phim' => 'Duration',
            'Thể Loại' => 'Genre',
            'phút' => 'mins',
            
            // Dịch Động: Thể loại
            'Kinh Dị' => 'Horror',
            'Ngôn Tình' => 'Romance',
            'Hài' => 'Comedy',
            'Ca nhạc' => 'Musical',
            'Cổ Trang' => 'Historical',
            'Hoạt Hình' => 'Animation',
            
            // Dịch Động: Tên Rạp
            'Galaxy Studio Quận 1' => 'Galaxy Studio District 1',
            'Galaxy Studio Quận 7' => 'Galaxy Studio District 7',
            'Galaxy Studio Quận 2' => 'Galaxy Studio District 2',
            'Galaxy Studio Bình Dương' => 'Galaxy Studio Binh Duong',
            'Galaxy Studio Đồng Nai' => 'Galaxy Studio Dong Nai',
            'Galaxy Tuy Hòa' => 'Galaxy Tuy Hoa',
            
            // Dịch Động: Tên Phim
            'Thỏ ơi!' => 'Oh Rabbit!',
            'Nhà ba tôi một phòng' => 'My Dad\'s Single Room',
            'Quỷ Nhập Tràng 2' => 'The Reanimated Coffin 2',
            'Tài' => 'Tai',
            'Kỳ án trên băng' => 'Anatomy of a Fall',
            'Búp bê' => 'Five Nights at Freddy\'s',
            'Đất rừng phương nam' => 'Song of the South',
            'THE MARVELS' => 'THE MARVELS',
            'QUỶ LÙN TINH NGHỊCH: ĐỒNG TÂM HIỆP NHẠC' => 'TROLLS BAND TOGETHER',
            'CHÚA TỂ CỦA NHỮNG CHIẾC NHẪN - SỰ TRỞ VỀ CỦA NHÀ VUA ' => 'THE LORD OF THE RINGS: THE RETURN OF THE KING',
            'YÊU LẠI VỢ NGẦU' => 'LOVE RESET',
            'KẺ ĂN HỒN' => 'THE SOUL REAPER',
            'NGƯỜI VỢ CUỐI CÙNG' => 'THE LAST WIFE',
            'THIẾU NIÊN VÀ CHIM DIỆC' => 'THE BOY AND THE HERON',
            'Bảy Viên Ngọc Rồng Siêu Cấp: Siêu Anh Hùng' => 'Dragon Ball Super: Super Hero',
            'NHỮNG KỶ NGUYÊN CỦA TAYLOR SWIFT' => 'TAYLOR SWIFT: THE ERAS TOUR',
            'WONKA' => 'WONKA',
            'NGƯỜI MẶT TRỜI' => 'THE SUN MAN',
            'ĐỊA ĐẠO' => 'THE TUNNEL',
            'Lật mặt' => 'Face Off',
            'PHIM IUH OK' => 'PHIM IUH OK',

            // === DỊCH BẢNG ĐIỀU KHIỂN QUẢN TRỊ (ADMIN PANEL) ===
            'Trang Chủ' => 'Dashboard',
            'Tổng doanh thu' => 'Total Revenue',
            'Tổng Doanh Thu' => 'Total Revenue',
            'Tổng vé đã bán' => 'Total Tickets Sold',
            'Tổng doanh thu hôm nay' => 'Today\'s Revenue',
            'Doanh Thu Hôm Nay' => 'Today\'s Revenue',
            'Tổng doanh thu tuần này' => 'Weekly Revenue',
            'Doanh Thu Tuần Này' => 'Weekly Revenue',
            'Tổng doanh thu tháng này' => 'Monthly Revenue',
            'Doanh Thu Tháng Này' => 'Monthly Revenue',
            'Tổng Phim Đang Chiếu' => 'Total Showing Movies',
            'Phim Đang Chiếu' => 'Showing Movies',
            'Tổng Phim Sắp Chiếu' => 'Total Upcoming Movies',
            'Phim Sắp Chiếu' => 'Upcoming Movies',
            'Combo được đặt nhiều nhất' => 'Most Ordered Combo',
            'Combo Top' => 'Top Combo',
            '📊 Biểu Đồ Doanh Thu 30 Ngày Qua' => '📊 Revenue Chart (Last 30 Days)',
            'Biểu Đồ Doanh Thu 30 Ngày Qua' => 'Revenue Chart (Last 30 Days)',
            '📊 Biểu đồ Doanh thu 30 Ngày Qua' => '📊 Revenue Chart (Last 30 Days)',
            'Biểu đồ Doanh thu 30 Ngày Qua' => 'Revenue Chart (Last 30 Days)',
            '🎬 Phân Bổ Phim Theo Trạng thái' => '🎬 Movie Status Distribution',
            '🎬 Phân Bổ Phim Theo Trạng Thái' => '🎬 Movie Status Distribution',
            'Phân Bổ Phim Theo Trạng thái' => 'Movie Status Distribution',
            'Phân Bổ Phim Theo Trạng Thái' => 'Movie Status Distribution',
            '💰 Doanh Thu Theo Loại Combo' => '💰 Revenue by Combo Type',
            'Doanh Thu Theo Loại Combo' => 'Revenue by Combo Type',
            'Chào mừng' => 'Welcome',
            'Người dùng' => 'User',
            'đến với trang làm việc của Galaxy Studio' => 'to Galaxy Studio\'s workspace',
            'Đang Chiếu' => 'Showing',
            'Sắp Chiếu' => 'Upcoming',
            'Kết Thúc' => 'Ended',
            'VNĐ' => 'VND',
            'VÉ' => 'TICKETS',
            'Phim' => 'Movies',
            'Đã có' => 'Already',
            'được đặt' => 'ordered',
            'Doanh Thu (Triệu VNĐ)' => 'Revenue (Million VND)',
            'Doanh Thu (VNĐ)' => 'Revenue (VND)',
            
            // Rap Dashboard
            'Dashboard Quản Lý Rạp' => 'Cinema Manager Dashboard',
            'Thời gian:' => 'Time:',
            'Từ ngày' => 'From Date',
            'Đến ngày' => 'To Date',
            'Tất cả phim' => 'All Movies',
            'Lọc' => 'Filter',
            'Đặt lại' => 'Reset',
            'Tổng Vé Bán' => 'Total Tickets Sold',
            'Bộ' => 'Movies',
            'đơn' => 'orders',
            'Không Combo' => 'No Combo',
            
            // Cum Dashboard
            'Dashboard Quản Lý Cụm' => 'Cluster Manager Dashboard',
            
            // Nhan Vien Dashboard
            'Dashboard Nhân Viên' => 'Employee Dashboard',
            'Vé Bán Hôm Nay' => 'Tickets Sold Today',
            'Phim Chiếu Hôm Nay' => 'Movies Showing Today',
            'Lịch Làm Hôm Nay' => 'Work Shift Today',
            'Có lịch' => 'Scheduled',
            'Không lịch' => 'No shift',
            'Lịch Làm Việc Hôm Nay' => 'Today\'s Work Schedule',
            'Ca làm:' => 'Shift:',
            'Ghi chú:' => 'Note:',
            'Hôm nay không có lịch làm việc' => 'No work shifts scheduled for today',
            'Lịch Làm Việc 7 Ngày Tới' => 'Work Schedule (Next 7 Days)',
            'Ca:' => 'Shift:',
            'Chưa có lịch trong 7 ngày tới' => 'No shifts scheduled for the next 7 days',
            'suất chiếu' => 'show(s)',
            'Hôm nay không có phim chiếu' => 'No movies showing today',
            'Lịch Chiếu Chi Tiết Hôm Nay' => 'Today\'s Detailed Showtimes',
            'Giờ Chiếu' => 'Showtime',
            'Phòng' => 'Room',
            'Hôm nay không có lịch chiếu' => 'No showtimes scheduled for today',
            'Vé Bán Theo Ngày (Tuần Này)' => 'Tickets Sold by Day (This Week)',
            'Số Vé' => 'Ticket Qty',
            'Doanh Thu' => 'Revenue',
            'Không có quyền truy cập!' => 'Access denied!',
            'Không thể xác định rạp của bạn!' => 'Unable to determine your cinema!',
            'Không thể xác định thông tin nhân viên!' => 'Unable to determine employee information!',

            'Quản Trị Galaxy Studio' => 'Galaxy Studio Administration',
            'Cấu hình website' => 'Website Settings',
            'Quản Lý Tài Khoản' => 'Account Management',
            'Thêm tài khoản' => 'Add Account',
            'Khách hàng' => 'Customers',
            'Nhân viên' => 'Employees',
            'Quản lý rạp' => 'Cinema Managers',
            'Quản lý cụm rạp' => 'Cluster Managers',
            'Thống Kê Doanh Thu' => 'Revenue Statistics',
            'Danh Thu Phim' => 'Movie Revenue',
            'Doanh Thu Phim' => 'Movie Revenue',
            'Theo Ngày' => 'By Day',
            'Theo Tuần' => 'By Week',
            'Theo Tháng' => 'By Month',
            'Quản Lý Rạp' => 'Cinema Management',
            'Danh sách rạp' => 'Cinema List',
            'Thêm rạp' => 'Add Cinema',
            'Quản Lý Loại Phim' => 'Genre Management',
            'Quản Lý Phim (Cụm)' => 'Movie Management (Cluster)',
            'Duyệt kế hoạch chiếu' => 'Approve Showtimes',
            'Lịch theo rạp' => 'Schedule by Cinema',
            'Phân phối phim' => 'Distribute Movies',
            'Tài khoản' => 'Accounts',
            'Thống Kê' => 'Statistics',
            'DT Phim theo Rạp' => 'Movie Revenue by Cinema',
            'Hiệu suất Rạp' => 'Cinema Performance',
            'Theo Rạp' => 'By Cinema',
            'Quản Lý Tin Tức' => 'News Management',
            'Lịch làm việc' => 'Work Schedule',
            'Dạng bảng' => 'Table View',
            'Calendar phân công' => 'Calendar Schedule',
            'Duyệt nghỉ phép' => 'Approve Leaves',
            'Lập kế hoạch chiếu phim' => 'Plan Movie Showtimes',
            'Quản Lý Phòng - Ghế' => 'Room & Seat Management',
            'Quản Lý Suất Chiếu' => 'Showtime Management',
            'Suất Chiếu' => 'Showtimes',
            'Khung Giờ' => 'Time Slots',
            'Ưu đãi' => 'Offers',
            'Combo' => 'Combos',
            'Nhân sự' => 'Staff & Attendance',
            'Chấm công' => 'Attendance',
            'Bảng lương' => 'Payroll',
            'Tổng quan rạp' => 'Cinema Overview',
            'Vé' => 'Tickets',
            'Quản lý vé' => 'Ticket Management',
            'Kiểm tra vé (QR)' => 'Verify Tickets (QR)',
            'Đổi/Hoàn vé' => 'Exchange/Refund Tickets',
            'Thiết bị phòng' => 'Room Equipment',
            'Bình luận/Feedback' => 'Comments/Feedback',
            'Đặt/Quản lý vé' => 'Book/Manage Tickets',
            'Đặt vé cho khách' => 'Book for Customer',
            'Chấm công của tôi' => 'My Attendance',
            'Đăng ký khuôn mặt' => 'Face Registration',
            'Xin nghỉ phép' => 'Request Leave',
            'Báo cáo cá nhân' => 'Personal Report',

            // === DỊCH LUỒNG GIAO DỊCH NGƯỜI DÙNG ===
            // Chi tiết phim
            'Chi tiết phim' => 'Movie Details',
            'Quốc Gia' => 'Country',
            'Năm' => 'Year',
            'Ngày Phát Hành' => 'Release Date',
            'Đạo Diễn' => 'Director',
            'Diễn Viên' => 'Cast',
            'Giới Hạn độ tuổi' => 'Age Limit',
            'Đặt ngay' => 'Book Now',
            'Mô Tả Phim' => 'Movie Description',
            'Trailer' => 'Trailer',

            // Đặt vé bước 1: Rạp, Ngày, Giờ
            'Tận Hưởng Thời Gian Xem Phim Vui Vẻ' => 'Enjoy Your Happy Movie Time',
            '1. Chọn Rạp' => '1. Select Cinema',
            '2. Chọn Ngày' => '2. Select Date',
            '3. Chọn Giờ' => '3. Select Time',
            'PHIM BẠN CHỌN' => 'YOUR SELECTED MOVIE',
            'CHỌN RẠP CHIẾU' => 'SELECT CINEMA',
            'suất chiếu' => 'showtimes',
            'Phim này hiện chưa có lịch chiếu tại các rạp.' => 'This movie currently has no showtimes at cinemas.',
            'CHỌN NGÀY CHIẾU TẠI' => 'SELECT SHOW DATE AT',
            'Hôm nay' => 'Today',
            'suất' => 'show(s)',
            'Không có lịch chiếu trong 14 ngày tới tại rạp này.' => 'No showtimes scheduled in the next 14 days at this cinema.',
            'CHỌN KHUNG GIỜ CHIẾU' => 'SELECT TIME SLOT',
            'Còn' => 'Only',
            'ghế' => 'seat(s) left',
            'Hết chỗ' => 'Sold Out',
            'Không có suất chiếu nào trong ngày này.' => 'No showtimes available on this date.',

            // Sơ đồ ghế (global.php)
            '1.   Lịch Chiếu &amp; Thời Gian' => '1. Showtimes &amp; Time',
            '2. Chọn ghế ' => '2. Select Seats',
            'Giá ' => 'Price',
            ' Đã được chọn' => 'Reserved',
            'Lựa chọn của bạn ' => 'Your choice',
            'Màn Hinh' => 'Screen',
            'Màn hình' => 'Screen',

            // Xác nhận ghế (dv2.php)
            'Chọn ghế ngồi' => 'Select Seats',
            '🪑Ghế đã chọn :' => '🪑Selected seats:',
            'Tổng cộng :' => 'Total:',
            'QUAY LẠI' => 'BACK',
            'TIẾP TỤC' => 'CONTINUE',

            // Chọn Combo đồ ăn (doan.php)
            'Combo Đồ ăn' => 'Food & Drink Combos',
            'Combo riêng của rạp' => 'Cinema Specific Combo',
            'Combo toàn hệ thống' => 'System-wide Combo',
            'Thông tin đặt vé' => 'Booking Info',
            'Ghế đã chọn:' => 'Selected seats:',
            'Combo đã chọn:' => 'Selected combos:',
            'Chưa chọn combo nào' => 'No combos selected',
            'Tổng cộng:' => 'Total:',
            'Vui lòng chọn ghế trước khi tiếp tục!' => 'Please select seats before continuing!',
            'Hiện tại không có combo đồ ăn nào khả dụng cho rạp này.' => 'Currently no food/drink combos are available for this cinema.',

            // Thanh toán (thanhtoan.php)
            'Bạn có mã khuyến mãi?' => 'Do you have a discount code?',
            'Nhập mã khuyến mãi' => 'Enter discount code',
            'Áp dụng' => 'Apply',
            'Hủy mã' => 'Cancel Code',
            'Mã khuyến mãi không áp dụng cho rạp này!' => 'Discount code does not apply to this cinema!',
            'Mã khuyến mãi không hợp lệ hoặc đã hết hạn!' => 'Invalid or expired discount code!',
            'Đã áp dụng mã:' => 'Applied code:',
            'Đổi điểm tích lũy' => 'Redeem Points',
            'Điểm hiện tại:' => 'Current points:',
            'Tỷ lệ đổi: 100,000 điểm = 10,000,000 VND (100 VND = 1 điểm) | Tối thiểu: 1,000 điểm' => 'Exchange rate: 100,000 points = 10,000,000 VND (100 VND = 1 point) | Minimum: 1,000 points',
            'Nhập số điểm muốn đổi' => 'Enter points to redeem',
            'Đổi điểm' => 'Redeem',
            'Hủy đổi' => 'Cancel Redeem',
            'Số điểm phải lớn hơn 0!' => 'Points must be greater than 0!',
            'Bạn không đủ điểm! Điểm hiện tại:' => 'Insufficient points! Current points:',
            'Tối thiểu phải đổi 1,000 điểm (= 10,000 VND)' => 'Minimum redemption is 1,000 points (= 10,000 VND)',
            'Chỉ thành viên mới được đổi điểm!' => 'Only members can redeem points!',
            'Đã đổi' => 'Redeemed',
            'Tổng tiền vé:' => 'Ticket Total:',
            'Mã khuyến mãi:' => 'Discount Code:',
            'Tổng tiết kiệm:' => 'Total Savings:',
            'Số tiền thanh toán:' => 'Payment Amount:',
            'Chọn hình thức thanh toán' => 'Select Payment Method',
            'Lưu ý:' => 'Note:',
            'Chọn phương thức thanh toán bên dưới để tiếp tục' => 'Choose a payment method below to continue',
            'QR Chuyển khoản' => 'QR Transfer',
            'Quét mã QR' => 'Scan QR Code',
            'Chuyển tiền' => 'Transfer',
            'Địa chỉ rạp:' => 'Cinema address:',
            'Phòng chiếu:' => 'Room:',
            'Khung giờ chiếu:' => 'Showtime slot:',
            'Số ghế:' => 'Seats:',
            'Phim:' => 'Movie:',
            'Rạp chiếu:' => 'Cinema:',
            'Ngày chiếu:' => 'Date:',
            'Combo:' => 'Combo:',
            '1. Lịch Chiếu &amp; Thời gian' => '1. Showtimes &amp; Time',
            '2. Chọn ghế' => '2. Select Seats',
            '3. Thanh Toán ' => '3. Payment',
            '3. Thanh Toán' => '3. Payment',

            // Lịch sử điểm
            'Lịch sử điểm tích lũy' => 'Points Accumulation History',
            'Điểm tích lũy hiện tại' => 'Current Accumulated Points',
            'Mã hóa đơn' => 'Invoice ID',
            'Số tiền thanh toán' => 'Payment Amount',
            'Điểm nhận được' => 'Points Received',
            'Ngày tích lũy' => 'Date Accumulated',
            'Không có lịch sử điểm nào.' => 'No points history found.',
            'Trạng thái' => 'Status',
            'Đã tích lũy' => 'Accumulated',

            // Đăng nhập / Đăng ký / Thông tin cá nhân
            'Tên đăng nhập' => 'Username',
            'Mật khẩu' => 'Password',
            'Mật khẩu cũ' => 'Old Password',
            'Mật khẩu mới' => 'New Password',
            'Nhập lại mật khẩu' => 'Confirm Password',
            'Quên mật khẩu' => 'Forgot Password',
            'Đăng ký' => 'Register',
            'Thông tin tài khoản' => 'Account Information',
            'Cập nhật tài khoản' => 'Update Account',
            'Họ và tên' => 'Full Name',
            'Số điện thoại' => 'Phone Number',
            'Email' => 'Email',
            'Địa chỉ' => 'Address',
            'Cập nhật' => 'Update',
            'Thay đổi thông tin thành công!' => 'Profile updated successfully!',
            'Thông tin khách hàng vãng lai' => 'Guest Customer Information',
            'Nhập thông tin để tiếp tục đặt vé mà không cần đăng nhập' => 'Enter info to continue booking without logging in',
            'Tiếp tục với tư cách khách' => 'Continue as Guest',

            // Vé của tôi / Chi tiết vé
            'Mã vé' => 'Ticket Code',
            'Ghế' => 'Seats',
            'Ngày đặt' => 'Date Booked',
            'Tổng tiền' => 'Total Amount',
            'Trạng thái thanh toán' => 'Payment Status',
            'Đã thanh toán' => 'Paid',
            'Chưa thanh toán' => 'Unpaid',
            'Chi tiết vé' => 'Ticket Details',
            'Xem vé' => 'View Ticket',
            'In vé PDF' => 'Download PDF Ticket',
            'Quét mã QR để soát vé' => 'Scan QR Code to verify ticket',

            // Thứ ngày
            'Thứ Hai' => 'Monday',
            'Thứ Ba' => 'Tuesday',
            'Thứ Tư' => 'Wednesday',
            'Thứ Năm' => 'Thursday',
            'Thứ Sáu' => 'Friday',
            'Thứ Bảy' => 'Saturday',
            'Chủ Nhật' => 'Sunday',

            // Bổ sung các từ khóa mới
            'Điểm hiện có' => 'Available Points',
            'Có thể sử dụng' => 'Available to use',
            'Tổng điểm tích luỹ' => 'Total Accumulated Points',
            'Từ trước đến nay' => 'Lifetime points',
            'Hệ số nhân điểm' => 'Point Multiplier',
            'Hạng Bạc' => 'Silver Tier',
            'Hạng Vàng' => 'Gold Tier',
            'Hạng Kim Cương' => 'Diamond Tier',
            'Tích điểm' => 'Accumulate points',
            'Tiến độ lên hạng' => 'Progress to',
            'Chúc mừng! Bạn đã đạt hạng cao nhất!' => 'Congratulations! You have reached the highest tier!',
            'Lịch sử giao dịch' => 'Transaction History',
            '50 giao dịch gần nhất' => 'Last 50 transactions',
            '100 giao dịch gần nhất' => 'Last 100 transactions',
            '200 giao dịch gần nhất' => 'Last 200 transactions',
            'Chưa có lịch sử giao dịch' => 'No transaction history yet',
            'Hãy đặt vé xem phim để bắt đầu tích điểm nhé!' => 'Book movie tickets to start accumulating points!',
            'Ngày giờ' => 'Date & Time',
            'Loại giao dịch' => 'Transaction Type',
            'Nội dung' => 'Details',
            'Điểm thay đổi' => 'Points Change',
            'Cộng điểm' => 'Add Points',
            'Trừ điểm' => 'Subtract Points',
            'Loại' => 'Type',
            'Nhập email đã đăng ký:' => 'Enter registered email:',
            'Gửi mã OTP' => 'Send OTP Code',
            'Nhập mã OTP đã gửi về email:' => 'Enter OTP code sent to email:',
            'Xác nhận OTP' => 'Confirm OTP',
            'Nhập mật khẩu mới:' => 'Enter new password:',
            'Nhập lại mật khẩu mới:' => 'Confirm new password:',
            'Vé đã mua' => 'Purchased Tickets',
            'Xin chào : ' => 'Hello: ',
            'Đăng nhập với tư cách Quản trị' => 'Login as Admin',
            'Tôi đồng ý với' => 'I agree to the',
            'Điều khoản' => 'Terms',
            'và' => 'and',
            'Chính sách bảo mật' => 'Privacy Policy',
            'DANH SÁCH VÉ ĐÃ MUA' => 'PURCHASED TICKETS LIST',
            'CHI TIẾT VÉ' => 'TICKET DETAILS',
            'Rạp chiếu:' => 'Cinema:',
            'Địa chỉ rạp:' => 'Cinema address:',
            'Phòng chiếu:' => 'Room:',
            'Người đặt:' => 'Customer:',
            'Thời gian đặt:' => 'Time booked:',
            'PHIM:' => 'MOVIE:',
            'Ghế:' => 'Seats:',
            'Trạng thái:' => 'Status:',
            'Đã dùng' => 'Used',
            'Đã hủy' => 'Cancelled',
            'Hết hạn' => 'Expired',
            'Trạng thái không xác định' => 'Unknown status',
            'Hủy vé' => 'Cancel Ticket',
            'Khi hủy vé bạn liên hệ với đội ngũ CSKH của Galaxy Studio để được hoàn tiền' => 'When cancelling a ticket, please contact Galaxy Studio CS team for a refund',
            'Bạn chưa có vé nào được đặt.' => 'You have no tickets booked yet.',
            'Cảm ơn' => 'Thank you',
            'bạn đã mua vé thành công' => 'your ticket purchase was successful',
            '🎉 Chúc mừng! Bạn nhận được' => '🎉 Congratulations! You received',
            '🏆 Bạn đã được nâng hạng lên' => '🏆 You have been upgraded to',
            '⭐ Đã sử dụng' => '⭐ Redeemed',
            'để giảm giá' => 'for discount',
            'Tổng điểm hiện tại:' => 'Total available points:',
            'Tải / In hóa đơn (PDF)' => 'Download / Print Invoice (PDF)',
            'Tải / In vé (PDF)' => 'Download / Print Ticket (PDF)',
            'Vé xem phim' => 'Movie Ticket',
            'Phòng:' => 'Room:',
            'Giá vé:' => 'Ticket Price:',
            'Không xác định' => 'Unknown',
            'Vui lòng đưa mã này cho nhân viên tại cổng kiểm soát' => 'Please present this code to the staff at checkout gate',
            'Galaxy Studio xin cảm ơn!' => 'Galaxy Studio thanks you!',
            'Lưu mã QR (ảnh)' => 'Save QR Code (image)',
            'Không thể lưu ảnh. Vui lòng thử lại trên trình duyệt khác hoặc kiểm tra cài đặt tải file.' => 'Unable to save image. Please try again on another browser or check download settings.',
            'Lỗi khi tạo ảnh:' => 'Error generating image:',
            'Bạn chưa thanh toán vé.' => 'You have not paid for the ticket yet.',
            'Đặt vé nhanh' => 'Quick Booking',
            'Nhập thông tin của bạn để tiếp tục đặt vé' => 'Enter your info to continue booking',
            'Nhập họ và tên của bạn' => 'Enter your full name',
            'Nhập email của bạn' => 'Enter your email address',
            'Vui lòng nhập họ và tên (ít nhất 2 ký tự)' => 'Please enter full name (at least 2 characters)',
            'Số điện thoại không hợp lệ (10 số bắt đầu từ 0)' => 'Invalid phone number (10 digits starting with 0)',
            'Email không hợp lệ' => 'Invalid email address',
            'Đang xử lý...' => 'Processing...',
            'Đã có tài khoản?' => 'Already have an account?',
            'Đăng nhập ngay' => 'Login now',
            'Hạng' => 'Tier',
            'Gửi' => 'Submit',
            'Nhập mật khẩu cũ' => 'Enter old password',
            'Nhập mật khẩu mới' => 'Enter new password',
            'Nhập lại mật khẩu' => 'Confirm password',
            'Hủy vé thành công!' => 'Ticket cancelled successfully!',
            'Vé không tồn tại' => 'Ticket does not exist',
            'Vé đã được sử dụng, không thể hủy/đổi' => 'Ticket is already used, cannot cancel/exchange',
            'Vé đã hủy' => 'Ticket already cancelled',
            'Vé đã hết hạn' => 'Ticket already expired',
            'Thời gian chiếu chưa được cập nhật' => 'Showtime not updated yet',
            'Giờ chiếu đã qua' => 'Showtime has passed',
            'Không thể hủy/đổi vé. Phải hủy trước giờ chiếu ít nhất 4 tiếng' => 'Cannot cancel/exchange ticket. Must cancel at least 4 hours before showtime',
            // Liên hệ / Contact
            'Bạn có thắc mắc hoặc cần trợ giúp, <br><span class="contact__describe">đừng ngại ngùng và liên hệ với chúng tôi</span>' => 'Do you have questions or need support, <br><span class="contact__describe">do not hesitate to contact us</span>',
            'Drop us a line' => 'Drop us a line',
            'Your name' => 'Your name',
            'Your email' => 'Your email',
            'Your message' => 'Your message',
            'send message' => 'send message',
            'Trying to find our location? <br> <span class="contact__describe">we are here</span>' => 'Trying to find our location? <br> <span class="contact__describe">we are here</span>',
            'Gửi tin nhắn thành công! Chúng tôi sẽ phản hồi sớm nhất qua email của bạn.' => 'Message sent successfully! We will respond as soon as possible via your email.',
            'Vui lòng điền đầy đủ thông tin và nhập email hợp lệ!' => 'Please fill in all information and enter a valid email address!',
            'Có lỗi xảy ra, vui lòng thử lại!' => 'An error occurred, please try again!',
            'Quản lý liên hệ' => 'Contact Management',
            'Danh sách liên hệ' => 'Contact List',
            'Chưa xử lý' => 'Pending',
            'Đã xử lý' => 'Processed',
            'Nội dung trả lời' => 'Reply Content'
        ];
    }
    
    $lang = get_current_lang();
    if ($lang === 'en') {
        if (isset($translations[$text])) {
            return $translations[$text];
        }
        
        // Hỗ trợ dịch động phần text động chứa số lượng vé đã đặt
        if (preg_match('/Đã có (\d+) vé đã đặt/', $text, $matches)) {
            return "Already " . $matches[1] . " tickets booked";
        }
        
        // Dịch động thông báo hủy vé hoàn điểm
        if (preg_match('/Hủy vé thành công! Đã hoàn (\d+) điểm/', $text, $matches)) {
            return "Ticket cancelled successfully! Refunded " . $matches[1] . " points";
        }
        
        // Dịch động thời gian hủy vé còn lại
        if (preg_match('/Bạn còn (\d+) giờ để hủy\/đổi vé/', $text, $matches)) {
            return "You have " . $matches[1] . " hours left to cancel/exchange this ticket";
        }
        
        // Dịch động lý do tích điểm
        if (preg_match('/Tích điểm từ đơn hàng #(\d+)/', $text, $matches)) {
            return "Points credited from order #" . $matches[1];
        }
        
        // Dịch động lý do trừ điểm
        if (preg_match('/Đổi điểm giảm giá đơn hàng #(\d+) \(-([\d,.]+) VND\)/', $text, $matches)) {
            return "Redeemed points discount for order #" . $matches[1] . " (-" . $matches[2] . " VND)";
        }
        
        // Dịch động lý do hoàn điểm
        if (preg_match('/Hoàn điểm do hủy vé \(Mã vé: VE(\d+)\)/', $text, $matches)) {
            return "Points refunded due to ticket cancellation (Ticket ID: VE" . $matches[1] . ")";
        }
    }
    
    return $text;
}
?>
