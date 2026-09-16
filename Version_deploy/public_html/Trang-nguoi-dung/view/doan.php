<?php include "view/search.php"; ?>

<style>
    .container {
        width: 80%;
        margin: 0 auto;
    }

    h1 {
        text-align: center;
    }

    .prodoan {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .prodo {
        width: 23%;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .prodo:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transform: translateY(-5px);
    }
    
    .prodo img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .combo-badge {
        display: inline-block;
        background: #4a3e43;
        border: 1px solid #ffd564;
        color: #ffd564;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 11px;
        margin-top: 5px;
    }

    .check_do_an {
        background-color: #dc3545;
        color: white;
        padding: 8px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .check_do_an:hover {
        background-color: #c82333;
    }
    
    .btn--success {
        background-color: #28a745 !important;
    }
    
    .btn--success:hover {
        background-color: #218838 !important;
    }
    
    .no-combo-message {
        text-align: center;
        padding: 40px;
        color: #999;
        font-size: 18px;
    }
    
    /* Quantity control styles */
    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    .quantity-btn {
        background: #ffd564;
        color: #4c4145;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .quantity-btn:hover {
        background: #ffe08d;
        transform: scale(1.1);
    }
    
    .quantity-btn:active {
        transform: scale(0.95);
    }
    
    .quantity-display {
        font-size: 20px;
        font-weight: bold;
        color: #fff;
        min-width: 30px;
        text-align: center;
    }
    
    .combo-selected-indicator {
        background: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        margin-top: 10px;
        display: inline-block;
    }
</style>

<!-- Info Bar hiển thị thông tin đặt vé -->
<div class="booking-info-bar" style="background: #232023; border: 1px solid #4a3e43; padding: 20px; margin: 20px auto; max-width: 1200px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-around; align-items: center; color: white;">
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-film" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Phim:") ?></strong><br>
                <?= isset($_SESSION['tong']['tieu_de']) ? htmlspecialchars(__($_SESSION['tong']['tieu_de'])) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-building" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Rạp:") ?></strong><br>
                <?= isset($_SESSION['tong']['ten_rap']) ? htmlspecialchars(__($_SESSION['tong']['ten_rap'])) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-calendar" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Ngày chiếu:") ?></strong><br>
                <?= isset($_SESSION['tong']['ngay_chieu']) ? htmlspecialchars($_SESSION['tong']['ngay_chieu']) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-clock" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Giờ chiếu:") ?></strong><br>
                <?= isset($_SESSION['tong']['thoi_gian_chieu']) ? htmlspecialchars($_SESSION['tong']['thoi_gian_chieu']) : 'N/A' ?>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="place-form-area">
    <section class="container">
        <h1><?= __("Combo Đồ ăn") ?></h1>
        
        <?php if (isset($combos) && count($combos) > 0): ?>
        <div class="prodoan">
            <?php foreach ($combos as $combo): ?>
            <div class="prodo">
                <img src="<?= !empty($combo['hinh_anh']) ? $combo['hinh_anh'] : 'imgavt/combo1.png' ?>" alt="<?= htmlspecialchars($combo['ten_combo']) ?>">
                <h3><?= htmlspecialchars($combo['ten_combo']) ?></h3>
                <p><?= htmlspecialchars($combo['mo_ta']) ?></p>
                
                <?php if ($combo['id_rap'] !== null): ?>
                    <span class="combo-badge"><i class="fa fa-building-o"></i> <?= __("Combo riêng của rạp") ?></span>
                <?php else: ?>
                    <span class="combo-badge"><i class="fa fa-star-o"></i> <?= __("Combo toàn hệ thống") ?></span>
                <?php endif; ?>
                
                <p style="font-size: 20px; color: #dc3545; font-weight: bold; margin-top: 10px;">
                    <?= __("Giá") ?>: <?= number_format($combo['gia'] ?? 0, 0, ',', '.') ?>đ
                </p>
                
                <!-- Quantity control -->
                <div class="quantity-control" data-combo-id="<?= $combo['id'] ?? $combo['id_combo'] ?>" 
                     data-combo-name="<?= htmlspecialchars($combo['ten_combo'] ?? $combo['ten'] ?? '') ?>"
                     data-combo-price="<?= $combo['gia'] ?? 0 ?>">
                    <button type="button" class="quantity-btn btn-decrease">−</button>
                    <span class="quantity-display">0</span>
                    <button type="button" class="quantity-btn btn-increase">+</button>
                </div>
                
                <div class="combo-selected-indicator" style="display: none;">
                    <?= __("Đã chọn:") ?> <span class="selected-count">0</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="no-combo-message">
                <i class="fa fa-info-circle" style="font-size: 50px; color: #ddd;"></i>
                <p><?= __("Hiện tại không có combo đồ ăn nào khả dụng cho rạp này.") ?></p>
            </div>
        <?php endif; ?>

    </section>
</div>

<form action="index.php?act=dv4" method="post">
    <div class="col-lg-offset-1">
        <div class="tong">
            <h2 class="phim" style="color: #ffd564;"><?= __("Thông tin đặt vé") ?></h2>
            
            <div style="display: flex; margin-bottom: 10px; align-items: center;">
                <span style="color: #fff;"><i class="fa fa-circle-o" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Ghế đã chọn:") ?></span>
                <div class="checked-place" style="margin-left: 10px;">
                    <?php
                    if (isset($ten_ghe['ghe'])) {
                        $ghes = $ten_ghe['ghe'];
                        echo '<span class="choosen-place" style="background:#ffd564; color:#4c4145; font-weight:bold;">' . implode(', ', $ghes) . '</span>';

                        // Tạo các hidden input cho mỗi ghế
                        foreach ($ghes as $ghe) {
                            echo '<input type="hidden" name="ten_ghe[]" value="' . htmlspecialchars($ghe) . '">';
                        }
                    }
                    ?>
                </div>
            </div>
            
            <div style="display: flex; margin-bottom: 10px; align-items: center;">
                <span style="color: #fff;"><i class="fa fa-coffee" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Combo đã chọn:") ?></span>
                <div class="check-doan" id="selected-combos-display" style="margin-left: 10px;">
                    <!-- Combos will be dynamically added here -->
                </div>
            </div>

            <div class="tongtien">
                <div class="checked-result">
                    <span style="color: #fff;"><?= __("Tổng cộng:") ?></span>
                    <input name="giaghe" style="width: 120px; font-size: 20px; border: none; background: transparent; color: #fe505a; font-weight: bold;" type="text" id="gia_ghe"
                           value="<?php 
                           // Get seat price from session
                           $seat_price = $_SESSION['tong']['gia_ghe'] ?? 0;
                           echo $seat_price; 
                           ?>" readonly> VND
                </div>
            </div>
        </div>
    </div>

    <div class="booking-pagination">
        <a href="index.php?act=datve2&id=<?php echo $_SESSION['tong']['id_phim'] ?>">
            <span class="quaylai"><?= __("QUAY LẠI") ?></span>
        </a>
        <a href="#">
            <input type="submit" name="tiep_tuc" class="booking-pagination__button" value="<?= __("TIẾP TỤC") ?>" style="background:#ffd564; color:#4c4145;">
        </a>
    </div>
</form>

<div class="clearfix"></div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize total price from seat selection
        var priceElement = document.getElementById('gia_ghe');
        var seatPrice = parseInt(priceElement?.value || 0);
        var comboTotal = 0;
        
        // Object to store combo quantities
        var comboQuantities = {};
        
        // Function to update total price display
        function updateTotalPrice() {
            var totalPrice = seatPrice + comboTotal;
            $('#gia_ghe').val(totalPrice);
            $('[name="giaghe"]').val(totalPrice);
        }
        
        // Function to update combo display
        function updateComboDisplay() {
            var displayHtml = '';
            var hasCombo = false;
            
            // Clear existing hidden inputs
            $('#selected-combos-display').empty();
            
            // Build display and hidden inputs
            $.each(comboQuantities, function(comboName, data) {
                if (data.quantity > 0) {
                    hasCombo = true;
                    displayHtml += '<span class="choosen-place" style="margin: 5px; padding: 5px 10px; background: #ffd564; color: #4c4145; font-weight: bold; border-radius: 15px; display: inline-block;">' + 
                                  comboName + ' x' + data.quantity + 
                                  '</span>';
                    
                    // Add multiple hidden inputs (one for each quantity)
                    for (var i = 0; i < data.quantity; i++) {
                        $('#selected-combos-display').append(
                            '<input type="hidden" name="ten_do_an[]" value="' + comboName + '">'
                        );
                    }
                }
            });
            
            if (!hasCombo) {
                displayHtml = '<span style="color: #999; font-style: italic;">' + '<?= __("Chưa chọn combo nào") ?>' + '</span>';
            }
            
            $('#selected-combos-display').html($('#selected-combos-display').html() + displayHtml);
        }
        
        // Handle increase button
        $('.btn-increase').on('click', function(e) {
            e.preventDefault();
            
            var container = $(this).closest('.quantity-control');
            var comboName = container.data('combo-name');
            var comboPrice = parseInt(container.data('combo-price')) || 0;
            var quantityDisplay = container.find('.quantity-display');
            var currentQty = parseInt(quantityDisplay.text()) || 0;
            var indicator = container.siblings('.combo-selected-indicator');
            
            // Increase quantity
            currentQty++;
            quantityDisplay.text(currentQty);
            
            // Update indicator
            indicator.find('.selected-count').text(currentQty);
            indicator.show();
            
            // Update combo quantities object
            if (!comboQuantities[comboName]) {
                comboQuantities[comboName] = {
                    price: comboPrice,
                    quantity: 0
                };
            }
            comboQuantities[comboName].quantity = currentQty;
            
            // Update combo total
            comboTotal += comboPrice;
            
            // Update displays
            updateTotalPrice();
            updateComboDisplay();
            
            // Visual feedback
            $(this).css('transform', 'scale(1.2)');
            setTimeout(() => {
                $(this).css('transform', 'scale(1)');
            }, 200);
        });
        
        // Handle decrease button
        $('.btn-decrease').on('click', function(e) {
            e.preventDefault();
            
            var container = $(this).closest('.quantity-control');
            var comboName = container.data('combo-name');
            var comboPrice = parseInt(container.data('combo-price')) || 0;
            var quantityDisplay = container.find('.quantity-display');
            var currentQty = parseInt(quantityDisplay.text()) || 0;
            var indicator = container.siblings('.combo-selected-indicator');
            
            // Decrease quantity (minimum 0)
            if (currentQty > 0) {
                currentQty--;
                quantityDisplay.text(currentQty);
                
                // Update indicator
                if (currentQty > 0) {
                    indicator.find('.selected-count').text(currentQty);
                } else {
                    indicator.hide();
                }
                
                // Update combo quantities object
                if (comboQuantities[comboName]) {
                    comboQuantities[comboName].quantity = currentQty;
                }
                
                // Update combo total
                comboTotal -= comboPrice;
                
                // Update displays
                updateTotalPrice();
                updateComboDisplay();
                
                // Visual feedback
                $(this).css('transform', 'scale(1.2)');
                setTimeout(() => {
                    $(this).css('transform', 'scale(1)');
                }, 200);
            }
        });
        
        // Prevent form submission if no seats selected
        $('form').on('submit', function(e) {
            var seatInputs = $('input[name="ten_ghe[]"]');
            if (seatInputs.length === 0) {
                e.preventDefault();
                alert('<?= __("Vui lòng chọn ghế trước khi tiếp tục!") ?>');
                return false;
            }
        });
    });
</script>
