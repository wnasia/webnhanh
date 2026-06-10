# Code Style — Tkwn

## PHP / WordPress Patterns

### Hook pattern chuẩn
```php
// Dùng anonymous function cho hooks đơn giản
add_filter('woocommerce_is_purchasable', '__return_false');

// Dùng named function khi cần remove_action sau này
function webnhanh_fix_something() { ... }
add_action('wp_head', 'webnhanh_fix_something');
```

### Prefix bắt buộc
Tất cả function, hook, filter custom dùng prefix:
- `webnhanh_` cho theme functions
- `wn_` cho plugin Web Nhanh

```php
// Đúng
function webnhanh_fix_organization_country_code($data) { ... }
function wn_handle_mail() { ... }

// Sai
function fix_country_code($data) { ... }
```

### Sanitize / Escape — BẮT BUỘC
```php
// Input: luôn sanitize
$name    = sanitize_text_field($_POST['wn_name']);
$message = sanitize_textarea_field($_POST['wn_message']);

// Output: luôn escape
echo esc_html($name);
echo esc_url($url);
echo esc_attr($attr);
echo wp_kses_post($html);
```

### Nonce verification
```php
// Tạo nonce
wp_create_nonce('action_name');

// Verify trước khi process — KHÔNG bỏ qua
if (!wp_verify_nonce($_POST['nonce_field'], 'action_name')) {
    wp_die('Security check failed.', 'Forbidden', ['response' => 403]);
}
```

### Config access
```php
// Đúng — dùng WordPress options/constants
get_option('option_name');
defined('ABSPATH') || exit;

// Sai — không hardcode paths
'/var/www/html/wp-content/...'
```

---

## CSS Patterns

### Specificity cho override Flatsome
```css
/* Dùng selector cụ thể hơn thay vì !important khi có thể */
.webnhanh-theme .element { ... }

/* !important chỉ dùng khi override inline styles của builder */
.zoom-button { width: 36px !important; }
```

### File nào chứa gì
| File | Nội dung |
|------|---------|
| `style.css` | Theme header + base overrides nhỏ |
| `assets/css/responsive.css` | Mobile responsive overrides |
| `assets/css/blog.css` | Blog single post (AnDuc v7) styles |
| `functions.php` (inline) | Critical CSS cần load trước (zoom button, login popup) |

### Không tạo thêm file CSS mới khi chưa cần
Ưu tiên: đặt vào file CSS đã có phù hợp nhất. Tạo file mới chỉ khi scope đủ lớn (>50 lines) và độc lập.

---

## Template Patterns

### Child theme override
```
Flatsome template: flatsome/woocommerce/...
Child override:    webnhanh/woocommerce/...
```

### Template hierarchy
Luôn dùng `get_stylesheet_directory()` cho child theme paths:
```php
require_once get_stylesheet_directory() . '/inc/something.php';
```

---

## WooCommerce Rules
Xem `.claude/rules/woocommerce.md` — đây là file quan trọng nhất với project này.
