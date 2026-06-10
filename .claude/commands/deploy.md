# Deploy Command — Tkwn

## Pre-Deploy Checklist
- [ ] Không có `var_dump()`, `print_r()`, `error_log()` debug còn sót
- [ ] `wp-config.php` KHÔNG trong git staged files
- [ ] WooCommerce catalog-only filters còn đủ trong `functions.php`
- [ ] Test local tại `http://localhost:81/Tkwn/` — không lỗi 500

## Deploy Steps

### 1. Push lên GitHub
```bash
cd D:\Data\Working\Tkwn
git add -A
git commit -m "type: mô tả"
git push origin main
```

### 2. Deploy lên host qua cPanel
1. Đăng nhập cPanel
2. **Git Version Control** → repo `webnhanh` → **Manage**
3. **Update from Remote** — pull commit mới nhất từ GitHub
4. **Deploy HEAD Commit** — chạy `.cpanel.yml`

### 3. Verify sau deploy
- Mở `https://webnhanh.net` — site bình thường
- Kiểm tra không lỗi 500
- Verify `wp-config.php` timestamp không đổi (File Manager)

## .cpanel.yml làm gì
```
rsync: themes/webnhanh/    → public_html/wp-content/themes/webnhanh/
rsync: themes/flatsome/    → public_html/wp-content/themes/flatsome/
rsync: plugins/Web Nhanh/  → public_html/wp-content/plugins/Web Nhanh/
rsync: mu-plugins/         → public_html/wp-content/mu-plugins/
```
**KHÔNG đụng**: `wp-config.php`, `.htaccess`, `uploads/`

## Rollback
```bash
git revert HEAD
git push origin main
# Sau đó deploy lại trên cPanel
```
