// ==========================================
// LOAN PAYMENT SYSTEM - WITH MODAL NOTIFICATIONS
// ==========================================

// Helper functions for modals
function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('paymentSuccessModal'));
    modal.show();
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('paymentErrorModal'));
    modal.show();
}

function showInfoModal(title, message, onClose = null) {
    document.getElementById('infoModalTitle').textContent = title;
    document.getElementById('infoMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('infoModal'));

    if (onClose) {
        const closeBtn = document.getElementById('infoModalCloseBtn');
        closeBtn.onclick = function () {
            onClose();
            modal.hide();
        };
    }

    modal.show();
}

function showConfirmModal(message, onConfirm) {
    document.getElementById('confirmMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('paymentConfirmModal'));

    const confirmBtn = document.getElementById('confirmPaymentAction');
    confirmBtn.onclick = function () {
        modal.hide();
        onConfirm();
    };

    modal.show();
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log('Loan payment script loaded');

    // ==========================================
    // VIEW LOAN DETAILS MODAL
    // ==========================================
    const viewLoanButtons = document.querySelectorAll('.view-loan-btn');
    console.log('Found ' + viewLoanButtons.length + ' view loan buttons');

    viewLoanButtons.forEach(button => {
        button.addEventListener('click', function () {
            const loanId = this.dataset.loanId;
            console.log('Loading loan details for loan ID:', loanId);

            const loadingEl = document.getElementById('loanDetailsLoading');
            const contentEl = document.getElementById('loanDetailsContent');

            if (loadingEl) loadingEl.classList.remove('d-none');
            if (contentEl) contentEl.classList.add('d-none');

            fetch('php/get_loan_details.php?loan_id=' + loanId)
                .then(res => {
                    console.log('Response status:', res.status);
                    if (!res.ok) {
                        throw new Error('HTTP error! status: ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Loan details received:', data);

                    if (loadingEl) loadingEl.classList.add('d-none');
                    if (contentEl) contentEl.classList.remove('d-none');

                    if (!data.success) {
                        showErrorModal(data.error || 'Unknown error');
                        return;
                    }

                    // Populate loan info
                    const loan = data.loan;
                    document.getElementById('ld-loan-id').textContent = loan.loan_id || 'N/A';
                    document.getElementById('ld-loan-type').textContent = loan.loan_type || 'N/A';
                    document.getElementById('ld-status').textContent = loan.Status || 'N/A';
                    document.getElementById('ld-date').textContent = loan.application_date || 'N/A';
                    document.getElementById('ld-reason').textContent = loan.reason || 'N/A';
                    document.getElementById('ld-total').textContent = '₱' + (loan.total_amount || '0.00');
                    document.getElementById('ld-paid').textContent = '₱' + (loan.paid || '0.00');
                    document.getElementById('ld-balance').textContent = '₱' + (loan.balance || '0.00');

                    // Populate payment breakdown
                    const tbody = document.getElementById('paymentBreakdownTable');
                    if (tbody) {
                        tbody.innerHTML = '';

                        if (data.payments && data.payments.length > 0) {
                            data.payments.forEach((p, index) => {
                                const statusClass = p.status === 'Paid' ? 'text-success' : 'text-warning';
                                tbody.innerHTML += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${p.due_date || 'N/A'}</td>
                                        <td>₱${p.payment_amount || '0.00'}</td>
                                        <td class="fw-bold ${statusClass}">
                                            ${p.status || 'N/A'}
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No payment records found</td></tr>';
                        }
                    }
                })
                .catch(err => {
                    console.error('Error loading loan details:', err);
                    if (loadingEl) loadingEl.classList.add('d-none');
                    showErrorModal('Failed to load loan details.\n\nError: ' + err.message);
                });
        });
    });

    // ==========================================
    // PAY LOAN MODAL
    // ==========================================
    let payableData = null;

    const payNowButtons = document.querySelectorAll('[data-bs-target="#payLoanModal"]');
    console.log('Found ' + payNowButtons.length + ' pay now buttons');

    payNowButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const loanId = this.dataset.loanId;
            console.log('Opening payment modal for loan ID:', loanId);

            // Reset all radio buttons
            const payMonthlyRadio = document.getElementById('payMonthly');
            const payEarlyRadio = document.getElementById('payEarly');
            const payFullRadio = document.getElementById('payFull');

            if (payMonthlyRadio) payMonthlyRadio.checked = false;
            if (payEarlyRadio) payEarlyRadio.checked = false;
            if (payFullRadio) payFullRadio.checked = false;

            // Reset inputs
            const paymentAmountInput = document.getElementById('paymentAmount');
            if (paymentAmountInput) paymentAmountInput.value = '';

            const paymentInfoDiv = document.getElementById('paymentInfo');
            if (paymentInfoDiv) paymentInfoDiv.innerHTML = 'Loading...';

            const userBalanceDiv = document.getElementById('userBalanceInfo');
            if (userBalanceDiv) userBalanceDiv.innerHTML = '';

            // Hide all payment options initially
            const currentMonthOption = document.getElementById('currentMonthOption');
            const earlyOption = document.getElementById('earlyPaymentOption');

            if (currentMonthOption) currentMonthOption.style.display = 'none';
            if (earlyOption) earlyOption.style.display = 'none';

            const confirmBtn = document.getElementById('confirmPaymentBtn');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Payment';
            }

            // Fetch payment data
            fetch('php/get_payable_payment.php?loan_id=' + loanId)
                .then(res => {
                    console.log('Payment data response status:', res.status);
                    if (!res.ok) {
                        throw new Error('HTTP error! status: ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Payment data received:', data);

                    if (!data.success) {
                        if (data.all_paid) {
                            showInfoModal('Loan Paid', 'This loan has been fully paid!', function () {
                                const modalEl = document.getElementById('payLoanModal');
                                if (modalEl) {
                                    const modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();
                                }
                            });
                            return;
                        }
                        showErrorModal(data.error || 'Unknown error');
                        return;
                    }

                    payableData = data;

                    // Set loan ID
                    document.getElementById('pay-loan-id').value = loanId;

                    let defaultSelected = false;

                    // Show current month option if available
                    if (data.current) {
                        console.log('Current payment available:', data.current);
                        if (currentMonthOption) currentMonthOption.style.display = 'block';

                        if (!defaultSelected) {
                            document.getElementById('pay-payment-id').value = data.current.payment_id;

                            if (paymentAmountInput) {
                                paymentAmountInput.value = data.current.payment_amount;
                            }

                            if (paymentInfoDiv) {
                                paymentInfoDiv.innerHTML = `
                                    Paying for: <strong>${data.current.label}</strong><br>
                                    Due Date: ${data.current.due_date}
                                `;
                            }

                            if (payMonthlyRadio) {
                                payMonthlyRadio.checked = true;
                                defaultSelected = true;
                            }
                        }
                    } else {
                        console.log('No current payment available (all future payments)');
                        if (currentMonthOption) currentMonthOption.style.display = 'none';
                    }

                    // Show early payment option if available
                    if (data.early) {
                        console.log('Early payment available:', data.early);
                        if (earlyOption) earlyOption.style.display = 'block';

                        // If no current payment, default to early
                        if (!defaultSelected) {
                            document.getElementById('pay-payment-id').value = data.early.payment_id;

                            if (paymentAmountInput) {
                                paymentAmountInput.value = data.early.payment_amount;
                            }

                            if (paymentInfoDiv) {
                                paymentInfoDiv.innerHTML = `
                                    Paying for: <strong>${data.early.label}</strong><br>
                                    Due Date: ${data.early.due_date}
                                `;
                            }

                            if (payEarlyRadio) {
                                payEarlyRadio.checked = true;
                                defaultSelected = true;
                            }
                        }
                    } else {
                        console.log('No early payment available');
                        if (earlyOption) earlyOption.style.display = 'none';
                    }

                    // If neither current nor early available, default to full payment
                    if (!defaultSelected && payFullRadio) {
                        payFullRadio.checked = true;
                        if (paymentAmountInput) {
                            paymentAmountInput.value = data.full_balance;
                        }
                        if (paymentInfoDiv) {
                            paymentInfoDiv.innerHTML = `
                                Paying: <strong>Full Loan Balance</strong><br>
                                This will pay off all remaining payments
                            `;
                        }
                    }

                    // Set user balance info
                    if (userBalanceDiv) {
                        userBalanceDiv.innerHTML = `
                            Your Balance: <strong>₱${data.user_balance}</strong><br>
                            Remaining Loan Balance: <strong>₱${data.full_balance}</strong>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Error loading payment data:', err);
                    showErrorModal('Failed to load payment information.\n\nError: ' + err.message);
                });
        });
    });

    // ==========================================
    // PAYMENT TYPE CHANGE
    // ==========================================
    const paymentTypeRadios = document.querySelectorAll('input[name="paymentType"]');
    console.log('Found ' + paymentTypeRadios.length + ' payment type radios');

    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            console.log('Payment type changed to:', this.value);

            if (!payableData) {
                console.warn('Payment data not loaded yet');
                return;
            }

            const input = document.getElementById('paymentAmount');
            const infoDiv = document.getElementById('paymentInfo');

            if (this.value === 'monthly' && payableData.current) {
                if (input) input.value = payableData.current.payment_amount;
                document.getElementById('pay-payment-id').value = payableData.current.payment_id;
                if (infoDiv) {
                    infoDiv.innerHTML = `
                        Paying for: <strong>${payableData.current.label}</strong><br>
                        Due Date: ${payableData.current.due_date}
                    `;
                }
            }

            if (this.value === 'early' && payableData.early) {
                if (input) input.value = payableData.early.payment_amount;
                document.getElementById('pay-payment-id').value = payableData.early.payment_id;
                if (infoDiv) {
                    infoDiv.innerHTML = `
                        Paying for: <strong>${payableData.early.label}</strong><br>
                        Due Date: ${payableData.early.due_date}
                    `;
                }
            }

            if (this.value === 'full') {
                if (input) input.value = payableData.full_balance;
                if (infoDiv) {
                    infoDiv.innerHTML = `
                        Paying: <strong>Full Loan Balance</strong><br>
                        This will pay off all remaining payments
                    `;
                }
            }
        });
    });

    // ==========================================
    // CONFIRM PAYMENT
    // ==========================================
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    if (confirmBtn) {
        console.log('Confirm payment button found');

        confirmBtn.addEventListener('click', function () {
            console.log('Confirm payment clicked');

            const loanId = document.getElementById('pay-loan-id').value;
            const paymentId = document.getElementById('pay-payment-id').value;
            const typeRadio = document.querySelector('input[name="paymentType"]:checked');
            const type = typeRadio ? typeRadio.value : null;
            const amount = document.getElementById('paymentAmount').value;

            console.log('Payment details:', { loanId, paymentId, type, amount });

            if (!loanId) {
                showErrorModal('Invalid loan data. Please try again.');
                return;
            }

            if (!type) {
                showErrorModal('Please select a payment type.');
                return;
            }

            if (!paymentId && type !== 'full') {
                showErrorModal('Invalid payment data. Please try again.');
                return;
            }

            if (!amount || parseFloat(amount) <= 0) {
                showErrorModal('Invalid payment amount.');
                return;
            }

            // Show confirmation modal
            showConfirmModal(`Are you sure you want to pay ₱${amount}?`, function () {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Processing...';

                fetch('php/process_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        loan_id: loanId,
                        payment_id: paymentId,
                        type: type
                    })
                })
                    .then(res => {
                        console.log('Payment response status:', res.status);
                        return res.text().then(text => {
                            console.log('Raw response:', text);
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Failed to parse JSON:', text);
                                throw new Error('Server returned invalid response. Check if process_payment.php exists and has no errors.');
                            }
                        });
                    })
                    .then(data => {
                        console.log('Payment response:', data);

                        if (data.success) {
                            showSuccessModal('Amount paid: ₱' + data.amount_paid);
                        } else {
                            showErrorModal(data.error || 'Unknown error');
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = 'Confirm Payment';
                        }
                    })
                    .catch(err => {
                        console.error('Payment error:', err);
                        showErrorModal('Payment failed. Please try again.\n\nError: ' + err.message);
                        confirmBtn.disabled = false;
                        confirmBtn.textContent = 'Confirm Payment';
                    });
            });
        });
    } else {
        console.error('Confirm payment button not found!');
    }

    console.log('Loan payment script initialization complete');
});