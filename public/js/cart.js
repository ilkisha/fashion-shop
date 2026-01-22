console.log('cart.js loaded');

(function () {
    const forms = document.querySelectorAll('.js-cart-update');
    if (!forms.length) return;

    forms.forEach((form) => {
        const input = form.querySelector('.js-qty');
        const btn = form.querySelector('.js-update-btn');
        if (!input) return;

        // hide fallback button if JS works
        if (btn) btn.style.display = 'none';

        const sendUpdate = async () => {
            const qty = input.value;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: new URLSearchParams({ qty })
                });

                if (!res.ok) return;
                const data = await res.json();
                if (!data.ok) return;

                const productId = form.dataset.productId;

                const lineTotalEl = document.querySelector(
                    '.js-line-total[data-product-id="' + productId + '"]'
                );
                if (lineTotalEl) lineTotalEl.textContent = data.lineTotal;

                const totalEl = document.getElementById('js-cart-total');
                if (totalEl) totalEl.textContent = data.cartTotal;

                if (data.isRemoved) {
                    window.location.reload();
                }
            } catch (e) {
                // fallback: show button again
                if (btn) btn.style.display = '';
            }
        };

        // If user presses Enter or form tries to submit, prevent double request
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            sendUpdate();
        });

        // Better UX: only send when user finishes change (not on every keystroke)
        input.addEventListener('change', () => {
            sendUpdate();
        });
    });
})();
