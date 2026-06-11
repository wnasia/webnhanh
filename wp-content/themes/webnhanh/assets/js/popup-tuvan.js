(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var overlay   = document.getElementById('wn-tuvan-overlay');
        var closeBtn  = overlay ? overlay.querySelector('.wn-tuvan-close') : null;
        var form      = document.getElementById('wn-tuvan-form');
        var success   = form ? form.querySelector('.wn-tuvan-success') : null;
        var errorEl   = form ? form.querySelector('.wn-tuvan-error') : null;
        var submitBtn = form ? form.querySelector('.wn-tuvan-submit') : null;

        var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var PHONE_RE = /^0[0-9]{9}$/;

        if (!overlay || !form) return;

        /* ── helpers ── */
        function showError(msg) {
            if (!errorEl) return;
            errorEl.textContent = msg;
            errorEl.hidden = false;
        }
        function clearMessages() {
            if (errorEl)  errorEl.hidden  = true;
            if (success)  success.hidden  = true;
        }
        function fieldVal(id) {
            var el = form.querySelector('#' + id);
            return el ? el.value.trim() : '';
        }

        /* ── open / close ── */
        function openPopup(e) {
            if (e) e.preventDefault();
            overlay.classList.add('is-open');
        }
        function closePopup() {
            overlay.classList.remove('is-open');
        }

        document.querySelectorAll('#wn-tuvan-trigger').forEach(function (btn) {
            btn.addEventListener('click', openPopup);
        });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePopup();
        });
        if (closeBtn) closeBtn.addEventListener('click', closePopup);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('is-open')) closePopup();
        });

        /* ── validate ── */
        function validate() {
            var ten   = fieldVal('wn-tuvan-ten');
            var sdt   = fieldVal('wn-tuvan-sdt');
            var email = fieldVal('wn-tuvan-email');

            // Họ tên bắt buộc
            if (ten.length < 2) {
                showError('Vui lòng nhập họ tên (ít nhất 2 ký tự).');
                return false;
            }

            // Phải nhập ít nhất 1 trong 2: SĐT hoặc Email
            var sdtOk    = sdt   !== '' && PHONE_RE.test(sdt);
            var emailOk  = email !== '' && EMAIL_RE.test(email);

            if (sdt === '' && email === '') {
                showError('Vui lòng nhập Số điện thoại hoặc Email để chúng tôi liên hệ lại.');
                return false;
            }
            if (sdt !== '' && !sdtOk) {
                showError('Số điện thoại không hợp lệ (10 số, bắt đầu bằng 0).');
                return false;
            }
            if (email !== '' && !emailOk) {
                showError('Email không hợp lệ. Vui lòng kiểm tra lại.');
                return false;
            }
            if (!sdtOk && !emailOk) {
                showError('Vui lòng nhập đúng Số điện thoại (10 số) hoặc Email hợp lệ.');
                return false;
            }

            return true;
        }

        /* ── submit ── */
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearMessages();

            if (!validate()) return;

            if (submitBtn) submitBtn.disabled = true;

            fetch(wnTuVanData.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: new FormData(form)
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.success) {
                        if (success) success.hidden = false;
                        form.reset();
                    } else {
                        showError('Có lỗi xảy ra. Vui lòng thử lại hoặc gọi trực tiếp cho chúng tôi.');
                    }
                })
                .catch(function () {
                    showError('Có lỗi xảy ra. Vui lòng thử lại hoặc gọi trực tiếp cho chúng tôi.');
                })
                .finally(function () {
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    });
})();
