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
                ? `../${this.dataset.profileimg}`  // go up one level if needed
                : 'assets/default-avatar.png';



        // Savings
        document.getElementById('modalSavingsId').textContent = savingsId;
        document.getElementById('modalStatus').textContent = this.dataset.status;
        document.getElementById('modalTotalBalance').textContent =
            Number(this.dataset.totalbalance).toFixed(2);
        document.getElementById('modalInterestRate').textContent =
            (Number(this.dataset.interestrate) * 100).toFixed(2);
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

// SETTINGS UPDATE FOR INTEREST
const rates = { Regular: 0.05, Fixed: 0.08, Special: 0.10 };

const modal = document.getElementById('gearModal');
const typeSelect = document.getElementById('gearSavingsType');
const interestInput = document.getElementById('gearInterestRate');

modal.addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    const savingsId = btn.dataset.savingsid;
    const savingsType = btn.dataset.savingsname;

    document.getElementById('gearSavingsId').value = savingsId;
    typeSelect.value = savingsType;
    interestInput.value = (rates[savingsType] || 0) * 100;
});

typeSelect.addEventListener('change', () => {
    interestInput.value = (rates[typeSelect.value] || 0) * 100;
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
const filterButtons = document.querySelectorAll('.btn-sm');
const savingsTiles = document.querySelectorAll('.savings-tile');

filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const filter = btn.textContent.trim(); // "All", "Active", etc.

        // Update button styles (optional)
        filterButtons.forEach(b => b.classList.remove('btn-dark'));
        filterButtons.forEach(b => b.classList.add('btn-light'));
        btn.classList.remove('btn-light');
        btn.classList.add('btn-dark');

        savingsTiles.forEach(tile => {
            const status = tile.getAttribute('data-status');

            if (filter === 'All' || status === filter) {
                tile.style.display = ''; // show
            } else {
                tile.style.display = 'none'; // hide
            }
        });
    });
});

// auto fill interest rate for NEW SAVINGS APPLICATION
const newType = document.getElementById('newSavingsType');
const newInterest = document.getElementById('newInterestRate');

// Define default rates
const savingsRates = { Regular: 0.05, Fixed: 0.08, Special: 0.10 };

newType.addEventListener('change', () => {
    const type = newType.value;
    newInterest.value = savingsRates[type] ? (savingsRates[type] * 100).toFixed(2) : '';
});

// ADMIN LOGOUT
adminLogoutLink.addEventListener('click', function(e) {
    e.preventDefault(); // prevent default anchor behavior

    const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
    logoutModal.show();
});
