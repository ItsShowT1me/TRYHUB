document.getElementById('languageSelect').addEventListener('change', function() {
    alert('คุณเลือกภาษา: ' + this.options[this.selectedIndex].text);
});
document.getElementById('languageLevel').addEventListener('change', function() {
    alert('คุณเลือก: ' + this.value);
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchType = document.getElementById('searchType');
    const groupCards = document.querySelectorAll('.user-card');

    function filterGroups() {
        const query = searchInput.value.trim().toLowerCase();
        const type = searchType.value; // "name" or "category"

        groupCards.forEach(card => {
            const value = card.dataset[type] || '';
            if (value.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterGroups);
    searchType.addEventListener('change', filterGroups);

    // Modal functionality
    const modal = document.getElementById('userModal');
    const closeModal = document.getElementById('closeModal');
    const modalImage = document.querySelector('.profile-modal-image img');
    const modalName = document.getElementById('modalName');
    const modalEmail = document.getElementById('modalEmail');
    const modalPhone = document.getElementById('modalPhone');
    const modalMbti = document.getElementById('modalMbti');

    groupCards.forEach(card => {
        card.addEventListener('click', function() {
            modalImage.src = card.dataset.image;
            modalName.textContent = card.dataset.name;
            modalEmail.textContent = card.dataset.email;
            modalPhone.textContent = card.dataset.phone || '-';
            modalMbti.textContent = card.dataset.mbti || '-';
            modal.classList.add('show');
        });
    });

    closeModal.onclick = function() {
        modal.classList.remove('show');
    };
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    };
});


