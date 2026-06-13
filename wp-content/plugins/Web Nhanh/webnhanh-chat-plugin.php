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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd"/></svg>
            </a>
            <a href="<?php echo esc_url($zalo_link); ?>" class="cta-button-child-v4 zalo" target="_blank" rel="nofollow" title="Chat Zalo">
                <img src="https://webnhanh.net/wp-content/uploads/2025/07/zalo-webnhanh.png" alt="Zalo">
            </a>
        </div>

        <!-- Nút chính -->
        <div class="main-cta-button-v4">
            <span class="main-icon-v4">Liên hệ</span>
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

        /* ICON ĐIỆN THOẠI MÀU XANH */
        .cta-button-child-v4.phone svg {
            width: 26px !important;
            height: 26px !important;
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
