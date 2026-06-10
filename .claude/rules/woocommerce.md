# WooCommerce Rules — Tkwn

## Nguyên tắc cốt lõi

**WooCommerce tồn tại CHỈ để dùng shortcode `[ux_products]` của Flatsome để hiển thị portfolio.**

Site KHÔNG bán hàng. KHÔNG có cart. KHÔNG có checkout. KHÔNG có giá.

---

## Filters đã được bật — KHÔNG BAO GIỜ XÓA

```php
// Catalog-only — giữ nguyên trong functions.php
add_filter('woocommerce_is_purchasable',          '__return_false');
add_filter('woocommerce_get_price_html',           '__return_empty_string');
add_filter('woocommerce_widget_cart_is_hidden',    '__return_true');
add_filter('woocommerce_loop_add_to_cart_link',    '__return_empty_string');
```

---

## Redirect commerce pages
```php
// Đã có trong functions.php — giữ nguyên
if (is_cart() || is_checkout() || is_account_page()) {
    wp_redirect(home_url('/'));
    exit;
}
```

---

## Khi làm task liên quan WooCommerce

### ĐƯỢC phép
- Thêm/sửa product (portfolio items) trong WP Admin
- Sửa WooCommerce product templates để hiển thị đẹp hơn
- Thêm custom fields cho product qua ACF
- Sửa `[ux_products]` shortcode parameters

### KHÔNG được phép
- Bật lại cart, checkout, prices, add-to-cart
- Cài thêm WooCommerce payment gateway plugin
- Xóa catalog-only filters trong `functions.php`
- Tạo WooCommerce account pages

---

## Zoom button fix
Đã có CSS inline trong `functions.php` (hook `wp_head`, priority 999).
Nếu zoom button bị vỡ layout → kiểm tra CSS `.button.circle` có `aspect-ratio: 1/1` và `width/height: 36px`.
