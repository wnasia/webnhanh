# Tkwn — WordPress Web Design Portfolio

## Project Overview
**Tkwn** (`webnhanh.net`) là WordPress site dùng làm portfolio/showcase cho dịch vụ thiết kế web. **KHÔNG phải e-commerce.** WooCommerce chỉ dùng để hiển thị portfolio qua `[ux_products]` — catalog-only, không bán hàng.

**Tech Stack:** WordPress · WooCommerce (catalog-only) · Flatsome theme (parent) · webnhanh theme (child) · Rank Math Pro · LiteSpeed Cache · Cloudflare CDN

**Local:** `http://localhost:81/Tkwn/` tại `D:\Data\Working\Tkwn`
**Production:** `https://webnhanh.net` tại `/home/eypppgfd/public_html`

---

## 🧹 Sau mỗi task — BẮT BUỘC

Xem `.claude/rules/cleanup-checklist.md` trước khi báo "hoàn thành".
- Không để file tạm (.tmp, .bak) trong git
- Cập nhật CLAUDE.md nếu thêm/xóa file quan trọng
- **Bắt buộc git add -A && git commit && git push sau mỗi task**

---

## ⚠️ Rules Tuyệt Đối — KHÔNG BAO GIỜ VI PHẠM

1. **WooCommerce catalog-only**: KHÔNG bao giờ bật cart, checkout, prices, add-to-cart. Mọi thứ đã bị suppress trong `functions.php`.
2. **KHÔNG đụng `wp-config.php`**: Chứa DB credentials production.
3. **KHÔNG đụng `.htaccess`**: Cloudflare xử lý HTTPS — không thêm Force HTTPS block (gây redirect loop).
4. **KHÔNG đụng `wp-content/uploads/`**: Media files, không track trong git.
5. **`RewriteBase` khác nhau**: Local dùng `/Tkwn/`, production dùng `/` — không nhầm lẫn.
6. **Rank Math Pro**: Chỉ dùng Pro, KHÔNG kích hoạt Free (duplicate). Free hiện đã deactivate.

---

## Architecture

```
D:\Data\Working\Tkwn\
├── wp-content/
│   ├── themes/
│   │   ├── flatsome/          ← Parent theme (KHÔNG sửa trực tiếp)
│   │   └── webnhanh/          ← Child theme (tất cả customization ở đây)
│   │       ├── functions.php  ← WooCommerce suppression, image fixes, form handler, zoom fix
│   │       ├── style.css      ← Theme header + base styles
│   │       ├── assets/css/
│   │       │   ├── responsive.css  ← Responsive overrides
│   │       │   └── blog.css        ← Blog single post styles (AnDuc v7)
│   │       ├── inc/               ← (trống, dùng để tách logic sau này)
│   │       ├── template-parts/    ← Template partials
│   │       ├── single-post-anduc.php ← Custom single post template
│   │       ├── og-tags.php        ← Open Graph / Twitter Card meta
│   │       ├── auto-alt.php       ← Auto-generate alt text cho ảnh không có alt
│   │       └── webnhanh-whitelist-links.php ← Whitelist external links
│   └── plugins/
│       ├── Web Nhanh/
│       │   └── webnhanh-chat-plugin.php  ← Chat widget (Zalo + Phone), v1.5
│       ├── woocommerce/           ← Catalog display only
│       ├── seo-by-rank-math-pro/  ← SEO (Pro version)
│       ├── litespeed-cache/       ← Cache + performance
│       ├── advanced-custom-fields/
│       ├── classic-editor/
│       ├── easy-table-of-contents/
│       ├── insert-headers-and-footers/
│       ├── menu-image/
│       └── wp-mail-smtp/
├── .github/
│   └── workflows/
│       └── deploy.yml         ← (nếu có CI/CD)
├── .cpanel.yml                ← cPanel deploy script
├── .gitignore
└── CLAUDE.md
```

---

## Modules / Phạm Vi Làm Việc

| Module | Files | Mô tả |
|--------|-------|-------|
| `theme-core` | `themes/webnhanh/functions.php`, `style.css` | WooCommerce suppression, performance hooks |
| `theme-ui` | `themes/webnhanh/assets/css/`, `template-parts/` | Giao diện, responsive |
| `blog` | `single-post-anduc.php`, `assets/css/blog.css`, `category.php`, `tag.php` | Blog single post layout AnDuc |
| `seo` | `og-tags.php`, Rank Math settings | Open Graph, JSON-LD, meta |
| `chat-plugin` | `plugins/Web Nhanh/webnhanh-chat-plugin.php` | Widget Zalo + Phone |
| `contact-form` | `functions.php` (wn_handle_mail) | Form liên hệ + auto-reply |
| `deploy` | `.cpanel.yml`, `.gitignore` | Deploy workflow |

---

## Critical Business Rules

1. **WooCommerce**: Tồn tại CHỈ để dùng `[ux_products]` shortcode của Flatsome hiển thị portfolio. Mọi commerce UI đã bị tắt trong `functions.php`.
2. **Cloudflare**: Xử lý HTTPS termination. `.htaccess` KHÔNG được có Force HTTPS block.
3. **Parent theme**: Flatsome là parent — KHÔNG sửa file trong `themes/flatsome/`. Mọi override đặt trong `themes/webnhanh/`.
4. **Image srcset**: Đã fix — bỏ entries < 150px, ảnh nguồn < 500px thì xóa hết srcset.
5. **Zoom button**: `.button.circle` cần `aspect-ratio: 1/1`, fixed 36×36px. CSS đã có trong `functions.php`.
6. **Contact form nonce**: `wn_send_mail_action` — luôn verify trước khi process.

---

## Deploy Workflow

```
Local (D:\Data\Working\Tkwn)
  → git push origin main
  → cPanel Git Version Control → webnhanh → Update from Remote → Deploy HEAD Commit
  → .cpanel.yml chạy rsync: themes/webnhanh + themes/flatsome + plugins/Web Nhanh + mu-plugins
  → KHÔNG đụng: wp-config.php, .htaccess, uploads/
```

**Không bao giờ** kết thúc task mà không push git.

---

## Quick Reference

### Local vs Production
| | Local | Production |
|---|---|---|
| URL | `http://localhost:81/Tkwn/` | `https://webnhanh.net` |
| RewriteBase | `/Tkwn/` | `/` |
| DB | local MySQL | cPanel MySQL |
| HTTPS | không | Cloudflare |

### Contact Form
- Action: `wn_send_mail`
- Admin email: `int.vnus@gmail.com`
- Auto-reply: nếu `wn_contact` là email hợp lệ
- Redirect sau gửi: `/lien-he/?sent=1`

### Chat Plugin
- Phone: `0918795578`
- Zalo: `https://zalo.me/0918795578`
- Zalo icon: hosted tại `webnhanh.asia`

### Git
- Repo: `https://github.com/wnasia/webnhanh.git`
- Branch: `main`
- cPanel path: `/home/eypppgfd/webnhanh_git`
- cPanel user: `eypppgfd`

---

## Detailed Rules (xem `.claude/rules/`)
- [Cleanup Checklist](/.claude/rules/cleanup-checklist.md) — **ĐỌC SAU MỖI TASK**
- [Git Workflow](/.claude/rules/git-workflow.md) — commit, push, deploy
- [Code Style](/.claude/rules/code-style.md) — PHP, CSS, WordPress patterns
- [WooCommerce](/.claude/rules/woocommerce.md) — catalog-only rules
- [SEO](/.claude/rules/seo.md) — Rank Math, OG tags, JSON-LD rules
- [Security](/.claude/rules/security.md) — nonce, sanitize, escaping
