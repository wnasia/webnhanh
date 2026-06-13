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

// Load parent theme (Flatsome) stylesheet
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}, 5);

// Preconnect tới Google Fonts & Font Awesome CDN để giảm render-blocking, hỗ trợ LCP
add_filter('wp_resource_hints', function($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = ['href' => 'https://fonts.googleapis.com'];
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
        $urls[] = ['href' => 'https://cdnjs.cloudflare.com'];
    }
    return $urls;
}, 10, 2);

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
        file_exists(get_stylesheet_directory() . '/assets/css/responsive.css')
            ? filemtime(get_stylesheet_directory() . '/assets/css/responsive.css')
            : '1.0.4'
    );
}
add_action('wp_enqueue_scripts', 'webnhanh_add_google_fonts');


// Font Awesome đã được thay bằng SVG inline — đảm bảo không nguồn nào load lại
function webnhanh_dequeue_fontawesome() {
    wp_dequeue_style('font-awesome');
    wp_dequeue_style('fontawesome');
    wp_dequeue_style('font-awesome-6');
    wp_deregister_style('font-awesome');
    wp_deregister_style('fontawesome');
    wp_deregister_style('font-awesome-6');
}
add_action('wp_enqueue_scripts', 'webnhanh_dequeue_fontawesome', 100);


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


/** === v7.2: Post views counter (simple, rate-limited bằng cookie) === */
function anduc_touch_post_view($post_id = null){
    if (!$post_id) return;

    // Mỗi visitor chỉ tính 1 view / bài / 12h — tránh bloat post meta khi reload liên tục
    $cookie_name = 'anduc_viewed_' . $post_id;
    if ( isset( $_COOKIE[$cookie_name] ) ) return;
    setcookie( $cookie_name, '1', time() + 12 * HOUR_IN_SECONDS, '/' );

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

/**
 * Flatsome's flatsome_archive_title() (hooked on flatsome_before_blog) renders
 * <h1 class="page-title is-large uppercase"> for category/tag/search/author/date
 * archives. On other request types (pages, posts page, post-type archives) the
 * conditions inside archive-title.php all fail and it still prints an EMPTY h1,
 * which combines with the page's own title h1 to create multiple H1 tags.
 * Remove that hook for any request where it would render empty.
 */
add_action('wp_head', function () {
    $is_supported_archive = is_category() || is_tag() || is_search() || is_author()
        || is_date() || is_tax('post_format');

    if ( ! $is_supported_archive ) {
        remove_action('flatsome_before_blog', 'flatsome_archive_title', 15);
    }
});

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

/* ============================================
 *  ẨN POPUP LOGIN KHỎI FRONT-END
 *  CSS display:none đặt trong assets/css/responsive.css
 *  (#login-form-popup) — không ảnh hưởng server-side.
 *  Nếu muốn xóa hoàn toàn: tắt "Login" popup trong Flatsome > Header Builder.
 * ============================================ */

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

    wn_send_contact_emails($name, $contact, $message);

    // Redirect về trang cảm ơn / liên hệ
    wp_redirect(home_url('/lien-he/?sent=1'));
    exit;
}

/**
 * Gửi mail liên hệ cho admin + auto-reply cho khách (nếu contact là email hợp lệ).
 * Dùng chung bởi wn_handle_mail (form trang liên hệ) và wn_handle_mail_ajax (popup tư vấn).
 */
function wn_send_contact_emails($name, $contact, $message, $email = '') {

    // Email admin
    $admin_email = "int.vnus@gmail.com";

    // Domain cho header From: — lấy từ site URL, không dùng $_SERVER['SERVER_NAME'] (có thể bị giả mạo qua Host header)
    $site_domain = wp_parse_url( home_url(), PHP_URL_HOST );

    // ============================
    // 1) GỬI MAIL VỀ CHO ADMIN
    // ============================

    $subject_admin = "Khách hàng liên hệ từ WebNhanh";

    $body_admin  = "Họ tên: $name\n";
    $body_admin .= "Liên hệ: $contact\n\n";
    $body_admin .= "Nội dung:\n$message\n";

    $headers_admin = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: Web Nhanh <no-reply@' . $site_domain . '>',
        // Bcc cho chính admin để luôn lưu vết
        'Bcc: ' . $admin_email,
    );

    // Gửi mail admin
    wp_mail($admin_email, $subject_admin, $body_admin, $headers_admin);

    // ============================
    // 2) AUTO-REPLY CHO KHÁCH
    //    Ưu tiên $email (field email riêng nếu có), fallback về $contact
    //    nếu $contact tự nó là email hợp lệ.
    // ============================

    $reply_to = '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reply_to = $email;
    } elseif (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
        $reply_to = $contact;
    }

    if ($reply_to) {

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
            'From: Web Nhanh <no-reply@' . $site_domain . '>',
        );

        wp_mail($reply_to, $subject_reply, $body_reply, $headers_reply);
    }
}

/* ============================================
 *  POPUP "TƯ VẤN NGAY" — header button mở form liên hệ qua AJAX
 * ============================================ */

// Enqueue CSS + JS cho popup
add_action('wp_enqueue_scripts', function () {
    if (is_admin()) return;

    $css_path = get_stylesheet_directory() . '/assets/css/popup-tuvan.css';
    $js_path  = get_stylesheet_directory() . '/assets/js/popup-tuvan.js';

    wp_enqueue_style(
        'webnhanh-popup-tuvan',
        get_stylesheet_directory_uri() . '/assets/css/popup-tuvan.css',
        [],
        file_exists($css_path) ? filemtime($css_path) : '1.0.0'
    );

    wp_enqueue_script(
        'webnhanh-popup-tuvan',
        get_stylesheet_directory_uri() . '/assets/js/popup-tuvan.js',
        [],
        file_exists($js_path) ? filemtime($js_path) : '1.0.0',
        true
    );

    wp_localize_script('webnhanh-popup-tuvan', 'wnTuVanData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
}, 20);

// Render popup markup vào footer
add_action('wp_footer', 'webnhanh_render_tuvan_popup');
function webnhanh_render_tuvan_popup() {
    if (is_admin()) return;
    $nonce = wp_create_nonce('wn_send_mail_action');
    ?>
    <div id="wn-tuvan-overlay" class="wn-tuvan-overlay" aria-hidden="true">
        <div class="wn-tuvan-popup" role="dialog" aria-modal="true" aria-labelledby="wn-tuvan-title">
            <button type="button" class="wn-tuvan-close" aria-label="Đóng">&times;</button>
            <h3 id="wn-tuvan-title">Tư vấn ngay</h3>
            <form id="wn-tuvan-form" class="wn-tuvan-form" method="post">
                <p class="wn-tuvan-field">
                    <label for="wn-tuvan-name">Họ tên</label>
                    <input type="text" id="wn-tuvan-name" name="wn_name" required>
                </p>
                <p class="wn-tuvan-field">
                    <label for="wn-tuvan-phone">Số điện thoại</label>
                    <input type="tel" id="wn-tuvan-phone" name="wn_contact" required>
                </p>
                <p class="wn-tuvan-field">
                    <label for="wn-tuvan-email">Email</label>
                    <input type="email" id="wn-tuvan-email" name="wn_email" placeholder="Email của bạn (để nhận tư vấn)">
                </p>
                <p class="wn-tuvan-field">
                    <label for="wn-tuvan-message">Nhu cầu</label>
                    <textarea id="wn-tuvan-message" name="wn_message" rows="4" required></textarea>
                </p>
                <input type="hidden" name="action" value="wn_send_mail_ajax">
                <input type="hidden" name="wn_mail_nonce" value="<?php echo esc_attr( $nonce ); ?>">
                <button type="submit" class="wn-tuvan-submit">Gửi</button>
                <p class="wn-tuvan-success" hidden><?php echo esc_html( 'Cảm ơn! Chúng tôi sẽ liên hệ sớm.' ); ?></p>
                <p class="wn-tuvan-error" hidden><?php echo esc_html( 'Đã có lỗi xảy ra. Vui lòng thử lại.' ); ?></p>
            </form>
        </div>
    </div>
    <?php
}

// AJAX handler cho popup tư vấn
add_action('wp_ajax_wn_send_mail_ajax', 'wn_handle_mail_ajax');
add_action('wp_ajax_nopriv_wn_send_mail_ajax', 'wn_handle_mail_ajax');
function wn_handle_mail_ajax() {

    // CSRF protection — verify nonce before processing any data
    if ( ! isset( $_POST['wn_mail_nonce'] ) ||
         ! wp_verify_nonce( $_POST['wn_mail_nonce'], 'wn_send_mail_action' ) ) {
        wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
    }

    $name    = isset($_POST['wn_name'])    ? sanitize_text_field($_POST['wn_name'])    : '';
    $contact = isset($_POST['wn_contact']) ? sanitize_text_field($_POST['wn_contact']) : '';
    $email   = isset($_POST['wn_email'])   ? sanitize_email($_POST['wn_email'])        : '';
    $message = isset($_POST['wn_message']) ? sanitize_textarea_field($_POST['wn_message']) : '';

    if ( $name === '' || $contact === '' || $message === '' ) {
        wp_send_json_error( [ 'message' => 'Vui lòng điền đầy đủ thông tin.' ], 400 );
    }

    // Email là field tùy chọn — nếu có nhập thì phải hợp lệ
    if ( $email !== '' && ! is_email($email) ) {
        wp_send_json_error( [ 'message' => 'Email không hợp lệ.' ], 400 );
    }

    wn_send_contact_emails($name, $contact, $message, $email);

    wp_send_json_success( [ 'message' => 'Cảm ơn! Chúng tôi sẽ liên hệ sớm.' ] );
}

// ── LITESPEED: EXCLUDE ZOOM BUTTON FROM UCSS ──
add_filter('litespeed_optm_ucss_whitelist', function($list) {
    $list[] = '.zoom-button';
    $list[] = 'a.zoom-button';
    $list[] = '.button.circle';
    $list[] = '.button.circle.icon';
    $list[] = '.icon-expand';
    $list[] = '.icon-expand:before';
    $list[] = '.pswp__button';
    $list[] = '.pswp__button--zoom';
    $list[] = '.pswp--zoom-allowed .pswp__button--zoom';
    $list[] = '.pswp--zoomed-in .pswp__button--zoom';
    return $list;
});

// Zoom-button + PhotoSwipe fix giờ nằm trong assets/css/responsive.css
// (selectors .zoom-button, .button.circle, .pswp__button — xem ZOOM BUTTON FIX)

