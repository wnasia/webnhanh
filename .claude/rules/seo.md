# SEO Rules — Tkwn

## Rank Math Pro
- Chỉ dùng **Pro version** (`seo-by-rank-math-pro`)
- `seo-by-rank-math` (Free) đã deactivate — KHÔNG kích hoạt lại
- Kích hoạt cả 2 cùng lúc = conflict

## JSON-LD / Structured Data

### Organization country code fix (đã có trong functions.php)
```php
add_filter('rank_math/json_ld', 'webnhanh_fix_organization_country_code');
function webnhanh_fix_organization_country_code($data) {
    if ($data['Organization']['address']['addressCountry'] === 'Việt Nam') {
        $data['Organization']['address']['addressCountry'] = 'VN';
    }
    return $data;
}
```

### KHÔNG thêm aggregateRating giả
AggregateRating JSON-LD chỉ hợp lệ khi có real reviews. Đã xóa fake structured data trong quá trình security audit — không thêm lại.

## Open Graph (og-tags.php)
File `og-tags.php` xử lý OG/Twitter Card meta tags. Load qua `functions.php`.
Khi sửa OG tags → sửa trong `og-tags.php`, không inline vào `functions.php`.

## Sitemap
Rank Math Pro tự generate sitemap. Không cần plugin sitemap riêng.

## Các tính năng đã tắt
- oEmbed (tắt trong functions.php — giảm request không cần thiết)
- RSS feed (tắt hoàn toàn — redirect về homepage)
- Emoji (tắt — giảm script load)

## Performance liên quan SEO
- LiteSpeed Cache: active, quản lý cache
- Cloudflare: CDN + HTTPS termination
- JPEG quality: 90 (tăng từ default 82)
- srcset: bỏ entries < 150px
