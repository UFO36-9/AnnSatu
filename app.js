(() => {
    const menu = window.ANNSATU_MENU || [];
    const cart = new Map();
    const toast = document.querySelector('[data-toast]');
    const drawer = document.querySelector('[data-cart-drawer]');
    const overlay = document.querySelector('[data-cart-overlay]');
    const content = document.querySelector('[data-cart-content]');
    const checkout = document.querySelector('[data-cart-checkout]');
    const count = document.querySelector('.cart-count');
    const total = document.querySelector('[data-cart-total]');
    const form = document.querySelector('[data-order-form]');
    let toastTimer;

    const showToast = (message) => {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
    };

    document.querySelector('.nav-toggle')?.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const open = button.getAttribute('aria-expanded') !== 'true';
        button.setAttribute('aria-expanded', String(open));
        document.querySelector('.main-nav')?.classList.toggle('is-open', open);
    });
    document.querySelectorAll('.main-nav a').forEach((link) => link.addEventListener('click', () => {
        document.querySelector('.main-nav')?.classList.remove('is-open');
        document.querySelector('.nav-toggle')?.setAttribute('aria-expanded', 'false');
    }));

    document.querySelectorAll('.menu-tab').forEach((tab) => tab.addEventListener('click', () => {
        document.querySelectorAll('.menu-tab').forEach((item) => item.classList.toggle('is-active', item === tab));
        document.querySelectorAll('[data-menu-card]').forEach((card) => {
            card.hidden = tab.dataset.category !== 'All' && card.dataset.category !== tab.dataset.category;
        });
    }));

    const renderCart = () => {
        const quantity = [...cart.values()].reduce((sum, item) => sum + item.quantity, 0);
        count.textContent = String(quantity);
        count.classList.toggle('has-items', quantity > 0);
        if (!quantity) {
            content.innerHTML = '<div class="empty-cart"><span aria-hidden="true">♡</span><h3>Your table is waiting.</h3><p>Add something delicious from the menu to get started.</p><button class="text-link" type="button" data-close-cart>Back to the menu →</button></div>';
            checkout.hidden = true;
            return;
        }
        checkout.hidden = false;
        content.innerHTML = [...cart.entries()].map(([id, item]) => `
            <div class="cart-line">
                <div><strong>${escapeHtml(item.name)}</strong><span>$${Number(item.price).toFixed(2)} each</span>
                    <div class="quantity-control"><button type="button" data-quantity="${id}" data-delta="-1" aria-label="Remove one ${escapeHtml(item.name)}">−</button><span>${item.quantity}</span><button type="button" data-quantity="${id}" data-delta="1" aria-label="Add one ${escapeHtml(item.name)}">+</button></div>
                </div><strong>$${(item.price * item.quantity).toFixed(2)}</strong>
            </div>`).join('');
        const sum = [...cart.values()].reduce((value, item) => value + item.price * item.quantity, 0);
        total.textContent = `$${sum.toFixed(2)}`;
    };

    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
    document.querySelectorAll('[data-add-item]').forEach((button) => button.addEventListener('click', () => {
        const item = menu.find((entry) => entry.id === Number(button.dataset.addItem));
        if (!item) return;
        const existing = cart.get(item.id);
        if (existing) existing.quantity += 1;
        else cart.set(item.id, { ...item, quantity: 1 });
        renderCart();
        showToast(`${item.name} added to your order`);
    }));
    content.addEventListener('click', (event) => {
        const button = event.target.closest('[data-quantity]');
        if (!button) return;
        const item = cart.get(Number(button.dataset.quantity));
        if (!item) return;
        item.quantity += Number(button.dataset.delta);
        if (item.quantity < 1) cart.delete(Number(button.dataset.quantity));
        renderCart();
    });

    const openCart = () => {
        overlay.hidden = false;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('cart-is-open');
        drawer.querySelector('input:not([type=hidden])')?.focus();
    };
    const closeCart = () => {
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.hidden = true;
        document.body.classList.remove('cart-is-open');
    };
    document.querySelector('[data-open-cart]')?.addEventListener('click', openCart);
    document.querySelectorAll('[data-close-cart]').forEach((button) => button.addEventListener('click', closeCart));
    overlay.addEventListener('click', closeCart);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeCart();
    });

    const fulfillment = document.querySelector('[data-fulfillment]');
    const addressLabel = document.querySelector('[data-address-label]');
    fulfillment.addEventListener('change', () => {
        const delivery = fulfillment.value === 'delivery';
        addressLabel.hidden = !delivery;
        addressLabel.querySelector('input').required = delivery;
    });

    document.querySelectorAll('[data-api-form]').forEach((apiForm) => apiForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = apiForm.querySelector('[type=submit]');
        const message = apiForm.querySelector('.form-message');
        button.disabled = true;
        message.textContent = 'One moment…';
        message.classList.remove('is-error');
        try {
            const response = await fetch('api.php', { method: 'POST', body: new FormData(apiForm), headers: { Accept: 'application/json' } });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Something went wrong. Please try again.');
            message.textContent = result.reservation_id
                ? `${result.message} Keep your reservation number: ${result.reservation_id}.`
                : result.message;
            apiForm.reset();
        } catch (error) {
            message.textContent = error.message || 'We couldn’t send that just now. Please try again.';
            message.classList.add('is-error');
        } finally {
            button.disabled = false;
        }
    }));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!cart.size) return;
        const button = form.querySelector('[type=submit]');
        const message = form.querySelector('.form-message');
        button.disabled = true;
        message.textContent = 'Sending your order…';
        message.classList.remove('is-error');
        const payload = Object.fromEntries(new FormData(form));
        payload.items = [...cart.entries()].map(([id, item]) => ({ id, quantity: item.quantity }));
        try {
            const response = await fetch('api.php', { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(payload) });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'We couldn’t place that order.');
            cart.clear();
            renderCart();
            form.reset();
            addressLabel.hidden = true;
            addressLabel.querySelector('input').required = false;
            showToast(result.message);
            window.setTimeout(closeCart, 900);
        } catch (error) {
            message.textContent = error.message || 'We couldn’t place that order. Please try again.';
            message.classList.add('is-error');
        } finally {
            button.disabled = false;
        }
    });

    const dateInput = document.querySelector('input[name=date]');
    if (dateInput) {
        const today = new Date();
        const localToday = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
        dateInput.min = localToday;
    }
    renderCart();
})();
