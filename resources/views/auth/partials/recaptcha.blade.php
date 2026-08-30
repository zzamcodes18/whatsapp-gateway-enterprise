@php
    $recaptchaEnabled = \App\Services\RecaptchaService::isEnabled();
    $recaptchaSiteKey = \App\Models\SystemSetting::get('recaptcha_site_key');
@endphp
@if($recaptchaEnabled)
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}" async defer></script>
    <script>
        (function() {
            const siteKey = '{{ $recaptchaSiteKey }}';

            // Ambil token & injeksi ke semua form auth di halaman ini
            function refreshTokens() {
                if (typeof grecaptcha === 'undefined') return;
                grecaptcha.ready(function() {
                    grecaptcha.execute(siteKey, { action: '{{ $action ?? 'auth' }}' }).then(function(token) {
                        document.querySelectorAll('.recaptcha-token').forEach(function(input) {
                            input.value = token;
                        });
                    });
                });
            }

            // Token pertama saat load, lalu refresh tiap 90 detik (token Google hanya berlaku 2 menit)
            refreshTokens();
            setInterval(refreshTokens, 90000);

            // Refresh token saat form akan disubmit (token selalu fresh)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const tokenInput = form.querySelector('.recaptcha-token');
                if (tokenInput && typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(function() {
                        grecaptcha.execute(siteKey, { action: '{{ $action ?? 'auth' }}' }).then(function(token) {
                            tokenInput.value = token;
                            form.submit();
                        });
                    });
                    e.preventDefault();
                }
            }, true);
        })();
    </script>
@endif
