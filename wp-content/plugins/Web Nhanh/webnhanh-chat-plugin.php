<?php
/**
 * Plugin Name:       Tiện ích Chat
 * Plugin URI:        https://www.webnhanh.net/
 * Description:       Tiện ích chat zalo, phone, không background – style tối giản
 * Version:           1.5
 * Author:            Web Nhanh
 * Author URI:        Thiết Kế Web Nhanh
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// HTML
function final_cta_buttons_html_v4() {
    $phone_number = '0918795578';
    $zalo_link    = 'https://zalo.me/0918795578';
    ?>
    <div class="cta-container-v4">
        
        <!-- Nút con -->
        <div class="cta-child-buttons-v4">
            <a href="tel:<?php echo esc_attr($phone_number); ?>" class="cta-button-child-v4 phone" target="_blank" rel="nofollow" title="Gọi điện">
                <i class="fa-solid fa-phone"></i>
            </a>
            <a href="<?php echo esc_url($zalo_link); ?>" class="cta-button-child-v4 zalo" target="_blank" rel="nofollow" title="Chat Zalo">
                <img src="https://webnhanh.net/wp-content/uploads/2025/07/zalo-webnhanh.png" alt="Zalo">
            </a>
        </div>

        <!-- Nút chính -->
        <div class="main-cta-button-v4">
            <span class="main-icon-v4">Liên hệ</span>
            <i class="fa-solid fa-xmark close-icon-v4"></i>
        </div>

    </div>
    <?php
}
add_action('wp_footer', 'final_cta_buttons_html_v4');

// CSS – xoá toàn bộ nền, bo góc, thêm màu xanh thương hiệu
function final_cta_buttons_css_v4() {
    ?>
    <style>
        .cta-container-v4 {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1000;
        }

        /* XÓA NỀN TẤT CẢ */
        .main-cta-button-v4,
        .cta-button-child-v4,
        .cta-button-child-v4.phone,
        .cta-button-child-v4.zalo {
            background: transparent !important;
            background-color: transparent !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            width: auto !important;
            height: auto !important;
            padding: 0 !important;
        }

        /* CHỮ LIÊN HỆ MÀU XANH */
        .main-icon-v4 {
            font-size: 14px;
            font-weight: 600;
            color: #0f75bd !important; /* màu xanh WebNhanh */
            position: relative !important;
            opacity: 1 !important;
            transform: none !important;
        }

        /* Ẩn icon X */
        .close-icon-v4 {
            display: none !important;
        }

        /* ICON ĐIỆN THOẠI MÀU XANH */
        .cta-button-child-v4.phone i {
            font-size: 26px !important;
            color: #0f75bd !important;
        }

        /* ICON ZALO */
        .cta-button-child-v4.zalo img {
            width: 28px !important;
            height: 28px !important;
        }

        /* KHỐI CÁC NÚT CON */
        .cta-child-buttons-v4 {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all .25s ease;
        }

        /* Hover mở nút con */
        .cta-container-v4:hover .cta-child-buttons-v4 {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* TẮT HOÀN TOÀN HIỆU ỨNG HOVER NÚT CHÍNH */
        .cta-container-v4:hover .main-cta-button-v4 {
            transform: none !important;
            background: transparent !important;
        }
        .cta-container-v4:hover .main-icon-v4 {
            opacity: 1 !important;
            transform: none !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'final_cta_buttons_css_v4', 999); // ưu tiên max
