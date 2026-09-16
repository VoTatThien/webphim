<?php include "banner.php"?>

<!--end slider -->
<!-- Main content -->
<section class="container">
    <div class="movie-best">
        <div class="col-sm-10 col-sm-offset-1 movie-best__rating"><?= __("Phim hot nhất") ?></div>
        <div class="col-sm-10 col-sm-offset-1 change--col">
            <?php 
            $num_items = count($loadphimhot);
            $card_width_pct = 25;
            $poster_w = 190;
            $poster_h = 285;
            if ($num_items <= 4) {
                $card_width_pct = 25;
                $poster_w = 220;
                $poster_h = 330;
            } elseif ($num_items === 5) {
                $card_width_pct = 20;
                $poster_w = 180;
                $poster_h = 270;
            } elseif ($num_items >= 6) {
                $card_width_pct = 16.666666;
                $poster_w = 150;
                $poster_h = 225;
            }

            foreach ($loadphimhot as $hot){
                extract($hot);
                $linkp = "index.php?act=ctphim&id=".$id;
                $hinh = "imgavt/".$img;
                echo '<div class="movie-beta__item" style="width: '.$card_width_pct.'%; float: left; text-align: center; margin-bottom: 20px;">
                    <div style="display: inline-block; position: relative; width: '.$poster_w.'px; height: '.$poster_h.'px; margin: 0 auto; text-align: left; border-radius: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        <a href="'.$linkp.'" style="display: block; width: 100%; height: 100%; border-radius: 6px; overflow: hidden;">
                            <img alt="lỗi cmmr" src="'.$hinh.'" style="width: '.$poster_w.'px; height: '.$poster_h.'px; display: block; border-radius: 6px;" loading="lazy">
                        </a>
                        <span class="best-rate">5.0</span>

                        <ul class="movie-beta__info" style="width: '.$poster_w.'px; box-sizing: border-box; left: 0; right: 0;">
                            <li><span class="best-voted">'.__("Đã có ".$tong_so_ve." vé đã đặt").'</span></li>
                            <li>
                                <p class="movie__time">'.$thoi_luong_phim.' '.__("phút").'</p>
                                <p>'.__($name).'</p>
                            </li>
                            <li class="last-block">
                                <a href="'.$linkp. '" class="slide__link">'.__("Chi tiết").'</a>
                            </li>
                        </ul>
                    </div>
                </div>';
            } ?>


        </div>
        <div class="col-sm-10 col-sm-offset-1 movie-best__check"><?= __("KIỂM TRA TẤT CẢ CÁC PHIM ĐANG CHIẾU") ?></div>
    </div>


    <div class="clearfix"></div>

    <h2 id='target' class="page-heading heading--outcontainer"><?= __("Phim mới nhất") ?></h2>

    <div class="col-sm-12">
        <div class="row" >
            <div class="col-sm-8 col-md-12">
                <?php foreach ($loadphimhome as $phim){
                    extract($phim);
                    $hinh ="imgavt/".$img;
                    $linkp="index.php?act=ctphim&id=".$id;
                    echo '<!-- Movie variant with time -->
                <div class="movie movie--test movie--test--dark movie--test--left" style="height: 350px;background-color: rgba(255, 213, 100, 0.8);">
                    <div class="movie__images">
                        <a href="'.$linkp.'" class="movie-beta__link">
                            <img alt="lỗi cmnr" src="'.$hinh.'"  style="width: 300px;height: 350px" loading="lazy">
                        </a>
                    </div>

                    <div class="movie__info">
                        <a href="'.$linkp.'" class="movie__title" style="font-size:1.4vw">'.__($tieu_de).'</a>

                        <p class="movie__time">'.__("Thời Lượng Phim").' : '.$thoi_luong_phim.' '.__("phút").'</p>

                        <p class="movie__option"><a href="">'.__("Thể Loại").' : '.__($name).'</a></p>

                        <div class="movie__rate">
                            <span class="movie__rating" >5.0</span>
                        </div>
                    </div>
                </div>
                <!-- Movie variant with time -->';
                } ?>

                <div class="row">
                    <div class="social-group">
                        <div class="col-sm-6 col-md-4 col-sm-push-6 col-md-push-4">
                            <div class="social-group__head" >Tết Làng Địa Ngục <br><p style="padding: 5px;font-size: 2vw;">Kẻ ăn hồn</p></div>
                            <div class="social-group__content">Giữa lúc series kinh dị Tết ở làng địa ngục gây sốt, bộ đôi đạo diễn Trần Hữu Tấn <br class="hidden-xs"><br>nhà sản xuất Hoàng Quân tiếp tục quay trở lại với Kẻ Ăn Hồn, dự án kinh dị ma mị mang đậm chất Việt.</div>
                        </div>

                        <div class="col-sm-6 col-md-4 col-sm-pull-6 col-md-pull-4">
                            <div class="facebook-group">

                              <img src="imgavt/kẻ ăn hồn.jpg" alt="" height="400px" loading="lazy">
                            </div>
                        </div>

                        <div class="clearfix visible-sm"></div>
                        <div class="col-sm-6 col-md-4">
                            <div class="twitter-group">
                            <div ><p class="twitter__head"> <div class="social-group__head">Phim Hot</div></p><iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true" class="twitter-follow-button twitter-follow-button-rendered" style="position: static; visibility: visible; width: 76px; height: 20px;" title="Twitter Follow Button" src="https://platform.twitter.com/widgets/follow_button.d37472b4a6622d0b1fff46ad904f6896.en.html#dnt=false&amp;id=twitter-widget-0&amp;lang=en&amp;screen_name=OliaGozha&amp;show_count=false&amp;show_screen_name=false&amp;size=m&amp;time=1701421844081" data-screen-name="OliaGozha"></iframe><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script><div ><img src="imgavt/kẻ ăn hồn2.jpeg" width="300" height="250" loading="lazy"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "view/tintuc.php"?>

</section>

<div class="clearfix"></div>