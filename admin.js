(() => {
    const toast = document.querySelector('[data-toast]');
    let timer;
    document.querySelectorAll('[data-status-type]').forEach((select) => {
        select.addEventListener('change', async () => {
            const nextStatus = select.value;
            select.disabled = true;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({
                        action: select.dataset.statusType,
                        id: Number(select.dataset.recordId),
                        status: nextStatus,
                        csrf: select.dataset.csrf
                    })
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Status update failed.');
                toast.textContent = result.message;
                toast.classList.add('is-visible');
                window.clearTimeout(timer);
                timer = window.setTimeout(() => toast.classList.remove('is-visible'), 2400);
            } catch (error) {
                toast.textContent = error.message || 'Status update failed. Please try again.';
                toast.classList.add('is-visible', 'is-error');
                window.setTimeout(() => toast.classList.remove('is-visible', 'is-error'), 3000);
            } finally {
                select.disabled = false;
            }
        });
    });
})();
