document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-patient-dialog]').forEach(button => {
        button.addEventListener('click', () => document.getElementById(button.dataset.patientDialog)?.showModal());
    });
    document.querySelectorAll('[data-close-dialog]').forEach(button => {
        button.addEventListener('click', () => button.closest('dialog').close());
    });
    const errors = document.querySelector('[data-reopen-dialog]');
    const reopenedDialog = errors ? document.getElementById(errors.dataset.reopenDialog) : null;
    if (reopenedDialog instanceof HTMLDialogElement) {
        const feedback = errors.cloneNode(true);
        feedback.removeAttribute('data-reopen-dialog');
        reopenedDialog.querySelector('.patient-dialog-head').after(feedback);
        reopenedDialog.showModal();
    }
    document.querySelectorAll('[data-confirm-payment]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm(form.dataset.confirmPayment)) event.preventDefault();
        });
    });
    const service = document.querySelector('[data-payment-service]');
    const amount = document.querySelector('[data-payment-amount]');
    if (service && amount) {
        const toCents = value => {
            if (!/^\d{1,8}(?:[.,]\d{1,2})?$/.test(value)) return null;
            const [whole, fraction = ''] = value.replace(',', '.').split('.');
            return BigInt(whole) * 100n + BigInt(fraction.padEnd(2, '0'));
        };
        const validateAmount = () => {
            amount.setCustomValidity('');
            if (!amount.value) return;
            const cents = toCents(amount.value);
            const balance = toCents(service.selectedOptions[0]?.dataset.balance ?? '');
            if (cents === null || cents <= 0n) amount.setCustomValidity(amount.dataset.invalidMessage);
            else if (balance !== null && cents > balance) amount.setCustomValidity(amount.dataset.overpaymentMessage);
        };
        service.addEventListener('change', validateAmount);
        amount.addEventListener('input', validateAmount);
        validateAmount();
    }
});
