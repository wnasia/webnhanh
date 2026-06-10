# Git Workflow — Tkwn

## Commit Convention

| Type | Khi nào |
|------|---------|
| `feat:` | Thêm tính năng mới |
| `fix:` | Sửa bug |
| `style:` | Chỉnh CSS/UI, không đổi logic |
| `refactor:` | Cải thiện code, không đổi chức năng |
| `chore:` | Config, deploy, dọn dẹp |
| `seo:` | Thay đổi liên quan SEO, OG tags, JSON-LD |
| `docs:` | Cập nhật tài liệu |

## Files KHÔNG BAO GIỜ commit
```
wp-config.php
.env
wp-content/uploads/
wp-content/cache/
wp-content/upgrade/
vendor/
node_modules/
*.log
```

## Quy trình chuẩn mỗi task
```bash
# 1. Làm xong task
# 2. Kiểm tra không có file nhạy cảm
git status

# 3. Commit
git add -A
git commit -m "type: mô tả ngắn gọn"
git push origin main
```

## Deploy lên production sau khi push
1. cPanel → Git Version Control → webnhanh → Manage
2. **Update from Remote**
3. **Deploy HEAD Commit**
4. `.cpanel.yml` tự chạy rsync lên `public_html`

## Files được deploy bởi .cpanel.yml
- `wp-content/themes/webnhanh/` → production
- `wp-content/themes/flatsome/` → production
- `wp-content/plugins/Web Nhanh/` → production
- `wp-content/mu-plugins/` → production (nếu có)

## Files KHÔNG bị deploy (an toàn)
- `wp-config.php` — giữ nguyên trên server
- `.htaccess` — giữ nguyên trên server
- `wp-content/uploads/` — không có trong repo
