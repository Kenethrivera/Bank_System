document.querySelectorAll('#sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function (e) {
        const targetId = this.getAttribute('href');

        // ignore external routes
        if (!targetId.startsWith('#')) return;

        e.preventDefault();

        document.querySelectorAll('main section').forEach(sec => {
            sec.classList.remove('active');
        });

        document.querySelector(targetId).classList.add('active');
    });
});

// show dashboard by default
document.querySelector('#dashboard').classList.add('active');
