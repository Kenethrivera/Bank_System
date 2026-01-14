function showSectionFromHash() {  //  function that decides which section to show
    const hash = window.location.hash || '#dashboard';  // window.location.hash reads the URL part after #. || means if nothing show direct to dashboard

    // loops through all of sections then removes the active section 
    // this simply hides all of the active first. to reset
    document.querySelectorAll('main section').forEach(sec => {
        sec.classList.remove('active');
        
    });

    // Finds the section whose ID matches the hash.
    const target = document.querySelector(hash);
    if (target) {
        target.classList.add('active'); //the hash that match will be added as active which means it will be display
        window.scrollTo({ top: 0, behavior: 'instant' });
    }
}

document.querySelectorAll('#sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        const target = this.getAttribute('href');

        if (!target.startsWith('#')) return;

    e.preventDefault();

        window.location.hash = target;

        showSectionFromHash()
    });
});

document.addEventListener('DOMContentLoaded', showSectionFromHash);


document.querySelectorAll('.view-profile-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const status = this.getAttribute('data-status');
        const buttons = document.getElementById('decisionButtons');
        const message = document.getElementById('decisionMadeMsg');
        
        if (status === 'Pending') {
            buttons.classList.remove('d-none');
            message.classList.add('d-none');
        } else {
            buttons.classList.add('d-none');
            message.classList.remove('d-none');
        }
    });
});
document.addEventListener('DOMContentLoaded', () => {

    const modalName = document.getElementById('modalName');
    const modalEmail = document.getElementById('modalEmail');
    const modalPhone = document.getElementById('modalPhone');
    const modalDOB = document.getElementById('modalDOB');
    const modalAddress = document.getElementById('modalAddress');
    const modalImg = document.querySelector('.modalImg');
    document.querySelectorAll('.view-profile-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            modalName.textContent = this.dataset.name;
            modalEmail.textContent = this.dataset.email;
            modalPhone.textContent = this.dataset.phone;
            modalDOB.textContent = this.dataset.dob;
            modalAddress.textContent = this.dataset.address;
            document.getElementById('modalAccountId').value = this.dataset.id;
            if (this.dataset.img) {
                modalImg.src = `../${this.dataset.img}`;
            } else {
                modalImg.src = '../profile/default.jpg';
            }

        });
    });

});
document.addEventListener('DOMContentLoaded', () => {
    // Existing code for sidebar and modal can stay here...

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('searchableTable');  // Corrected ID
    const rows = table.getElementsByTagName('tr');
    const notFound = document.getElementById('notFound');

    searchInput.addEventListener('keyup', function () {
        const filter = this.value.toUpperCase().trim();  // Trim whitespace and uppercase
        let hasResult = false;

        // Loop through rows (skip header row at index 0)
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;

            // Check each cell in the row
            for (let j = 0; j < cells.length; j++) {
                const cellText = cells[j].textContent || cells[j].innerText;
                if (cellText.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            // Show/hide row
            rows[i].style.display = found ? '' : 'none';
            if (found) hasResult = true;
        }

        // Show/hide "no results" message
        notFound.style.display = hasResult ? 'none' : 'block';
    });
});


// confirmation modal for action in LOAN SECTION
const confirmModal = document.getElementById('confirmModal');
if (confirmModal) {
    confirmModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const action = button.getAttribute('data-action');
        const loanId = button.getAttribute('data-loanid');

        document.getElementById('confirmLoanId').value = loanId;
        document.getElementById('confirmAction').value = action;

        document.getElementById('confirmMessage').innerHTML =
            `Are you sure you want this laon to be <strong>${action}</strong>?`;
    });
}

// script for DETAILS modal
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
    btn.addEventListener('click', function () {

        const savingsId = this.dataset.savingsid;

        // Profile
        document.getElementById('modalCustomerName').textContent = this.dataset.customername;
        document.getElementById('Email').textContent = this.dataset.email;
        document.getElementById('Phone').textContent = this.dataset.phone;
        document.getElementById('Address').textContent = this.dataset.address;
        document.getElementById('modalBirthdate').textContent = this.dataset.birthdate;
        document.getElementById('modalUserStatus').textContent = this.dataset.userstatus;

        document.getElementById('profileIMG').src =
            this.dataset.profileimg
                ? `../${this.dataset.profileimg}`  
                : 'assets/default-avatar.png';



        // Savings
        document.getElementById('modalSavingsId').textContent = savingsId;
        document.getElementById('modalStatus').textContent = this.dataset.status;
        document.getElementById('modalTotalBalance').textContent =
            Number(this.dataset.totalbalance).toFixed(2);
        document.getElementById('modalInterestRate').textContent =
            (Number(this.dataset.interestrate)).toFixed(2);
        document.getElementById('modalAccountType').textContent =
            this.dataset.accounttype;

        // Transactions
        const tbody = document.getElementById('modalTransactions');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';

        fetch(`php/get_savings_transactions.php?savings_id=${savingsId}`)
            .then(res => res.json())
            .then(data => {

                tbody.innerHTML = '';

                if (data.transactions.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="4" class="text-center">No transactions found</td></tr>';
                    return;
                }

                data.transactions.forEach(t => {
                    tbody.innerHTML += `
            <tr>
              <td>${t.date}</td>
              <td>${t.type}</td>
              <td>₱${Number(t.amount).toFixed(2)}</td>
              <td>₱${Number(t.balance_after).toFixed(2)}</td>
            </tr>
          `;
                });

                document.getElementById('modalTotalInterest').textContent =
                    Number(data.total_interest).toFixed(2);
            })
            .catch(() => {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-danger text-center">Error loading transactions</td></tr>';
            });
    });
});

const SAVINGS_RATES = {
    Regular: 2.5,
    Fixed: 3.5,
    Special: 5.0
};
// SETTINGS UPDATE FOR INTEREST
const rates = { Regular: 0.05, Fixed: 0.08, Special: 0.10 };

const gearModal = document.getElementById('gearModal');
const gearTypeSelect = document.getElementById('gearSavingsType');
const gearInterestInput = document.getElementById('gearInterestRate');

if (gearModal) {
    gearModal.addEventListener('show.bs.modal', (e) => {
        const btn = e.relatedTarget;

        const savingsId = btn.getAttribute('data-savingsid');
        const savingsType = btn.getAttribute('data-savingsname');

        document.getElementById('gearSavingsId').value = savingsId;
        gearTypeSelect.value = savingsType;
        gearInterestInput.value = SAVINGS_RATES[savingsType].toFixed(2);
    });
}

if (gearTypeSelect) {
    gearTypeSelect.addEventListener('change', () => {
        gearInterestInput.value = SAVINGS_RATES[gearTypeSelect.value].toFixed(2);
    });
}


// error code and success alert
document.addEventListener('DOMContentLoaded', () => {

    const errorBox = document.getElementById('savingsError');
    const successBox = document.getElementById('savingsSuccess');

    if (errorBox) {
        alert(errorBox.dataset.message);
    }

    if (successBox) {
        alert(successBox.dataset.message);
    }

});

//  CHANGING STATUS MODAL
var statusModal = document.getElementById('statusModal');
statusModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // the button that triggered the modal
    var savingsId = button.getAttribute('data-savingsid');
    var currentStatus = button.getAttribute('data-currentstatus');

    // set hidden input
    document.getElementById('statusSavingsId').value = savingsId;

    // display current status
    document.getElementById('currentStatusText').textContent = currentStatus;
});

//  STATUS FILTERING
document.addEventListener('DOMContentLoaded', () => {

    const filterButtons = document.querySelectorAll('.filter-buttons .btn-sm');
    const savingsTiles = document.querySelectorAll('.savings-tile');

    if (!filterButtons.length || !savingsTiles.length) {
        console.warn('Savings filter: buttons or tiles not found');
        return;
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.textContent.trim(); // All, Active, Pending...

            // update button styles
            filterButtons.forEach(b => {
                b.classList.remove('btn-dark');
                b.classList.add('btn-light');
            });

            btn.classList.remove('btn-light');
            btn.classList.add('btn-dark');

            // filter tiles
            savingsTiles.forEach(tile => {
                const status = tile.dataset.status;

                if (filter === 'All' || status === filter) {
                    tile.style.display = '';
                } else {
                    tile.style.display = 'none';
                }
            });
        });
    });

});

// auto fill interest rate for NEW SAVINGS APPLICATION
const newSavingsModal = document.getElementById('savingsModal');
const newSavingsType = document.getElementById('newSavingsType');
const newInterestRate = document.getElementById('newInterestRate');

if (newSavingsModal) {
    newSavingsModal.addEventListener('show.bs.modal', () => {
        const type = newSavingsType.value;
        newInterestRate.value = SAVINGS_RATES[type].toFixed(2);
    });
}

if (newSavingsType) {
    newSavingsType.addEventListener('change', () => {
        newInterestRate.value = SAVINGS_RATES[newSavingsType.value].toFixed(2);
    });
}
document.addEventListener('DOMContentLoaded', () => {
    const customerSelect = document.getElementById('customerSelect');
    const savingsType = document.getElementById('newSavingsType');
    const interestInput = document.getElementById('newInterestRate');
    const depositInput = document.getElementById('initialDeposit');
    const balanceText = document.getElementById('availableBalance');
    const submitBtn = document.getElementById('createSavingsBtn');

    // Create a small element to show error below input if it doesn't exist
    let depositError = document.getElementById('depositError');
    if (!depositError) {
        depositError = document.createElement('small');
        depositError.id = 'depositError';
        depositError.classList.add('text-danger', 'd-block', 'mt-1'); // red text, block, margin-top
        depositInput.parentNode.appendChild(depositError);
    }

    const interestRates = { Regular: 2.5, Fixed: 3.5, Special: 5.0 };
    const MINIMUMS = { Regular: 100, Fixed: 1000, Special: 50000 };

    let currentBalance = 0;

    // Initialize interest rate
    interestInput.value = interestRates[savingsType.value].toFixed(2);

    // Update interest rate when type changes
    savingsType.addEventListener('change', () => {
        interestInput.value = interestRates[savingsType.value].toFixed(2);
        validateDeposit();
    });

    // Fetch balance when customer changes
    customerSelect.addEventListener('change', () => {
        const userId = customerSelect.value;
        currentBalance = 0;
        balanceText.textContent = '₱0.00';
        validateDeposit();

        if (!userId) return;

        fetch(`Dashboard.php?get_balance=1&id=${userId}`)
            .then(res => res.json())
            .then(data => {
                currentBalance = data.balance ?? 0;
                balanceText.textContent = '₱' + currentBalance.toLocaleString(undefined, { minimumFractionDigits: 2 });
                validateDeposit();
            });
    });

    // Validate deposit input
    depositInput.addEventListener('input', validateDeposit);

    function validateDeposit() {
        const amount = parseFloat(depositInput.value) || 0;
        const type = savingsType.value;
        const min = MINIMUMS[type];

        let valid = true;
        let errorMsg = '';

        if (!customerSelect.value) {
            valid = false;
            errorMsg = 'Select a customer';
        } else if (currentBalance < min) {
            valid = false;
            errorMsg = `Available balance is too low for ${type} savings (minimum ₱${min.toLocaleString()})`;
        } else if (amount < min) {
            valid = false;
            errorMsg = `Minimum deposit for ${type} is ₱${min.toLocaleString()}`;
        } else if (amount > currentBalance) {
            valid = false;
            errorMsg = 'Deposit cannot exceed available balance';
        }

        // Show/hide error
        if (valid) {
            depositInput.classList.remove('is-invalid');
            depositError.textContent = '';
        } else {
            depositInput.classList.add('is-invalid');
            depositError.textContent = errorMsg;
        }

        submitBtn.disabled = !valid;
    }
});
// DELTE USERS
// Listener for the Delete Reason Modal
document.addEventListener('DOMContentLoaded', () => {
    const reasonGroup = document.getElementById('reasonInputGroup');
    const reasonText = document.getElementById('rejectionReasonText');
    const actionWarning = document.getElementById('actionWarning');
    const modalTitle = document.getElementById('reasonModalTitle');
    const confirmBtn = document.getElementById('confirmBtnText');

    // 1. Logic for REJECT (from Profile Modal)
    const profileRejectBtn = document.getElementById('profileRejectBtn');
    if (profileRejectBtn) {
        profileRejectBtn.addEventListener('click', function() {
            const currentId = document.getElementById('modalAccountId').value;
            document.getElementById('deleteTargetId').value = currentId;
            document.getElementById('actionType').value = 'reject';
            
            // UI Adjustments for Reject
            modalTitle.innerText = "Reject Application";
            actionWarning.innerHTML = "This marks the account as <b class='text-danger'>Rejected</b>. The user will see the reason.";
            confirmBtn.innerText = "Confirm Reject";
            reasonGroup.classList.remove('d-none'); // Show reason
            reasonText.required = true; // Make it mandatory
        });
    }

    // 2. Logic for DELETE (from Trash Bin in Table)
    const deleteReasonModal = document.getElementById('deleteReasonModal');
    if (deleteReasonModal) {
        deleteReasonModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            if (button && button.getAttribute('data-id')) {
                const accountId = button.getAttribute('data-id');
                document.getElementById('deleteTargetId').value = accountId;
                document.getElementById('actionType').value = 'delete';
                
                // UI Adjustments for Delete
                modalTitle.innerText = "Delete Account Permanently";
                actionWarning.innerHTML = "<div class='alert alert-danger'><b>Warning:</b> This will permanently remove this record from the database. This cannot be undone.</div>";
                confirmBtn.innerText = "Delete Permanently";
                reasonGroup.classList.add('d-none'); // Hide reason
                reasonText.required = false; // Not needed
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('transactionSearchInput');
    const table = document.getElementById('transactionTable');
    if(!searchInput || !table) return;

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const text = rows[i].textContent.toUpperCase();
            rows[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
        }
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const adminPicInput = document.getElementById('adminProfilePicture');
    if (adminPicInput) {
        adminPicInput.onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const wrapper = document.getElementById('adminPreviewWrapper');
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Replace the camera icon with the actual image preview
                    wrapper.innerHTML = `
                        <img src="${e.target.result}" 
                             class="rounded-circle border border-4 border-white shadow mx-auto d-block" 
                             style="width:100px; height:100px; object-fit: cover; cursor: pointer"
                             onclick="document.getElementById('adminProfilePicture').click()">`;
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
});


// ADMIN LOGOUT
adminLogoutLink.addEventListener('click', function(e) {
    e.preventDefault(); // prevent default anchor behavior

    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
    logoutModal.show();
});
  
