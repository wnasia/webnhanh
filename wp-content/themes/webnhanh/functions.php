<?php
// ── WOOCOMMERCE: SUPPRESS ADMIN NOTICES, SETUP WIZARD, COMING SOON ──

// Remove dashboard meta boxes
add_action('admin_init', function () {
    remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
    remove_meta_box('wc_admin_dashboard_setup',             'dashboard', 'normal');
    remove_meta_box('woocommerce_dashboard_status',         'dashboard', 'normal');
});
add_action('wp_dashboard_setup', function () {
    remove_meta_box('wc_admin_dashboard_setup',             'dashboard', 'normal');
    remove_meta_box('woocommerce_dashboard_recent_reviews', 'dashboard', 'normal');
}, 99);

// Disable WC Admin onboarding/task-list React widget via feature flags
add_filter('woocommerce_admin_features', function ($features) {
    $remove = ['onboarding', 'onboarding-tasks', 'remote-inbox-notifications', 'marketing'];
    return array_values(array_diff($features, $remove));
});

// Suppress all WC admin notices
add_filter('woocommerce_helper_suppress_admin_notices',  '__return_true');
add_filter('woocommerce_show_admin_notice',              '__return_false', 99);
add_filter('woocommerce_allow_marketplace_suggestions',  '__return_false');
add_filter('woocommerce_hide_admin_bar_wc_menu',         '__return_true');
add_filter('woocommerce_navigation_is_connected_page',   '__return_false');
// This is the actual filter checked inside base_tables_missing_notice()
add_filter('woocommerce_hide_base_tables_missing_nag',   '__return_true');

// Aggressively block WC base_tables_missing notice — unhook the check itself
add_action('init', function () {
    // Remove the hook that re-adds the notice on every admin page load
    remove_action('in_admin_header', ['WC_Install', 'check_base_tables_on_admin']);
    // Also delete the stored missing-table list and clear the notice flag
    delete_option('woocommerce_schema_missing_tables');
    if (class_exists('WC_Admin_Notices')) {
        WC_Admin_Notices::remove_notice('base_tables_missing');
        WC_Admin_Notices::remove_notice('update');
        WC_Admin_Notices::remove_notice('install');
    }
}, 5);

// Belt-and-suspenders: remove from notices list just before WC outputs them
add_action('admin_notices', function () {
    if (class_exists('WC_Admin_Notices')) {
        WC_Admin_Notices::remove_notice('base_tables_missing');
    }
    // Clear stored notices
    $notices = get_option('woocommerce_admin_notices', []);
    if (!empty($notices)) {
        update_option('woocommerce_admin_notices', []);
    }
}, 1);

// Remove setup wizard submenu
add_action('admin_menu', function () {
    remove_submenu_page('woocommerce', 'wc-admin&path=/setup-wizard');
}, 999);

// Disable coming soon / store launch mode
add_filter('woocommerce_coming_soon',    '__return_false');
add_filter('woocommerce_store_pages_only', '__return_false');

// Ensure coming_soon stays off on every request
add_action('init', function () {
    if (get_option('woocommerce_coming_soon') !== 'no') {
        update_option('woocommerce_coming_soon', 'no');
    }
});
// ── END SUPPRESS ──

// ── IMAGE QUALITY FIXES ──

// JPEG quality 90 (WordPress default is 82)
add_filter('jpeg_quality',           fn() => 90);
add_filter('wp_editor_set_quality',  fn() => 90);

// Remove tiny srcset entries (< 150px wide) — prevents browser picking icon-sized thumbs
add_filter('wp_calculate_image_srcset', function ($sources) {
    foreach ($sources as $w => $src) {
        if ($w < 150) {
            unset($sources[$w]);
        }
    }
    return $sources;
}, 10, 1);

// For WC product loop: use full image when thumbnail is too small vs display size
add_filter('woocommerce_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    // Remove broken srcset — let browser use the src alone for small source images
    $meta = wp_get_attachment_metadata($attachment->ID);
    $full_w = $meta['width'] ?? 0;
    if ($full_w > 0 && $full_w < 500) {
        // Source too small for reliable srcset — use full image directly
        $attr['src']    = wp_get_attachment_url($attachment->ID);
        $attr['srcset'] = '';
        $attr['sizes']  = '';
    }
    return $attr;
}, 10, 3);

// Add WC product image sizes after WC loads
add_action('after_setup_theme', function () {
    // Remove the tiny menu-icon sizes from WC product image context
    remove_image_size('menu-24x24');
    remove_image_size('menu-36x36');
    remove_image_size('menu-48x48');
}, 20);
// ── END IMAGE QUALITY FIXES ──

// ── WOOCOMMERCE: CATALOG-ONLY MODE (no cart, no prices, no checkout) ──
add_filter('woocommerce_is_purchasable',                    '__return_false');
add_filter('woocommerce_get_price_html',                    '__return_empty_string');
add_filter('woocommerce_widget_cart_is_hidden',             '__return_true');
add_filter('woocommerce_loop_add_to_cart_link',             '__return_empty_string');
add_filter('woocommerce_prevent_automatic_wizard_redirect', '__return_true');

add_action('woocommerce_after_shop_loop_item', function () {
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}, 1);

add_action('wp', function () {
    if (!function_exists('is_cart')) return;
    // Redirect commerce pages to homepage
    if (is_cart() || is_checkout() || is_account_page()) {
        wp_redirect(home_url('/'));
        exit;
    }
    // Remove single-product commerce elements
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
});
// ── END CATALOG-ONLY MODE ──

// Load các file chức năng riêng
require_once get_stylesheet_directory() . '/webnhanh-whitelist-links.php';
// Load OG/Twitter setup (Web Nhanh)
require_once get_stylesheet_directory() . '/og-tags.php';

// tự thêm alt khi hình k có alt
require_once get_theme_file_path('/auto-alt.php');

// Tắt oEmbed trong WordPress
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
// Deferred to 'init' to ensure $GLOBALS['wp_embed'] is initialized before dereference
add_action('init', function () {
    if ( ! empty( $GLOBALS['wp_embed'] ) ) {
        remove_filter( 'the_content', [ $GLOBALS['wp_embed'], 'autoembed' ], 8 );
    }
});
add_filter('embed_oembed_discover', '__return_false');

add_filter('wp_get_attachment_image_attributes', function($attr, $attachment) {
    // Nếu ảnh có URL chứa từ "Website.webp" thì bỏ lazy loading
    if (isset($attr['src']) && strpos($attr['src'], 'Website.webp') !== false) {
        unset($attr['loading']); // Xóa thuộc tính lazy
    }
    return $attr;
}, 10, 2);

// TẮT EMOJI TRIỆT ĐỂ
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('emoji_svg_url', '__return_false');

// TẮT RSS FEED HOÀN TOÀN
function disable_all_feeds() {
    wp_die(__('Feed hiện đang bị tắt. Vui lòng quay lại <a href="' . esc_url(home_url('/')) . '">trang chủ</a>.'));
}
add_action('do_feed', 'disable_all_feeds', 1);
add_action('do_feed_rdf', 'disable_all_feeds', 1);
add_action('do_feed_rss', 'disable_all_feeds', 1);
add_action('do_feed_rss2', 'disable_all_feeds', 1);
add_action('do_feed_atom', 'disable_all_feeds', 1);
add_action('do_feed_rss2_comments', 'disable_all_feeds', 1);
add_action('do_feed_atom_comments', 'disable_all_feeds', 1);

function webnhanh_add_google_fonts() {
    wp_enqueue_style(
        'webnhanh-google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;700&display=swap',
        false
    );
    wp_enqueue_style(
        'webnhanh-responsive',
        get_stylesheet_directory_uri() . '/assets/css/responsive.css',
        ['flatsome-main'],
        '1.0.3'
    );
}
add_action('wp_enqueue_scripts', 'webnhanh_add_google_fonts');


function webnhanh_enqueue_fontawesome() {
    wp_enqueue_style(
        'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}
add_action('wp_enqueue_scripts', 'webnhanh_enqueue_fontawesome');


/** AnDuc blog-only hooks (v7: Footer2 as sidebar, wide 1360, title 22px) */
add_action('wp_enqueue_scripts', function(){
    if (is_single() && get_post_type()==='post') {
        wp_enqueue_style('anduc-blog-v7', get_stylesheet_directory_uri().'/assets/css/blog.css', [], '7.0.5');
    }
}, 20);

add_filter('single_template', function($tpl){
    if (is_single() && get_post_type()==='post') {
        $t = get_stylesheet_directory().'/single-post-anduc.php';
        if (file_exists($t)) return $t;
    }
    return $tpl;
}, 20);


/** === v7.2: Post views counter (simple) === */
function anduc_touch_post_view($post_id = null){
    if (!$post_id) return;
    $key = 'anduc_post_views';
    $count = (int) get_post_meta($post_id, $key, true);
    $count++;
    update_post_meta($post_id, $key, $count);
}
add_action('template_redirect', function(){
    if (is_single() && get_post_type()==='post') {
        $id = get_queried_object_id();
        if ($id) anduc_touch_post_view($id);
    }
});

add_action('wp_footer', function () {
    if (is_admin()) return; // tránh chạy trong admin
    ?>
    <script id="demote-extra-h1">
    document.addEventListener('DOMContentLoaded', function () {
        var h1s = document.querySelectorAll('h1');
        for (var i = 1; i < h1s.length; i++) {
            var h1 = h1s[i];
            var h2 = document.createElement('h2');
            for (var j = 0; j < h1.attributes.length; j++) {
                var a = h1.attributes[j];
                h2.setAttribute(a.name, a.value);
            }
            h2.setAttribute('data-demoted','1');
            h2.innerHTML = h1.innerHTML;
            h1.parentNode.replaceChild(h2, h1);
        }
    });
    </script>
    <?php
}, 999);

// HARD REMOVE LOST PASSWORD FEATURE
add_action('login_init', function() {
    if ( isset($_GET['action']) && $_GET['action'] === 'lostpassword' ) {
        wp_die('Tính năng khôi phục mật khẩu đã bị vô hiệu hoá.', 'Không hỗ trợ', array('response' => 410));
    }
});

// Disable WP reset password system
add_filter('allow_password_reset', '__return_false');

// Remove lost password URL being generated by plugins/themes
add_filter('lostpassword_url', function($url){
    return home_url('/');
});

// Fix Organization country code in Rank Math JSON-LD
add_filter( 'rank_math/json_ld', 'webnhanh_fix_organization_country_code' );
function webnhanh_fix_organization_country_code( $data ) {
    if ( isset( $data['Organization']['address']['addressCountry'] ) && $data['Organization']['address']['addressCountry'] === 'Việt Nam' ) {
        $data['Organization']['address']['addressCountry'] = 'VN';
    }
    return $data;
}

/* ============================================================
 *  ẨN POPUP LOGIN KHỎI FRONT-END — dùng CSS thay ob_start()
 *  ob_start() cũ buffer toàn bộ trang (~100% RAM overhead/request).
 *  CSS display:none hiệu quả hơn và không ảnh hưởng server-side.
 *  Nếu muốn xóa hoàn toàn: tắt "Login" popup trong Flatsome > Header Builder.
 * ============================================================ */
add_action('wp_head', function () {
    if ( is_admin() ) return;
    echo '<style>#login-form-popup{display:none!important}</style>' . "\n";
}, 1);

/* ============================================
 *  FORM LIÊN HỆ WEBNHANH – GỬI MAIL + AUTO-REPLY
 *  - User gửi: bắt Họ tên, Liên hệ, Nội dung (min 25 ký tự ở HTML)
 *  - Gửi mail cho admin (int.vnus@gmail.com)
 *  - Nếu Liên hệ là email hợp lệ → gửi auto-reply cho khách
 *  - Admin luôn nhận được (To) và có thể xem trong Bcc header
 * ============================================ */

add_action('admin_post_nopriv_wn_send_mail', 'wn_handle_mail');
add_action('admin_post_wn_send_mail', 'wn_handle_mail');

/**
 * Auto-inject nonce hidden field into any form that posts to wn_send_mail.
 * Works even when the form HTML lives in the database (page builder content).
 */
add_action('wp_footer', function () {
    if ( is_admin() ) return;
    $nonce = wp_create_nonce('wn_send_mail_action');
    ?>
    <script>
    (function () {
        document.querySelectorAll('form').forEach(function (form) {
            var action = form.querySelector('[name="action"]');
            if (action && action.value === 'wn_send_mail') {
                if (!form.querySelector('[name="wn_mail_nonce"]')) {
                    var input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = 'wn_mail_nonce';
                    input.value = <?php echo wp_json_encode($nonce); ?>;
                    form.appendChild(input);
                }
            }
        });
    })();
    </script>
    <?php
}, 5);

function wn_handle_mail(){

    // CSRF protection — verify nonce before processing any data
    if ( ! isset( $_POST['wn_mail_nonce'] ) ||
         ! wp_verify_nonce( $_POST['wn_mail_nonce'], 'wn_send_mail_action' ) ) {
        wp_die( 'Security check failed. Please go back and try again.', 'Forbidden', [ 'response' => 403 ] );
    }

    // Lấy dữ liệu từ form
    $name    = isset($_POST['wn_name'])    ? sanitize_text_field($_POST['wn_name'])    : '';
    $contact = isset($_POST['wn_contact']) ? sanitize_text_field($_POST['wn_contact']) : '';
    $message = isset($_POST['wn_message']) ? sanitize_textarea_field($_POST['wn_message']) : '';

    // Email admin
    $admin_email = "int.vnus@gmail.com";

    // ============================
    // 1) GỬI MAIL VỀ CHO ADMIN
    // ============================

    $subject_admin = "Khách hàng liên hệ từ WebNhanh";

    $body_admin  = "Họ tên: $name\n";
    $body_admin .= "Liên hệ: $contact\n\n";
    $body_admin .= "Nội dung:\n$message\n";

    $headers_admin = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: Web Nhanh <no-reply@' . $_SERVER['SERVER_NAME'] . '>',
        // Bcc cho chính admin để luôn lưu vết
        'Bcc: ' . $admin_email,
    );

    // Gửi mail admin
    wp_mail($admin_email, $subject_admin, $body_admin, $headers_admin);

    // ============================
    // 2) AUTO-REPLY CHO KHÁCH (NẾU CONTACT LÀ EMAIL HỢP LỆ)
    // ============================

    if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {

        $subject_reply = "Web Nhanh đã nhận được yêu cầu của bạn";

        $body_reply =
"Chào $name,

Web Nhanh đã nhận được thông tin liên hệ của bạn.

Nội dung bạn đã gửi:
$message

Chúng tôi sẽ phản hồi trong thời gian sớm nhất.

Trân trọng,
Web Nhanh";

        $headers_reply = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: Web Nhanh <no-reply@' . $_SERVER['SERVER_NAME'] . '>',
        );

        wp_mail($contact, $subject_reply, $body_reply, $headers_reply);
    }

    // ============================
    // 3) Redirect về trang cảm ơn / liên hệ
    // ============================

    wp_redirect(home_url('/lien-he/?sent=1'));
    exit;
}

// ── LITESPEED: EXCLUDE ZOOM BUTTON FROM UCSS ──
add_filter('litespeed_optm_ucss_whitelist', function($list) {
    $list[] = '.zoom-button';
    $list[] = '.button.circle';
    $list[] = '.button.circle.icon';
    return $list;
});

add_action('wp_head', function() {
    echo '<style id="zoom-button-fix">
a.zoom-button.button.is-outline.circle.icon,
.zoom-button.button.circle.icon,
a[href="#product-zoom"] {
    width: 36px !important;
    height: 36px !important;
    min-height: 36px !important;
    max-height: 36px !important;
    line-height: 36px !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}
</style>';
}, 999);


