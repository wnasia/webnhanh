# Cleanup Checklist — Tkwn

## Quy tắc bắt buộc SAU KHI hoàn thành bất kỳ task nào

### 1. Không để file tạm trong git
```bash
git status
```
Xóa bất kỳ file `.tmp`, `.bak`, file test tạm thời trước khi commit.

### 2. Không sửa file của parent theme
Kiểm tra: mọi thay đổi phải nằm trong `wp-content/themes/webnhanh/` hoặc `wp-content/plugins/Web Nhanh/`. KHÔNG đụng `themes/flatsome/`.

### 3. Kiểm tra WooCommerce không bị bật commerce UI
Sau khi sửa `functions.php`, verify các filter sau vẫn còn:
- `woocommerce_is_purchasable` → `__return_false`
- `woocommerce_get_price_html` → `__return_empty_string`
- `woocommerce_loop_add_to_cart_link` → `__return_empty_string`

### 4. Không commit file nhạy cảm
```bash
git status | grep -E "wp-config|.env|.htaccess"
```
Nếu thấy `wp-config.php` hoặc `.env` trong staged files → `git reset HEAD <file>` ngay.

### 5. Cập nhật CLAUDE.md nếu có thay đổi lớn
| Thay đổi | Cần cập nhật |
|---|---|
| Thêm file PHP mới vào theme | Architecture section trong CLAUDE.md |
| Thêm plugin mới | Danh sách plugins trong CLAUDE.md |
| Thêm module mới | Bảng Modules trong CLAUDE.md |
| Thay đổi deploy workflow | Deploy section + `.claude/rules/git-workflow.md` |

### 6. Commit và push — BƯỚC CUỐI BẮT BUỘC
```bash
git add -A
git commit -m "type: mô tả"
git push origin main
```
Báo lại commit hash để có thể deploy lên production.

---

## Checklist nhanh trước khi báo "hoàn thành"
```
[ ] Không có file tạm trong git status
[ ] Không đụng themes/flatsome/
[ ] WooCommerce catalog-only filters vẫn còn trong functions.php
[ ] wp-config.php không bị stage
[ ] CLAUDE.md cập nhật (nếu có thay đổi lớn)
[ ] git add -A && git commit && git push xong
[ ] Báo commit hash
```
