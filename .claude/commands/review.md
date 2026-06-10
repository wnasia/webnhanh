# Code Review Command — Tkwn

## functions.php Review

- [ ] Catalog-only filters còn đủ (purchasable, price_html, cart_hidden, add_to_cart_link)
- [ ] Không có `__return_true` trên `woocommerce_is_purchasable`
- [ ] Nonce verify trong `wn_handle_mail()` trước khi đọc `$_POST`
- [ ] Tất cả `$_POST` values đều được sanitize
- [ ] Không có hardcoded paths

## Theme Files Review

- [ ] Override Flatsome dùng child theme path (`get_stylesheet_directory()`)
- [ ] CSS override dùng đúng specificity, hạn chế `!important`
- [ ] Prefix `webnhanh_` cho tất cả custom functions
- [ ] Không sửa file trong `themes/flatsome/`

## Plugin Web Nhanh Review

- [ ] Prefix `wn_` cho tất cả functions
- [ ] `defined('ABSPATH') || exit;` ở đầu file
- [ ] Output đều được escape (esc_url, esc_attr, esc_html)

## SEO Review

- [ ] Không có fake aggregateRating JSON-LD
- [ ] Organization country code là 'VN' (không phải 'Việt Nam')
- [ ] OG tags đặt trong `og-tags.php`, không inline

## Git Review

- [ ] `wp-config.php` không trong staged files
- [ ] Không có file `.tmp`, `.bak`, debug files
- [ ] Commit message theo convention (feat/fix/style/chore/seo)
