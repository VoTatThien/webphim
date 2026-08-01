
   <!-- Side Header Start -->
        <div class="side-header show">
            <button class="side-header-close"><i class="zmdi zmdi-close"></i></button>
            <!-- Side Header Inner Start -->
            <div class="side-header-inner custom-scroll">

                <?php 
                    require_once __DIR__ . '/../../helpers/quyen.php';
                    $role = $_SESSION['user1']['vai_tro'] ?? -1;
                    $currentAct = $_GET['act'] ?? 'home';
                ?>
                <nav class="side-header-menu" id="side-header-menu">
                    <ul>
                        <li><a href="index.php?act=home" class="<?= $currentAct==='home'?'is-active':''; ?>"><i class="fa fa-institution"></i> <span><?= __("Trang chủ") ?></span></a></li>

                        <?php if ($role == ROLE_ADMIN_HE_THONG): ?>
                            <li><a href="index.php?act=cauhinh" class="<?= $currentAct==='cauhinh'?'is-active':''; ?>"><i class="zmdi zmdi-settings"></i> <span><?= __("Cấu hình website") ?></span></a></li>
                            <li><a href="#"><i class="fa fa-user"></i> <span><?= __("Quản Lý Tài Khoản") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=themuser" class="<?= $currentAct==='themuser'?'is-active':''; ?>"><i class="zmdi zmdi-account-add"></i> <span><?= __("Thêm tài khoản") ?></span></a></li>
                                    <li><a href="index.php?act=QTkh" class="<?= $currentAct==='QTkh'?'is-active':''; ?>"><i class="fa fa-users"></i> <span><?= __("Khách hàng") ?></span></a></li>
                                    <li><a href="index.php?act=QTvien" class="<?= $currentAct==='QTvien'?'is-active':''; ?>"><i class="fa fa-id-badge"></i> <span><?= __("Nhân viên") ?></span></a></li>
                                    <li><a href="index.php?act=QLquanlyrap" class="<?= $currentAct==='QLquanlyrap'?'is-active':''; ?>"><i class="fa fa-user-secret"></i> <span><?= __("Quản lý rạp") ?></span></a></li>
                                    <li><a href="index.php?act=QLquanlycum" class="<?= $currentAct==='QLquanlycum'?'is-active':''; ?>"><i class="zmdi zmdi-city-alt"></i> <span><?= __("Quản lý cụm rạp") ?></span></a></li>
                                    <li><a href="index.php?act=cum_admin" class="<?= $currentAct==='cum_admin'?'is-active':''; ?>"><i class="zmdi zmdi-shield-security"></i> <span><?= __("Admin hệ thống") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="fa fa-line-chart" ></i> <span><?= __("Thống Kê Doanh Thu") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=DTdh&&trang=1"><i class="fa fa-line-chart" ></i><span><?= __("Danh Thu Phim") ?></span></a></li>
                                    <li><a href="index.php?act=DTngay&&trang=1"><i class="fa fa-line-chart" ></i><span><?= __("Theo Ngày") ?></span></a></li>
                                    <li><a href="index.php?act=DTtuan&&trang=1"><i class="fa fa-line-chart" ></i><span><?= __("Theo Tuần") ?></span></a></li>
                                    <li><a href="index.php?act=DTthang&&trang=1"><i class="fa fa-line-chart" ></i><span><?= __("Theo Tháng") ?></span></a></li>
                                    <li><a href="index.php?act=baocao_analytics" class="<?= $currentAct==='baocao_analytics'?'is-active':''; ?>"><i class="fa fa-dashboard" ></i><span><?= __("Phân Tích Nâng Cao 📊") ?></span></a></li>
                                </ul>
                            </li>
                            <li><a href="index.php?act=QLsuco" class="<?= $currentAct==='QLsuco'?'is-active':''; ?>"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> <span><?= __("Quản Lý Sự Cố ⚠️") ?></span></a></li>
                            <li><a href="index.php?act=autopilot_showtimes" class="<?= $currentAct==='autopilot_showtimes'?'is-active':''; ?>"><i class="zmdi zmdi-flash zmdi-hc-fw"></i> <span><?= __("Autopilot Suất Chiếu ⚡") ?></span></a></li>
                            <li><a href="index.php?act=QLlienhe" class="<?= $currentAct==='QLlienhe'?'is-active':''; ?>"><i class="fa fa-envelope"></i> <span><?= __("Quản lý liên hệ") ?></span></a></li>
                        <?php elseif ($role == ROLE_QUAN_LY_CUM): ?>
                            <li class="has-sub-menu"><a href="#"><i class="fa fa-building"></i> <span><?= __("Quản Lý Rạp") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=QLrap" class="<?= $currentAct==='QLrap'?'is-active':''; ?>"><i class="fa fa-list"></i> <span><?= __("Danh sách rạp") ?></span></a></li>
                                    <li><a href="index.php?act=themrp" class="<?= $currentAct==='themrp'?'is-active':''; ?>"><i class="fa fa-plus"></i> <span><?= __("Thêm rạp") ?></span></a></li>
                                </ul>
                            </li>
                            <li><a href="index.php?act=QLloaiphim" class="<?= $currentAct==='QLloaiphim'?'is-active':''; ?>"><i class="fa fa-window-restore"></i> <span><?= __("Quản Lý Loại Phim") ?></span></a></li>
                            <li><a href="index.php?act=QLphim" class="<?= $currentAct==='QLphim'?'is-active':''; ?>"><i class="fa fa-film"></i> <span><?= __("Quản Lý Phim (Cụm)") ?></span></a></li>
                            <li><a href="index.php?act=duyet_lichchieu" class="<?= $currentAct==='duyet_lichchieu'?'is-active':''; ?>"><i class="zmdi zmdi-check"></i> <span><?= __("Duyệt kế hoạch chiếu") ?></span></a></li>
                            <li><a href="index.php?act=lich_rap" class="<?= $currentAct==='lich_rap'?'is-active':''; ?>"><i class="zmdi zmdi-calendar"></i> <span><?= __("Lịch theo rạp") ?></span></a></li>
                            <li><a href="index.php?act=phanphim" class="<?= $currentAct==='phanphim'?'is-active':''; ?>"><i class="zmdi zmdi-collection-video"></i> <span><?= __("Phân phối phim") ?></span></a></li>
                            <li class="has-sub-menu"><a href="#"><i class="zmdi zmdi-accounts"></i> <span><?= __("Tài khoản") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=themuser"><i class="zmdi zmdi-account-add"></i> <span><?= __("Thêm tài khoản") ?></span></a></li>
                                    <li><a href="index.php?act=QTkh"><i class="fa fa-users"></i> <span><?= __("Khách hàng") ?></span></a></li>
                                    <li><a href="index.php?act=QTvien"><i class="fa fa-id-badge"></i> <span><?= __("Nhân viên") ?></span></a></li>
                                    <li><a href="index.php?act=QLquanlyrap"><i class="fa fa-user-secret"></i> <span><?= __("Quản lý rạp") ?></span></a></li>

                                    <li><a href="index.php?act=cum_admin"><i class="zmdi zmdi-shield-security"></i> <span><?= __("Admin hệ thống") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="fa fa-line-chart" ></i> <span><?= __("Thống Kê") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=DTdh&&trang=1" class="<?= $currentAct==='DTdh'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Doanh Thu Phim") ?></span></a></li>
                                    <li><a href="index.php?act=DTngay&&trang=1" class="<?= $currentAct==='DTngay'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Theo Ngày") ?></span></a></li>
                                    <li><a href="index.php?act=DTtuan&&trang=1" class="<?= $currentAct==='DTtuan'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Theo Tuần") ?></span></a></li>
                                    <li><a href="index.php?act=DTthang&&trang=1" class="<?= $currentAct==='DTthang'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Theo Tháng") ?></span></a></li>
                                    <li><a href="index.php?act=DTphim_rap" class="<?= $currentAct==='DTphim_rap'?'is-active':''; ?>"><i class="fa fa-bar-chart" ></i><span><?= __("DT Phim theo Rạp") ?></span></a></li>
                                    <li><a href="index.php?act=hieusuat_rap" class="<?= $currentAct==='hieusuat_rap'?'is-active':''; ?>"><i class="fa fa-area-chart" ></i><span><?= __("Hiệu suất Rạp") ?></span></a></li>
                                    <li><a href="index.php?act=TKrap" class="<?= $currentAct==='TKrap'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Theo Rạp") ?></span></a></li>
                                    <li><a href="index.php?act=baocao_analytics" class="<?= $currentAct==='baocao_analytics'?'is-active':''; ?>"><i class="fa fa-dashboard" ></i><span><?= __("Phân Tích Nâng Cao 📊") ?></span></a></li>
                                </ul>
                            </li>
                            <li><a href="index.php?act=QLsuco" class="<?= $currentAct==='QLsuco'?'is-active':''; ?>"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> <span><?= __("Quản Lý Sự Cố ⚠️") ?></span></a></li>
                            <li><a href="index.php?act=autopilot_showtimes" class="<?= $currentAct==='autopilot_showtimes'?'is-active':''; ?>"><i class="zmdi zmdi-flash zmdi-hc-fw"></i> <span><?= __("Autopilot Suất Chiếu ⚡") ?></span></a></li>
                            <li><a href="index.php?act=quan_ly_tin_tuc" class="<?= $currentAct==='quan_ly_tin_tuc'?'is-active':''; ?>"><i class="zmdi zmdi-info-outline"></i> <span><?= __("Quản Lý Tin Tức") ?></span></a></li>
                            <li><a href="index.php?act=QLlienhe" class="<?= $currentAct==='QLlienhe'?'is-active':''; ?>"><i class="fa fa-envelope"></i> <span><?= __("Quản lý liên hệ") ?></span></a></li>
                        <?php elseif ($role == ROLE_QUAN_LY_RAP): ?>
                            <li class="has-sub-menu"><a href="#"><i class="zmdi zmdi-calendar"></i> <span><?= __("Lịch làm việc") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=ql_lichlamviec"><i class="zmdi zmdi-format-list-bulleted"></i><span><?= __("Dạng bảng") ?></span></a></li>
                                    <li><a href="index.php?act=ql_lichlamviec_calendar"><i class="zmdi zmdi-calendar-note"></i><span><?= __("📅 Calendar phân công") ?></span></a></li>
                                </ul>
                            </li>
                            <li><a href="index.php?act=ql_duyetnghi"><i class="zmdi zmdi-time-restore"></i> <span><?= __("Duyệt nghỉ phép") ?></span></a></li>
                            <li><a href="index.php?act=kehoach" class="<?= $currentAct==='kehoach'?'is-active':''; ?>"><i class="zmdi zmdi-movie-alt"></i> <span><?= __("Lập kế hoạch chiếu phim") ?></span></a></li>
                            <li><a href="index.php?act=phong"><i class="zmdi zmdi-local-movies zmdi-hc-fw"></i> <span><?= __("Quản Lý Phòng - Ghế") ?></span></a></li>
                            <li><a href="index.php?act=QLsuco"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> <span><?= __("Quản Lý Sự Cố ⚠️") ?></span></a></li>
                            <li class="has-sub-menu"><a href="#"><i class="zmdi zmdi-tv-alt-play zmdi-hc-fw"></i> <span><?= __("Quản Lý Suất Chiếu") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=QLsuatchieu"><i class="zmdi zmdi-tv-alt-play zmdi-hc-fw"></i><span><?= __("Suất Chiếu") ?></span></a></li>
                                    <li><a href="index.php?act=thoigian"><i class="zmdi zmdi-tv-alt-play zmdi-hc-fw"></i><span><?= __("Khung Giờ") ?></span></a></li>
                                    <li><a href="index.php?act=lich_rap" class="<?= $currentAct==='lich_rap'?'is-active':''; ?>"><i class="zmdi zmdi-calendar"></i><span><?= __("Lịch theo rạp") ?></span></a></li>
                                    <li><a href="index.php?act=autopilot_showtimes"><i class="zmdi zmdi-flash zmdi-hc-fw"></i><span><?= __("Autopilot Suất Chiếu ⚡") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="zmdi zmdi-local-offer"></i> <span><?= __("Ưu đãi") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=QLkm" class="<?= $currentAct==='QLkm'?'is-active':''; ?>"><i class="zmdi zmdi-ticket-star"></i> <span><?= __("Khuyến mãi") ?></span></a></li>
                                    <li><a href="index.php?act=QLcombo" class="<?= $currentAct==='QLcombo'?'is-active':''; ?>"><i class="zmdi zmdi-cocktail"></i> <span><?= __("Combo") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="zmdi zmdi-accounts"></i> <span><?= __("Nhân sự") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=chamcong" class="<?= $currentAct==='chamcong'?'is-active':''; ?>"><i class="zmdi zmdi-check"></i> <span><?= __("Chấm công") ?></span></a></li>
                                    <li><a href="index.php?act=bangluong" class="<?= $currentAct==='bangluong'?'is-active':''; ?>"><i class="zmdi zmdi-money"></i> <span><?= __("Bảng lương") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="fa fa-line-chart" ></i> <span><?= __("Thống Kê") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=DTphim_rap" class="<?= $currentAct==='DTphim_rap'?'is-active':''; ?>"><i class="fa fa-bar-chart" ></i><span><?= __("Doanh thu phim") ?></span></a></li>
                                    <li><a href="index.php?act=TKrap" class="<?= $currentAct==='TKrap'?'is-active':''; ?>"><i class="fa fa-line-chart" ></i><span><?= __("Tổng quan rạp") ?></span></a></li>
                                    <li><a href="index.php?act=baocao_analytics" class="<?= $currentAct==='baocao_analytics'?'is-active':''; ?>"><i class="fa fa-dashboard" ></i><span><?= __("Phân Tích Nâng Cao 📊") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="fa fa-user"></i> <span><?= __("Tài khoản") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=themuser" class="<?= $currentAct==='themuser'?'is-active':''; ?>"><i class="zmdi zmdi-account-add"></i> <span><?= __("Thêm tài khoản") ?></span></a></li>
                                    <li><a href="index.php?act=QTvien" class="<?= $currentAct==='QTvien'?'is-active':''; ?>"><i class="fa fa-id-badge"></i> <span><?= __("Nhân viên") ?></span></a></li>
                                </ul>
                            </li>
                            <li class="has-sub-menu"><a href="#"><i class="ti-shopping-cart"></i> <span><?= __("Vé") ?></span></a>
                                <ul class="side-header-sub-menu">
                                    <li><a href="index.php?act=ve"><i class="ti-shopping-cart"></i> <span><?= __("Quản lý vé") ?></span></a></li>
                                    <li><a href="index.php?act=scanve_new"><i class="zmdi zmdi-check"></i> <span><?= __("Kiểm tra vé (QR)") ?></span></a></li>
                                    <li><a href="index.php?act=doi_hoan_ve"><i class="zmdi zmdi-refresh"></i> <span><?= __("Đổi/Hoàn vé") ?></span></a></li>
                                </ul>
                            </li>
                            <li><a href="index.php?act=thietbiphong"><i class="zmdi zmdi-wrench"></i> <span><?= __("Thiết bị phòng") ?></span></a></li>
                            <li><a href="index.php?act=QLfeed&&sotrang=1"><i class="fa fa-comments" ></i> <span><?= __("Bình luận/Feedback") ?></span></a></li>
                            <li><a href="index.php?act=QLlienhe" class="<?= $currentAct==='QLlienhe'?'is-active':''; ?>"><i class="fa fa-envelope"></i> <span><?= __("Quản lý liên hệ") ?></span></a></li>
                        <?php elseif ($role == ROLE_NHAN_VIEN): ?>
                            <li><a href="index.php?act=ve"><i class="ti-shopping-cart"></i> <span><?= __("Đặt/Quản lý vé") ?></span></a></li>
                            <li><a href="index.php?act=nv_datve"><i class="zmdi zmdi-ticket-star"></i> <span><?= __("Đặt vé cho khách") ?></span></a></li>
                            <li><a href="index.php?act=scanve_new"><i class="zmdi zmdi-check"></i> <span><?= __("Kiểm tra vé (QR)") ?></span></a></li>
                            <li><a href="index.php?act=nv_lichlamviec"><i class="zmdi zmdi-calendar"></i> <span><?= __("Lịch làm việc") ?></span></a></li>
                            <li><a href="index.php?act=nv_chamcong"><i class="zmdi zmdi-time"></i> <span><?= __("Chấm công của tôi") ?></span></a></li>
                            <li><a href="index.php?act=register_face"><i class="zmdi zmdi-account"></i> <span><?= __("Đăng ký khuôn mặt") ?></span></a></li>
                            <li><a href="index.php?act=xinnghi"><i class="zmdi zmdi-time-restore"></i> <span><?= __("Xin nghỉ phép") ?></span></a></li>
                            <li><a href="index.php?act=nv_baocao"><i class="fa fa-line-chart"></i> <span><?= __("Báo cáo cá nhân") ?></span></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

            </div><!-- Side Header Inner End -->
        </div><!-- Side Header End -->

<!-- Chat Modal Component -->
<?php include __DIR__ . '/chat_modal.php'; ?>
