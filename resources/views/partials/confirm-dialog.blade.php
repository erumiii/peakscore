<dialog id="confirmDialog" class="w-[400px] max-w-[90vw] rounded-xl border border-canvas bg-white p-6 backdrop:bg-ink/40">
    <h2 class="text-base font-semibold">Please confirm</h2>
    <p id="confirmMessage" class="mt-2 text-sm text-ink/80"></p>
    <form method="dialog" class="mt-5 flex justify-end gap-2">
        <button value="" class="rounded-lg border border-line bg-white px-4 py-2 text-sm font-medium text-ink hover:bg-black/5">Cancel</button>
        <button value="true" class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">Confirm</button>
    </form>
</dialog>

<script>
    let pendingConfirmForm = null;
    document.addEventListener('submit', e => {
        const src = e.target.closest('[data-confirm]');
        const form = src && (src.form || src.closest('form'));
        if (!form || confirmDialog.open) return;
        e.preventDefault();
        pendingConfirmForm = form;
        confirmMessage.textContent = src.dataset.confirm;
        confirmDialog.showModal();
    });
    confirmDialog.addEventListener('close', () => {
        const form = pendingConfirmForm;
        pendingConfirmForm = null;
        if (form && confirmDialog.returnValue === 'true') form.submit();
    });
</script>
