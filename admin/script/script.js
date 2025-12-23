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


document.querySelector('#dashboard').classList.add('active');
