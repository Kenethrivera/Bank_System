// --- Tab Switching Logic ---
function showSection(sectionId, linkElement, skipAnimation = false) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(sec => sec.classList.remove('active'));
    // Remove 'active' class from all nav links
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

    // Show target section and highlight nav link
    const section = document.getElementById(sectionId);
    section.classList.add('active');
    linkElement.classList.add('active');

    // Scroll to top of the page
    if (skipAnimation) {
        window.scrollTo(0, 0); // instantly jump to top
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' }); // smooth scroll to top
        // Save active section only when user clicks
        localStorage.setItem('activeSection', sectionId);
    }
}

// --- On Page Load: Show last active section ---
document.addEventListener('DOMContentLoaded', function () {
    const lastSection = localStorage.getItem('activeSection');
    let defaultSection = 'home';

    if (lastSection && document.getElementById(lastSection)) {
        defaultSection = lastSection;
    }

    // Find the corresponding nav link
    const link = Array.from(document.querySelectorAll('.nav-link'))
        .find(l => l.getAttribute('onclick')?.includes(defaultSection));

    if (link) {
        // Load last active section instantly, skip animation to prevent flash
        showSection(defaultSection, link, true);
    }
});


// --- Balance Toggle Logic ---
// Toggles between showing the actual amount and asterisks ($****)
let isHidden = false;
const balanceElement = document.getElementById('displayBalance');
const originalBalance = balanceElement.innerText; // Store original PHP value

function toggleBalance() {
    const eyeIcon = document.getElementById('eyeIcon');
    if (isHidden) {
        // Show Numbers
        balanceElement.innerText = originalBalance;
        eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        isHidden = false;
    } else {
        // Hide with Asterisks
        balanceElement.innerText = "$****";
        eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        isHidden = true;
    }
}


// CASH IN MODAL
const cashInData = {
    "7-Eleven": [
        { step: "Go to the CLiQQ kiosk and select 'e-money'.", icon: "bi bi-geo-alt-fill" },
        { step: "Select GCash and type in your registered number.", icon: "bi bi-phone-fill" },
        { step: "Type in the amount you want to cash in and confirm. Wait for the printed receipt.", icon: "bi bi-cash-stack" },
        { step: "Present receipt to the cashier and pay.", icon: "bi bi-person-fill" },
        { step: "You will receive a text confirmation upon successful cash in.", icon: "bi bi-check-circle-fill" }
    ],
    "Alfa Mart": [
        { step: "Select cash in > Cash in on the machine.", icon: "bi bi-card-list" },
        { step: "Enter your number and cash in amount.", icon: "bi bi-phone-fill" },
        { step: "Insert cash payment.", icon: "bi bi-cash-stack" },
        { step: "Receive receipt from the machine and a text confirmation.", icon: "bi bi-receipt" }
    ],
    "Shell Select": [
        { step: "Select cash in > Cash in on the machine.", icon: "bi bi-card-list" },
        { step: "Enter your number and cash in amount.", icon: "bi bi-phone-fill" },
        { step: "Insert cash payment.", icon: "bi bi-cash-stack" },
        { step: "Receive receipt from the machine and a text confirmation.", icon: "bi bi-receipt" }
    ],
    "Uncle John's": [
        { step: "Inform the cashier that you want to cash in to your account.", icon: "bi bi-person-fill" },
        { step: "Tap on 'Generate barcode' button. Enter the amount then tap 'Generate barcode'. Show the cashier the generated barcode.", icon: "bi bi-upc-scan" },
        { step: "Cashier to confirm, collect the payment, and print receipts.", icon: "bi bi-receipt" },
        { step: "Wait for the text confirmation upon successful cash in before leaving the store.", icon: "bi bi-check-circle-fill" }
    ],
    "eTap": [
        { step: "Select cash in > Cash in on the machine.", icon: "bi bi-card-list" },
        { step: "Enter your number and cash in amount.", icon: "bi bi-phone-fill" },
        { step: "Insert cash payment.", icon: "bi bi-cash-stack" },
        { step: "Receive receipt from the machine and a text confirmation.", icon: "bi bi-receipt" }
    ],
    "Pay&Go": [
        { step: "Select cash in > Cash in on the machine.", icon: "bi bi-card-list" },
        { step: "Enter your number and cash in amount.", icon: "bi bi-phone-fill" },
        { step: "Insert cash payment.", icon: "bi bi-cash-stack" },
        { step: "Receive receipt from the machine and a text confirmation.", icon: "bi bi-receipt" }
    ],
    "TouchPay": [
        { step: "Select cash in > Cash in on the machine.", icon: "bi bi-card-list" },
        { step: "Enter your number and cash in amount.", icon: "bi bi-phone-fill" },
        { step: "Insert cash payment.", icon: "bi bi-cash-stack" },
        { step: "Receive receipt from the machine and a text confirmation.", icon: "bi bi-receipt" }
    ],
    "Sari-Sari Store": [
        { step: "Inform the cashier that you want to cash in to your account.", icon: "bi bi-person-fill" },
        { step: "Tap on 'Generate barcode' button. Enter the amount then tap 'Generate barcode'. Show the cashier the generated barcode.", icon: "bi bi-upc-scan" },
        { step: "Cashier to confirm, collect the payment, and print receipts.", icon: "bi bi-receipt" },
        { step: "Wait for the text confirmation upon successful cash in before leaving the store.", icon: "bi bi-check-circle-fill" }
    ]
};
function showCashInInstructions(storeName) {
    const modalTitle = document.getElementById("cashInModalTitle");
    const modalBody = document.getElementById("cashInModalBody");

    modalTitle.textContent = `${storeName} Cash In Instructions`;
    modalBody.innerHTML = ""; // clear previous content

    const steps = cashInData[storeName];
    if (!steps) return;

    steps.forEach((s, index) => {
        const stepHtml = `
      <div class="d-flex align-items-start mb-3">
        <div class="step-icon text-primary me-3 fs-3">
          <i class="${s.icon}"></i>
        </div>
        <div>
          <strong>Step ${index + 1}:</strong> ${s.step}
        </div>
      </div>
    `;
        modalBody.innerHTML += stepHtml;
    });

    // show the modal
    const cashInModal = new bootstrap.Modal(document.getElementById("cashInInstructionsModal"));
    cashInModal.show();
}


function selectCashOutMethod(method, description, icon) {
    document.getElementById('cashOutMethod').value = method;
    document.getElementById('cashOutDescription').value = description;
    document.getElementById('cashOutIcon').value = icon;

    // Optional: highlight selected method
    document.querySelectorAll('#cashOutModal .card').forEach(card => card.classList.remove('border-danger'));
    event.currentTarget.classList.add('border-danger', 'border-3');
}

const amountInput = document.getElementById('cashOutAmount');
const warningDiv = document.getElementById('cashOutWarning');
const cashOutForm = document.querySelector('#cashOutModal form');

amountInput.addEventListener('input', () => {
    const amount = parseFloat(amountInput.value) || 0;
    if (amount > availableBalance) {
        warningDiv.textContent = `Amount cannot be greater than available balance ($${availableBalance.toFixed(2)})`;
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
});

cashOutForm.addEventListener('submit', (e) => {
    const amount = parseFloat(amountInput.value) || 0;
    if (amount > availableBalance) {
        e.preventDefault();
        warningDiv.textContent = `Amount cannot be greater than available balance ($${availableBalance.toFixed(2)})`;
        warningDiv.style.display = 'block';
    }
});

// sendMoney 
const sendForm = document.getElementById('sendMoneyForm');
const sendAmountInput = document.getElementById('sendAmount');
const recipientInput = document.getElementById('recipientNumber');
const warningDivSend = document.getElementById('sendMoneyWarning');

const nextBtn = document.getElementById('sendMoneyNext');
const confirmAmountSpan = document.getElementById('confirmSendAmount');
const confirmRecipientSpan = document.getElementById('confirmRecipient');

function maskRecipient(number) {
    // Mask all digits except last 4
    if (number.length <= 4) return "****";
    let masked = number.slice(0, number.length - 4).replace(/\d/g, "*");
    return masked + number.slice(-4);
}

nextBtn.addEventListener('click', () => {
    const amount = parseFloat(sendAmountInput.value) || 0;
    const recipient = recipientInput.value.trim();

    if (!recipient) {
        warningDivSend.textContent = "Please enter recipient's cellphone number.";
        warningDivSend.style.display = 'block';
        return;
    }

    if (amount <= 0) {
        warningDivSend.textContent = "Enter a valid amount.";
        warningDivSend.style.display = 'block';
        return;
    }

    if (amount > balanceDisplay) {
        warningDivSend.textContent = `Amount cannot be greater than available balance ($${balanceDisplay.toFixed(2)})`;
        warningDivSend.style.display = 'block';
        return;
    }

    warningDivSend.style.display = 'none';

    confirmAmountSpan.textContent = `$${amount.toFixed(2)}`;
    confirmRecipientSpan.textContent = maskRecipient(recipient);

    const confirmModal = new bootstrap.Modal(document.getElementById('sendMoneyConfirmModal'));
    confirmModal.show();
});


// SAVINGS SECTION JAVASCRIPT
// Store user's main balance for validation
const userMainBalance = parseFloat(document.getElementById('userMainBalance').value);
/**
 * Open deposit modal with account details
 */
function openDepositModal(savingsId, savingsType, currentBalance) {
    document.getElementById('depositSavingsId').value = savingsId;
    document.getElementById('depositAccountName').textContent = savingsType;
    document.getElementById('depositAccountId').textContent = savingsId;
    document.getElementById('depositAmount').value = '';
    document.getElementById('depositWarning').style.display = 'none';

    const modal = new bootstrap.Modal(document.getElementById('depositModal'));
    modal.show();
}

/**
 * Open withdraw modal with account details
 */
function openWithdrawModal(savingsId, savingsType, currentBalance, maxWithdraw, canWithdraw) {
    document.getElementById('withdrawSavingsId').value = savingsId;
    document.getElementById('withdrawAccountName').textContent = savingsType;
    document.getElementById('withdrawAccountId').textContent = savingsId;
    document.getElementById('withdrawAvailable').textContent = '₱' + currentBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('withdrawMaxAmount').value = maxWithdraw;
    document.getElementById('withdrawSavingsType').value = savingsType;
    document.getElementById('withdrawAmount').value = '';
    document.getElementById('withdrawAmount').max = maxWithdraw;
    document.getElementById('withdrawWarning').style.display = 'none';

    // Show restriction info for Special accounts
    const restrictionInfo = document.getElementById('withdrawRestrictionInfo');
    const restrictionText = document.getElementById('withdrawRestrictionText');

    if (savingsType === 'Special') {
        restrictionText.textContent = 'Special accounts must maintain a minimum balance of ₱50,000. Maximum withdrawal: ₱' + maxWithdraw.toLocaleString('en-US', { minimumFractionDigits: 2 });
        restrictionInfo.style.display = 'block';
    } else if (savingsType === 'Fixed') {
        restrictionText.textContent = 'Fixed accounts have a 3-month lock-in period from creation date.';
        restrictionInfo.style.display = 'block';
    } else {
        restrictionInfo.style.display = 'none';
    }

    const modal = new bootstrap.Modal(document.getElementById('withdrawModal'));
    modal.show();
}
// ===== CREATE SAVINGS ACCOUNT VALIDATION =====
document.addEventListener('DOMContentLoaded', function () {
    const createSavingsForm = document.getElementById('createSavingsForm');
    const initialDepositInput = document.getElementById('initialDeposit');
    const initialDepositWarning = document.getElementById('initialDepositWarning');
    const minimumDepositLabel = document.getElementById('minimumDepositLabel');
    const savingsTypeRadios = document.querySelectorAll('input[name="savings_type"]');
    const submitButton = document.getElementById('createSavingsSubmit');
    const savingsTypeInfo = document.getElementById('savingsTypeInfo');
    const savingsTypeInfoText = document.getElementById('savingsTypeInfoText');

    // Get user's available balance
    const userMainBalance = parseFloat(document.getElementById('userMainBalance')?.value || 0);

    // Minimum deposit requirements
    const minimumDeposits = {
        'Regular': 100,
        'Fixed': 1000,
        'Special': 50000
    };

    // Account type information
    const accountInfo = {
        'Regular': 'Regular Savings: Withdraw anytime with no restrictions. Minimum deposit: ₱100. Perfect for everyday savings.',
        'Fixed': 'Fixed Savings: Cannot withdraw for 3 months after account creation. Minimum deposit: ₱1,000. Higher interest rate as a reward for commitment.',
        'Special': 'Special Savings: Must maintain minimum balance of ₱50,000. Minimum deposit: ₱50,000. Highest interest rate for high-value savers.'
    };

    let selectedSavingsType = null;

    // Disable submit button initially
    if (submitButton) {
        submitButton.disabled = true;
    }

    // Handle savings type selection
    savingsTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            selectedSavingsType = this.value;
            const minDeposit = minimumDeposits[selectedSavingsType];

            console.log('Savings type selected:', selectedSavingsType);
            console.log('Minimum deposit required:', minDeposit);

            // Update label
            if (minimumDepositLabel) {
                minimumDepositLabel.textContent = `(Minimum ₱${minDeposit.toLocaleString('en-PH', { minimumFractionDigits: 2 })})`;
                minimumDepositLabel.className = 'text-primary';
            }

            // Show account information
            if (savingsTypeInfo && savingsTypeInfoText) {
                savingsTypeInfoText.textContent = accountInfo[selectedSavingsType];
                savingsTypeInfo.style.display = 'block';
            }

            // Enable input field
            initialDepositInput.disabled = false;
            initialDepositInput.placeholder = minDeposit.toFixed(2);

            // Clear previous value and warning
            initialDepositInput.value = '';
            initialDepositWarning.style.display = 'none';
            submitButton.disabled = true;

            // Focus on the input
            initialDepositInput.focus();
        });
    });

    // Validate initial deposit amount
    function validateInitialDeposit() {
        const amount = parseFloat(initialDepositInput.value);

        // Must select savings type first
        if (!selectedSavingsType) {
            initialDepositWarning.textContent = 'Please select a savings account type first';
            initialDepositWarning.style.display = 'block';
            submitButton.disabled = true;
            return false;
        }

        const minDeposit = minimumDeposits[selectedSavingsType];

        // Empty input
        if (!initialDepositInput.value || initialDepositInput.value.trim() === '') {
            initialDepositWarning.style.display = 'none';
            submitButton.disabled = true;
            return false;
        }

        // Invalid amount
        if (isNaN(amount) || amount <= 0) {
            initialDepositWarning.textContent = 'Please enter a valid amount';
            initialDepositWarning.style.display = 'block';
            submitButton.disabled = true;
            return false;
        }

        // Below minimum deposit
        if (amount < minDeposit) {
            initialDepositWarning.textContent = `${selectedSavingsType} Savings requires minimum ₱${minDeposit.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
            initialDepositWarning.style.display = 'block';
            submitButton.disabled = true;
            return false;
        }

        // Exceeds available balance
        if (amount > userMainBalance) {
            initialDepositWarning.textContent = `Insufficient balance. Available: ₱${userMainBalance.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
            initialDepositWarning.style.display = 'block';
            submitButton.disabled = true;
            return false;
        }

        // All validations passed
        console.log('✓ All validations passed');
        initialDepositWarning.style.display = 'none';
        submitButton.disabled = false;
        return true;
    }

    // Validate on input
    if (initialDepositInput) {
        initialDepositInput.addEventListener('input', validateInitialDeposit);
        initialDepositInput.addEventListener('blur', validateInitialDeposit);
    }

    // Final validation on form submit
    if (createSavingsForm) {
        createSavingsForm.addEventListener('submit', function (e) {
            console.log('Form submit attempt');

            if (!validateInitialDeposit()) {
                console.log('❌ Form submission blocked - validation failed');
                e.preventDefault();
                e.stopPropagation();

                // Show error if trying to submit without selecting type
                if (!selectedSavingsType) {
                    initialDepositWarning.textContent = 'Please select a savings account type first';
                    initialDepositWarning.style.display = 'block';
                }

                return false;
            }

            console.log('✓ Form submission allowed');
            return true;
        });
    }

    // Reset form when modal is closed
    const createSavingsModal = document.getElementById('createSavingsModal');
    if (createSavingsModal) {
        createSavingsModal.addEventListener('hidden.bs.modal', function () {
            // Reset form
            if (createSavingsForm) {
                createSavingsForm.reset();
            }

            // Reset state
            selectedSavingsType = null;
            initialDepositInput.disabled = true;
            initialDepositInput.placeholder = 'Select account type first';
            initialDepositWarning.style.display = 'none';
            minimumDepositLabel.textContent = '(Select account type first)';
            minimumDepositLabel.className = 'text-muted';
            savingsTypeInfo.style.display = 'none';
            submitButton.disabled = true;
        });
    }
});

/**
 * View transaction history for a savings account */
function viewTransactions(savingsId) {
    console.log('Opening transaction history for:', savingsId);

    const modal = new bootstrap.Modal(document.getElementById('transactionHistoryModal'));
    const content = document.getElementById('transactionHistoryContent');

    // Show loading spinner
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Loading transactions...</p>
        </div>
    `;

    modal.show();

    // Load transactions via AJAX
    fetch('php/get_savings_transaction.php?savings_id=' + encodeURIComponent(savingsId))
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);

            if (!data.success) {
                throw new Error(data.error || 'Unknown error occurred');
            }

            if (data.transactions && data.transactions.length > 0) {
                let html = '<div class="list-group">';

                data.transactions.forEach(t => {
                    let iconClass = '';
                    let colorClass = '';
                    let sign = '';
                    let icon = '';

                    // Determine styling based on transaction type
                    if (t.transaction_type === 'Deposit') {
                        iconClass = 'icon-green';
                        colorClass = 'text-success';
                        sign = '+';
                        icon = 'bi-arrow-down-circle';
                    } else if (t.transaction_type === 'Withdraw') {
                        iconClass = 'icon-red';
                        colorClass = 'text-danger';
                        sign = '-';
                        icon = 'bi-arrow-up-circle';
                    } else if (t.transaction_type === 'Interest') {
                        iconClass = 'icon-blue';
                        colorClass = 'text-primary';
                        sign = '+';
                        icon = 'bi-graph-up-arrow';
                    } else {
                        // Default styling for unknown types
                        iconClass = 'icon-blue';
                        colorClass = 'text-secondary';
                        sign = '';
                        icon = 'bi-circle';
                    }

                    html += `
                        <div class="list-group-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box ${iconClass}" style="width: 40px; height: 40px;">
                                        <i class="bi ${icon} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">${escapeHtml(t.transaction_type)}</div>
                                        <small class="text-muted">${formatDate(t.created_at)}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold ${colorClass}">
                                        ${sign}₱${parseFloat(t.amount).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </div>
                                    <small class="text-muted">Balance: ₱${parseFloat(t.balance_after).toLocaleString('en-US', { minimumFractionDigits: 2 })}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';

                // Add transaction count info
                html += `
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing ${data.transactions.length} transaction${data.transactions.length !== 1 ? 's' : ''}
                        </small>
                    </div>
                `;

                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <p class="text-muted">No transactions yet for this savings account</p>
                        <small class="text-muted">Deposits, withdrawals, and interest will appear here</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading transactions:', error);

            content.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Failed to Load Transactions</h6>
                    <p class="mb-0">${escapeHtml(error.message)}</p>
                    <hr>
                    <small class="text-muted">
                        <strong>Troubleshooting tips:</strong><br>
                        • Check that the file <code>php/get_savings_transaction.php</code> exists<br>
                        • Verify your database connection is working<br>
                        • Check browser console for more details<br>
                        • Savings ID: ${escapeHtml(savingsId)}
                    </small>
                </div>
            `;
        });
}

/** Format date for display*/
function formatDate(dateString) {
    try {
        const date = new Date(dateString);

        // Check if date is valid
        if (isNaN(date.getTime())) {
            return dateString; // Return original if invalid
        }

        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };

        return date.toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Date formatting error:', error);
        return dateString;
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

/**
 * Auto-dismiss alerts after 5 seconds
 */
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Only auto-dismiss success alerts
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        }
    });
});



// view loan details modal
document.querySelectorAll('.view-loan-btn').forEach(button => {
    button.addEventListener('click', function () {
        const loanId = this.dataset.loanId;

        document.getElementById('loanDetailsLoading').classList.remove('d-none');
        document.getElementById('loanDetailsContent').classList.add('d-none');

        fetch('php/get_loan_details.php?loan_id=' + loanId)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loanDetailsLoading').classList.add('d-none');
                document.getElementById('loanDetailsContent').classList.remove('d-none');

                // Loan Info
                document.getElementById('ld-loan-id').textContent = data.loan.loan_id;
                document.getElementById('ld-loan-type').textContent = data.loan.loan_type;
                document.getElementById('ld-status').textContent = data.loan.Status;
                document.getElementById('ld-date').textContent = data.loan.application_date;
                document.getElementById('ld-reason').textContent = data.loan.reason;
                document.getElementById('ld-total').textContent = '₱' + data.loan.total_amount;
                document.getElementById('ld-paid').textContent = '₱' + data.loan.paid;
                document.getElementById('ld-balance').textContent = '₱' + data.loan.balance;

                // Payment Breakdown
                const tbody = document.getElementById('paymentBreakdownTable');
                tbody.innerHTML = '';

                data.payments.forEach((p, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${p.due_date}</td>
                            <td>₱${p.payment_amount}</td>
                            <td class="fw-bold ${p.status === 'Paid' ? 'text-success' : 'text-warning'}">
                                ${p.status}
                            </td>
                        </tr>
                    `;
                });
            });
    });
});

// ==========================================
// CASH OUT VALIDATION AND FORM HANDLING
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const methodCards = document.querySelectorAll('.cashout-method-card');
    const selectedMethodDisplay = document.getElementById('selectedMethodDisplay');
    const displayMethod = document.getElementById('displayMethod');
    const counterOptions = document.getElementById('counterOptions');
    const machineOptions = document.getElementById('machineOptions');
    const storeOptions = document.getElementById('storeOptions');
    const cashOutForm = document.getElementById('cashOutForm');
    const cashOutAmount = document.getElementById('cashOutAmount');
    const cashOutWarning = document.getElementById('cashOutWarning');
    const confirmCashOutBtn = document.getElementById('confirmCashOutBtn');


    let selectedMethod = '';


    // Handle method selection
    if (methodCards) {
        methodCards.forEach(card => {
            card.addEventListener('click', function () {
                // Remove selected class from all cards
                methodCards.forEach(c => c.classList.remove('selected'));


                // Add selected class to clicked card
                this.classList.add('selected');


                // Get method data
                const method = this.dataset.method;
                const description = this.dataset.description;
                const icon = this.dataset.icon;


                // Set hidden inputs
                document.getElementById('cashOutMethod').value = method;
                document.getElementById('cashOutDescription').value = description;
                document.getElementById('cashOutIcon').value = icon;


                // Show selected method
                selectedMethodDisplay.style.display = 'block';
                displayMethod.textContent = method;


                // Hide all option divs
                counterOptions.style.display = 'none';
                machineOptions.style.display = 'none';
                storeOptions.style.display = 'none';


                // Clear previous selections and warnings
                document.getElementById('counterLocation').value = '';
                document.getElementById('machineType').value = '';
                document.getElementById('storeCellphone').value = '';
                document.getElementById('cellphoneWarning').style.display = 'none';
                document.getElementById('cellphoneWarning').textContent = '';


                // Show relevant options
                if (method === 'Over the Counter') {
                    counterOptions.style.display = 'block';
                } else if (method === 'Cash Machine') {
                    machineOptions.style.display = 'block';
                } else if (method === 'Sari-Sari Store') {
                    storeOptions.style.display = 'block';
                }


                selectedMethod = method;
            });
        });
    }


    // Validate amount on input
    if (cashOutAmount) {
        cashOutAmount.addEventListener('input', function () {
            const amount = parseFloat(this.value);
            if (isNaN(amount) || amount <= 0) {
                cashOutWarning.style.display = 'block';
                cashOutWarning.textContent = 'Amount must be greater than 0';
                confirmCashOutBtn.disabled = true;
            } else if (amount > availableBalance) {
                cashOutWarning.style.display = 'block';
                cashOutWarning.textContent = 'Insufficient balance!';
                confirmCashOutBtn.disabled = true;
            } else {
                cashOutWarning.style.display = 'none';
                confirmCashOutBtn.disabled = false;
            }
        });
    }


    // Validate cellphone for Sari-Sari Store
    const storeCellphone = document.getElementById('storeCellphone');
    if (storeCellphone) {
        storeCellphone.addEventListener('input', function () {
            // Clear warning on input
            const warningDiv = document.getElementById('cellphoneWarning');
            warningDiv.style.display = 'none';
            warningDiv.textContent = '';
            confirmCashOutBtn.disabled = false;
        });


        storeCellphone.addEventListener('blur', async function () {
            const cellphone = this.value.trim();
            const warningDiv = document.getElementById('cellphoneWarning');


            // Clear previous warnings
            warningDiv.style.display = 'none';
            warningDiv.textContent = '';


            if (!cellphone) {
                return;
            }


            // Basic format validation
            if (cellphone.length !== 11 || !cellphone.startsWith('09')) {
                warningDiv.style.display = 'block';
                warningDiv.textContent = 'Invalid format. Use: 09XXXXXXXXX';
                confirmCashOutBtn.disabled = true;
                return;
            }


            // Show validating message
            warningDiv.style.display = 'block';
            warningDiv.className = 'text-info mt-1';
            warningDiv.textContent = 'Validating cellphone number...';


            // Validate cellphone via AJAX
            try {
                const response = await fetch('php/validate_cellphone.php?cellphone=' + encodeURIComponent(cellphone));
                const data = await response.json();


                if (!data.success) {
                    warningDiv.className = 'text-danger mt-1';
                    warningDiv.style.display = 'block';
                    warningDiv.textContent = data.message || 'Invalid cellphone number';
                    confirmCashOutBtn.disabled = true;
                } else {
                    warningDiv.className = 'text-success mt-1';
                    warningDiv.style.display = 'block';
                    warningDiv.textContent = '✓ ' + data.message;
                    confirmCashOutBtn.disabled = false;
                }
            } catch (error) {
                console.error('Validation error:', error);
                warningDiv.className = 'text-danger mt-1';
                warningDiv.style.display = 'block';
                warningDiv.textContent = 'Error validating cellphone number';
                confirmCashOutBtn.disabled = true;
            }
        });
    }


    // Form submission validation
    if (cashOutForm) {
        cashOutForm.addEventListener('submit', async function (e) {
            e.preventDefault();


            // Check if method is selected
            if (!selectedMethod) {
                showCashOutModal('cashOutWarningModal', 'Please select a cash out method first!');
                return;
            }


            // Check amount
            const amount = parseFloat(cashOutAmount.value);
            if (!amount || amount <= 0) {
                showCashOutModal('cashOutWarningModal', 'Please enter a valid amount!');
                return;
            }


            if (amount > availableBalance) {
                showCashOutModal('cashOutWarningModal', 'Insufficient balance! Available: ₱' + availableBalance.toFixed(2));
                return;
            }


            // Validate specific options
            if (selectedMethod === 'Over the Counter') {
                const location = document.getElementById('counterLocation').value;
                if (!location) {
                    showCashOutModal('cashOutWarningModal', 'Please select a counter location!');
                    return;
                }
            } else if (selectedMethod === 'Cash Machine') {
                const machine = document.getElementById('machineType').value;
                if (!machine) {
                    showCashOutModal('cashOutWarningModal', 'Please select a machine type!');
                    return;
                }
            } else if (selectedMethod === 'Sari-Sari Store') {
                const cellphone = storeCellphone.value.trim();
                if (!cellphone) {
                    showCashOutModal('cashOutWarningModal', 'Please enter store cellphone number!');
                    return;
                }
                if (cellphone.length !== 11 || !cellphone.startsWith('09')) {
                    showCashOutModal('cashOutWarningModal', 'Invalid cellphone format! Use: 09XXXXXXXXX');
                    return;
                }


                const warningDiv = document.getElementById('cellphoneWarning');
                // Check if there's an error (not a success message)
                if (warningDiv.style.display !== 'none' && warningDiv.className.includes('text-danger')) {
                    showCashOutModal('cashOutWarningModal', warningDiv.textContent);
                    return;
                }


                // Re-validate cellphone before submitting
                try {
                    const response = await fetch('php/validate_cellphone.php?cellphone=' + encodeURIComponent(cellphone));
                    const data = await response.json();


                    if (!data.success) {
                        showCashOutModal('cashOutWarningModal', data.message || 'Invalid cellphone number');
                        return;
                    }
                } catch (error) {
                    showCashOutModal('cashOutErrorModal', 'Error validating cellphone number');
                    return;
                }
            }


            // If all validations pass, submit the form
            this.submit();
        });
    }


    function showCashOutModal(modalId, message) {
        const messageElement = modalId === 'cashOutWarningModal'
            ? document.getElementById('cashOutWarningMessage')
            : document.getElementById('cashOutErrorMessage');


        if (messageElement) {
            messageElement.textContent = message;
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }
    }


    // Reset form when modal is closed
    const cashOutModal = document.getElementById('cashOutModal');
    if (cashOutModal) {
        cashOutModal.addEventListener('hidden.bs.modal', function () {
            methodCards.forEach(c => c.classList.remove('selected'));
            selectedMethodDisplay.style.display = 'none';
            counterOptions.style.display = 'none';
            machineOptions.style.display = 'none';
            storeOptions.style.display = 'none';
            cashOutForm.reset();
            cashOutWarning.style.display = 'none';
            document.getElementById('cellphoneWarning').style.display = 'none';
            document.getElementById('cellphoneWarning').textContent = '';
            selectedMethod = '';
            confirmCashOutBtn.disabled = false;
        });
    }
});


// ==========================================
// SEND MONEY VALIDATION
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const sendForm = document.getElementById('sendMoneyForm');
    const sendAmountInput = document.getElementById('sendAmount');
    const recipientInput = document.getElementById('recipientNumber');
    const warningDivSend = document.getElementById('sendMoneyWarning');
    const nextBtn = document.getElementById('sendMoneyNext');
    const confirmAmountSpan = document.getElementById('confirmSendAmount');
    const confirmRecipientSpan = document.getElementById('confirmRecipient');


    if (recipientInput) {
        // Create warning div if it doesn't exist
        let recipientWarning = document.getElementById('recipientWarning');
        if (!recipientWarning) {
            recipientWarning = document.createElement('div');
            recipientWarning.id = 'recipientWarning';
            recipientWarning.className = 'text-danger mt-1';
            recipientWarning.style.display = 'none';
            recipientInput.parentNode.appendChild(recipientWarning);
        }


        // Clear warning on input
        recipientInput.addEventListener('input', function () {
            recipientWarning.style.display = 'none';
            recipientWarning.textContent = '';
        });


        // Validate on blur (when user leaves the field)
        recipientInput.addEventListener('blur', async function () {
            const cellphone = this.value.trim();


            // Clear previous warnings
            recipientWarning.style.display = 'none';
            recipientWarning.textContent = '';


            if (!cellphone) {
                return;
            }


            // Basic format validation
            if (cellphone.length !== 11 || !cellphone.startsWith('09')) {
                recipientWarning.style.display = 'block';
                recipientWarning.className = 'text-danger mt-1';
                recipientWarning.textContent = 'Invalid format. Use: 09XXXXXXXXX';
                return;
            }


            // Show validating message
            recipientWarning.style.display = 'block';
            recipientWarning.className = 'text-info mt-1';
            recipientWarning.textContent = 'Validating recipient...';


            // Validate cellphone via AJAX (reusing the same endpoint!)
            try {
                const response = await fetch('php/validate_cellphone.php?cellphone=' + encodeURIComponent(cellphone));
                const data = await response.json();


                if (!data.success) {
                    recipientWarning.className = 'text-danger mt-1';
                    recipientWarning.style.display = 'block';
                    recipientWarning.textContent = data.message || 'Invalid recipient number';
                } else {
                    recipientWarning.className = 'text-success mt-1';
                    recipientWarning.style.display = 'block';
                    recipientWarning.textContent = '✓ ' + data.message;
                }
            } catch (error) {
                console.error('Validation error:', error);
                recipientWarning.className = 'text-danger mt-1';
                recipientWarning.style.display = 'block';
                recipientWarning.textContent = 'Error validating recipient number';
            }
        });
    }


    // Update the existing sendMoneyNext button click handler
    if (nextBtn) {
        nextBtn.addEventListener('click', async function () {
            const amount = parseFloat(sendAmountInput.value) || 0;
            const recipient = recipientInput.value.trim();
            const recipientWarning = document.getElementById('recipientWarning');


            if (!recipient) {
                warningDivSend.textContent = "Please enter recipient's cellphone number.";
                warningDivSend.style.display = 'block';
                return;
            }


            // Check if there's an error (not a success message)
            if (recipientWarning && recipientWarning.style.display !== 'none' && recipientWarning.className.includes('text-danger')) {
                warningDivSend.textContent = recipientWarning.textContent;
                warningDivSend.style.display = 'block';
                return;
            }


            if (amount <= 0) {
                warningDivSend.textContent = "Enter a valid amount.";
                warningDivSend.style.display = 'block';
                return;
            }


            if (amount > balanceDisplay) {
                warningDivSend.textContent = `Amount cannot be greater than available balance (₱${balanceDisplay.toFixed(2)})`;
                warningDivSend.style.display = 'block';
                return;
            }


            // Re-validate before showing confirmation
            try {
                const response = await fetch('php/validate_cellphone.php?cellphone=' + encodeURIComponent(recipient));
                const data = await response.json();


                if (!data.success) {
                    warningDivSend.textContent = data.message || 'Invalid recipient number';
                    warningDivSend.style.display = 'block';
                    return;
                }
            } catch (error) {
                warningDivSend.textContent = 'Error validating recipient';
                warningDivSend.style.display = 'block';
                return;
            }


            warningDivSend.style.display = 'none';


            confirmAmountSpan.textContent = `₱${amount.toFixed(2)}`;
            confirmRecipientSpan.textContent = maskRecipient(recipient);


            const confirmModal = new bootstrap.Modal(document.getElementById('sendMoneyConfirmModal'));
            confirmModal.show();
        });
    }
});


function maskRecipient(number) {
    // Mask all digits except last 4
    if (number.length <= 4) return "****";
    let masked = number.slice(0, number.length - 4).replace(/\d/g, "*");
    return masked + number.slice(-4);
}

