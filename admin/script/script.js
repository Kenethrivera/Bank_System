document.querySelectorAll('#sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');

        if (!targetId.startsWith('#')) return;

        e.preventDefault();

        document.querySelectorAll('main section').forEach(sec => {
            sec.classList.remove('active');
        });

        document.querySelector(targetId).classList.add('active');
    });
});

document.querySelector('#accounts').classList.add('active');


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

    searchInput.addEventListener('keyup', function() {
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