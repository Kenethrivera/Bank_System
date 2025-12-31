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

// script for savings modal
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
  btn.addEventListener('click', function () {

    const savingsId = this.dataset.savingsid;

    document.getElementById('modalCustomerName').textContent = this.dataset.customername;
    document.getElementById('modalSavingsId').textContent = savingsId;
    document.getElementById('modalStatus').textContent = this.dataset.status;
    document.getElementById('modalTotalBalance').textContent =
      Number(this.dataset.totalbalance).toFixed(2);
    document.getElementById('modalInterestRate').textContent =
      (Number(this.dataset.interestrate) * 100).toFixed(2);
    document.getElementById('modalAccountType').textContent =
      this.dataset.accounttype;

    const tbody = document.getElementById('modalTransactions');
    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Loading...</td></tr>';

    fetch(`php/get_savings_transactions.php?savings_id=${savingsId}`)
      .then(res => res.json())
      .then(data => {

        tbody.innerHTML = '';

        if (data.transactions.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="3" class="text-center">No transactions found</td></tr>';
          return;
        }

        data.transactions.forEach(t => {
          const amount = parseFloat(t.amount) || 0;
          const balanceAfter = parseFloat(t.balance_after) || 0;

          const tr = document.createElement('tr');
          tr.innerHTML = `
              <td>${t.date}</td>
              <td>${t.type}</td>
              <td>₱${amount.toFixed(2)}</td>
              <td>₱${balanceAfter.toFixed(2)}</td>
            `;
          tbody.appendChild(tr);
        });

        document.getElementById('modalTotalInterest').textContent =
          Number(data.total_interest).toFixed(2);
      })
      .catch(err => {
        console.error(err);
        tbody.innerHTML =
          '<tr><td colspan="3" class="text-danger text-center">Error loading transactions</td></tr>';
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

