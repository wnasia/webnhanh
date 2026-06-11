(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('wn-tuvan-overlay');
        var popup   = overlay ? overlay.querySelector('.wn-tuvan-popup') : null;
        var closeBtn = overlay ? overlay.querySelector('.wn-tuvan-close') : null;
        var form    = document.getElementById('wn-tuvan-form');
        var success = form ? form.querySelector('.wn-tuvan-success') : null;
        var error   = form ? form.querySelector('.wn-tuvan-error') : null;
        var submitBtn = form ? form.querySelector('.wn-tuvan-submit') : null;

        if (!overlay || !form) return;

        function openPopup(e) {
            if (e) e.preventDefault();
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
        }

        function closePopup() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
        }

        // Trigger: header button "Tư vấn ngay"
        document.querySelectorAll('#wn-tuvan-trigger').forEach(function (btn) {
            btn.addEventListener('click', openPopup);
        });

        // Close on overlay click (but not when clicking inside the popup)
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePopup();
        });

        // Close on X button
        if (closeBtn) closeBtn.addEventListener('click', closePopup);

        // Close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('is-open')) closePopup();
        });

        // AJAX submit
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (success) success.hidden = true;
            if (error) error.hidden = true;
            if (submitBtn) submitBtn.disabled = true;

            var formData = new FormData(form);

            fetch(wnTuVanData.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data && data.success) {
                        if (success) success.hidden = false;
                        form.reset();
                    } else {
                        if (error) error.hidden = false;
                    }
                })
                .catch(function () {
                    if (error) error.hidden = false;
                })
                .finally(function () {
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    });
})();
