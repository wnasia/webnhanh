<?php
if (!defined('ABSPATH')) exit;

/**
 * AUTO ALT - Web Nhanh
 * Tự động thêm thuộc tính alt cho ảnh nếu thiếu, rỗng, hoặc alt vô nghĩa.
 * Không ghi đè alt đã có nội dung hợp lệ.
 */

// ==========================
// 1. TẠO ALT TEXT TỰ ĐỘNG
// ==========================
function wn_get_alt_text($attachment_id = 0, $fallback = '') {
    if ($attachment_id) {
        $saved_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if ($saved_alt && !preg_match('/^\d+$/', $saved_alt) && !preg_match('/^(image|img|photo|anh|hinh)$/i', $saved_alt)) {
            return wp_strip_all_tags($saved_alt);
        }

        $att = get_post($attachment_id);
        if ($att && $att->post_title && !preg_match('/^\d+$/', $att->post_title) && !preg_match('/^(image|img|photo|anh|hinh)$/i', $att->post_title)) {
            return wp_strip_all_tags($att->post_title);
        }
    }

    if (!$fallback) $fallback = is_singular() ? get_the_title() : get_bloginfo('name');
    return wp_strip_all_tags($fallback . ' - Web Nhanh');
}

// ==========================
// 2. THÊM ALT TRONG HTML NỘI DUNG
// ==========================
function wn_add_alt_in_html($html, $title = '') {
    if (stripos($html, '<img') === false) return $html;

    // Thêm alt nếu thiếu
    $html = preg_replace_callback('/<img(?![^>]*alt=)([^>]*)>/i', function ($m) use ($title) {
        $tag = $m[0];
        $attID = 0;
        if (preg_match('/wp-image-([0-9]+)/i', $tag, $mm)) $attID = intval($mm[1]);
        $alt = wn_get_alt_text($attID, $title);
        return rtrim($tag, '>') . ' alt="' . esc_attr($alt) . '">';
    }, $html);

    // Thay alt="" hoặc alt rác (vô nghĩa)
    $html = preg_replace_callback('/<img([^>]*?)\balt=(["\'])(.*?)\2([^>]*)>/i', function ($m) use ($title) {
        $tag = $m[0];
        $alt_now = trim($m[3]);
        $attID = 0;
        if (preg_match('/wp-image-([0-9]+)/i', $tag, $mm)) $attID = intval($mm[1]);

        // Nếu alt trống, toàn số, hoặc vô nghĩa → thay
        if ($alt_now === '' || preg_match('/^\d+$/', $alt_now) || preg_match('/^(image|img|photo|anh|hinh)$/i', $alt_now)) {
            $alt = wn_get_alt_text($attID, $title);
            return preg_replace('/\balt=(["\'])(.*?)\1/i', 'alt="' . esc_attr($alt) . '"', $tag);
        }

        return $tag;
    }, $html);

    return $html;
}

// ==========================
// 3. ÁP DỤNG CHO NỘI DUNG BÀI VIẾT
// ==========================
function wn_filter_the_content_auto_alt($content) {
    if (is_admin()) return $content;
    return wn_add_alt_in_html($content, is_singular() ? get_the_title() : '');
}

// ==========================
// 4. ÁP DỤNG CHO ẢNH GỌI BẰNG HÀM WP_GET_ATTACHMENT_IMAGE()
// ==========================
function wn_filter_attachment_attrs_alt($attr, $attachment, $size) {
    $alt = isset($attr['alt']) ? trim($attr['alt']) : '';

    // Nếu alt trống, toàn số, hoặc vô nghĩa → thay bằng alt chuẩn
    if ($alt === '' || preg_match('/^\d+$/', $alt) || preg_match('/^(image|img|photo|anh|hinh)$/i', $alt)) {
        $attr['alt'] = wn_get_alt_text($attachment->ID);
    }

    return $attr;
}

// ==========================
// 5. ÁP DỤNG CHO ẢNH ĐẠI DIỆN (THUMBNAIL)
// ==========================
function wn_filter_thumbnail_html_alt($html, $post_id, $thumb_id, $size, $attr) {
    // Nếu alt rỗng hoặc vô nghĩa → thay
    if (!preg_match('/alt=["\']([^"\']*)["\']/', $html, $match) ||
        preg_match('/^\d+$/', $match[1]) ||
        preg_match('/^(image|img|photo|anh|hinh)$/i', $match[1])) {
        
        $alt = wn_get_alt_text($thumb_id, get_the_title($post_id));
        return preg_replace(
            '/<img\s(?![^>]*\balt=)([^>]*src=)([\'"][^\'"]+[\'"])([^>]*)>/i',
            '<img $1$2 alt="' . esc_attr($alt) . '"$3>',
            $html
        );
    }

    return $html;
}

// ==========================
// 6. GẮN FILTER TỰ ĐỘNG
// ==========================
add_filter('the_content', 'wn_filter_the_content_auto_alt', 99);
add_filter('wp_get_attachment_image_attributes', 'wn_filter_attachment_attrs_alt', 10, 3);
add_filter('post_thumbnail_html', 'wn_filter_thumbnail_html_alt', 10, 5);
