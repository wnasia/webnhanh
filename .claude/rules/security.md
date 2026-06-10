# Security Rules — Tkwn

## Đã xử lý trong security audit (không làm lại)
- AWS IAM key đã purge + revoke
- `vendor/aws/` SDK (~40MB) đã xóa
- SQL dump public đã xóa
- `phpinfo()` exposure đã patch
- CSRF protection đã thêm vào contact form
- Email injection đã fix
- Fake aggregateRating JSON-LD đã xóa

## Bắt buộc khi thêm form mới
```php
// 1. Tạo nonce field trong form
wp_nonce_field('action_name', 'nonce_field_name');

// 2. Verify trong handler — TRƯỚC KHI đọc bất kỳ $_POST nào
if (!wp_verify_nonce($_POST['nonce_field_name'], 'action_name')) {
    wp_die('Security check failed.', 'Forbidden', ['response' => 403]);
}

// 3. Sanitize tất cả input
$value = sanitize_text_field($_POST['field']);
```

## Files nhạy cảm — KHÔNG commit
```
wp-config.php     — DB credentials
.env              — environment variables
.htaccess         — server config
```

## Email security
Contact form đã có:
- Nonce verification
- Input sanitization
- Email injection prevention (sanitize_text_field trên headers)

## Password reset
Đã tắt hoàn toàn (`allow_password_reset` → false, `lostpassword_url` → homepage).

## Login popup
Ẩn bằng CSS (`#login-form-popup { display:none !important }`) — không xóa HTML để không break Flatsome builder.
