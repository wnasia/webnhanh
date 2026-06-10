<?php
/**
 * Open Graph & Twitter Card – One-time setup (Web Nhanh)
 * File: og-tags.php (đặt trong thư mục theme hoặc child theme)
 *
 * Tác dụng:
 * - Luôn xuất đủ OG + Twitter cho mọi trang/bài.
 * - Nếu có Rank Math: dùng filter để "điền" phần thiếu (không trùng thẻ).
 * - Nếu KHÔNG có Rank Math: tự in đầy đủ thẻ vào <head>.
 * - Có ảnh fallback thương hiệu nếu bài không có featured image.
 */

/* =========================
 * SETTINGS – cấu hình mặc định
 * ========================= */
if ( ! defined('WEBNHANH_OG_DEFAULT_IMAGE') ) {
    // Ảnh fallback 1200x630 (bạn đã cung cấp)
    define('WEBNHANH_OG_DEFAULT_IMAGE', 'https://webnhanh.asia/wp-content/uploads/2025/10/Thiet-ke-web-nhanh.png');
}
if ( ! defined('WEBNHANH_OG_LOCALE') ) {
    define('WEBNHANH_OG_LOCALE', 'vi_VN');
}

/**
 * Lấy dữ liệu OG dùng chung
 */
if ( ! function_exists('webnhanh_get_og_payload') ) {
    function webnhanh_get_og_payload() {
        $site_name   = 'Web Nhanh'; // cố định tên thương hiệu
        $default_img = WEBNHANH_OG_DEFAULT_IMAGE;
        $locale      = WEBNHANH_OG_LOCALE;

        if ( is_singular() ) {
            global $post;
            $title = get_the_title($post->ID);
            $raw   = $post ? $post->post_content : '';
            $desc  = has_excerpt($post->ID) ? wp_strip_all_tags(get_the_excerpt($post->ID))
                                            : wp_trim_words( wp_strip_all_tags($raw), 30 );
            $url   = get_permalink($post->ID);
            $img   = get_the_post_thumbnail_url($post->ID, 'full');
            if ( ! $img ) $img = $default_img;

            // Lấy kích thước ảnh — cached to avoid DB query on every page load
            $img_w = 1200; $img_h = 630;
            $cache_key     = 'wn_att_id_' . md5( $img );
            $attachment_id = get_transient( $cache_key );
            if ( $attachment_id === false ) {
                $attachment_id = attachment_url_to_postid( $img );
                set_transient( $cache_key, (int) $attachment_id, DAY_IN_SECONDS );
            }
            if ( $attachment_id ) {
                $image_data = wp_get_attachment_image_src( $attachment_id, 'full' );
                if ( is_array( $image_data ) ) {
                    $img_w = intval( $image_data[1] );
                    $img_h = intval( $image_data[2] );
                }
            }

            return [
                'title'  => $title,
                'desc'   => $desc,
                'url'    => $url,
                'img'    => $img,
                'img_w'  => $img_w,
                'img_h'  => $img_h,
                'site'   => $site_name,
                'type'   => 'article',
                'locale' => $locale,
                'pub'    => get_the_time('c', $post->ID),
                'mod'    => get_the_modified_time('c', $post->ID),
            ];
        }

        // Trang chủ/Archive
        return [
            'title'  => $site_name,
            'desc'   => wp_strip_all_tags( get_bloginfo('description') ),
            'url'    => home_url('/'),
            'img'    => $default_img,
            'img_w'  => 1200,
            'img_h'  => 630,
            'site'   => $site_name,
            'type'   => 'website',
            'locale' => $locale,
            'pub'    => '',
            'mod'    => '',
        ];
    }
}

/* =========================================================
 * TẦNG 1: NẾU CÓ Rank Math → dùng filter để điền phần thiếu
 * (không in trùng thẻ; ưu tiên để Rank Math render)
 * ========================================================= */
if ( defined('RANK_MATH_VERSION') ) {

    if ( ! function_exists('webnhanh_rm_fill') ) {
        function webnhanh_rm_fill( $val, $key ) {
            $p = webnhanh_get_og_payload();
            return ( empty($val) && isset($p[$key]) ) ? $p[$key] : $val;
        }
    }

    // Open Graph (Facebook/Zalo)
    add_filter('rank_math/opengraph/facebook/title',        fn($v)=>webnhanh_rm_fill($v,'title'), 10);
    add_filter('rank_math/opengraph/facebook/description',  fn($v)=>webnhanh_rm_fill($v,'desc'), 10);
    add_filter('rank_math/opengraph/facebook/url',          fn($v)=>webnhanh_rm_fill($v,'url'), 10);
    add_filter('rank_math/opengraph/facebook/image',        fn($v)=>webnhanh_rm_fill($v,'img'), 10);
    add_filter('rank_math/opengraph/facebook/site_name',    fn($v)=>webnhanh_rm_fill($v,'site'), 10);
    add_filter('rank_math/opengraph/facebook/type',         fn($v)=>webnhanh_rm_fill($v,'type'), 10);

    // Thời gian bài viết
    add_filter('rank_math/opengraph/facebook/published_time', fn($v)=>webnhanh_rm_fill($v,'pub'), 10);
    add_filter('rank_math/opengraph/facebook/modified_time',  fn($v)=>webnhanh_rm_fill($v,'mod'), 10);

    // Twitter Cards
    add_filter('rank_math/opengraph/twitter/card',          fn($v)=>($v?:'summary_large_image'), 10);
    add_filter('rank_math/opengraph/twitter/title',         fn($v)=>webnhanh_rm_fill($v,'title'), 10);
    add_filter('rank_math/opengraph/twitter/description',   fn($v)=>webnhanh_rm_fill($v,'desc'), 10);
    add_filter('rank_math/opengraph/twitter/image',         fn($v)=>webnhanh_rm_fill($v,'img'), 10);

    // Bổ sung kích thước ảnh để bot nhận chắc hơn
    add_action('wp_head', function(){
        $p = webnhanh_get_og_payload();
        echo "\n<!-- Web Nhanh: ensure og:image size for scrapers -->\n";
        echo '<meta property="og:image:width" content="'.esc_attr($p['img_w'])."\" />\n";
        echo '<meta property="og:image:height" content="'.esc_attr($p['img_h'])."\" />\n";
    }, 6);
}

/* =========================================================
 * TẦNG 2: KHÔNG có Rank Math → tự in đầy đủ OG/Twitter
 * ========================================================= */
if ( ! defined('RANK_MATH_VERSION') ) {
    add_action('wp_head', function(){
        $p = webnhanh_get_og_payload();

        static $printed = false;
        if ( $printed ) return;
        $printed = true;

        echo "\n<!-- OG/Twitter by Web Nhanh - begin -->\n";
        echo '<meta property="og:locale" content="'.esc_attr($p['locale'])."\" />\n";
        echo '<meta property="og:site_name" content="'.esc_attr($p['site'])."\" />\n";
        echo '<meta property="og:title" content="'.esc_attr($p['title'])."\" />\n";
        echo '<meta property="og:description" content="'.esc_attr($p['desc'])."\" />\n";
        echo '<meta property="og:type" content="'.esc_attr($p['type'])."\" />\n";
        echo '<meta property="og:url" content="'.esc_url($p['url'])."\" />\n";
        echo '<meta property="og:image" content="'.esc_url($p['img'])."\" />\n";
        echo '<meta property="og:image:width" content="'.esc_attr($p['img_w'])."\" />\n";
        echo '<meta property="og:image:height" content="'.esc_attr($p['img_h'])."\" />\n";
        if ( ! empty($p['pub']) ) echo '<meta property="article:published_time" content="'.esc_attr($p['pub'])."\" />\n";
        if ( ! empty($p['mod']) ) echo '<meta property="article:modified_time" content="'.esc_attr($p['mod'])."\" />\n";

        echo '<meta name="twitter:card" content="summary_large_image" />'."\n";
        echo '<meta name="twitter:title" content="'.esc_attr($p['title'])."\" />\n";
        echo '<meta name="twitter:description" content="'.esc_attr($p['desc'])."\" />\n";
        echo '<meta name="twitter:image" content="'.esc_url($p['img'])."\" />\n";
        echo "<!-- OG/Twitter by Web Nhanh - end -->\n";
    }, 5);
}
