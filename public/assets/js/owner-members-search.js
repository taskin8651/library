const liveSearch = document.getElementById('memberLiveSearch');
const searchHint = document.getElementById('memberSearchHint');
const noMatch = document.getElementById('memberNoLocalMatch');
const pagination = document.getElementById('memberPagination');

if (liveSearch) {
    liveSearch.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        const items = document.querySelectorAll('.member-grid-item');
        let visible = 0;

        items.forEach(item => {
            const match = !q
                || item.dataset.name.includes(q)
                || item.dataset.phone.includes(q)
                || item.dataset.uid.includes(q);
            item.classList.toggle('d-none', !match);
            if (match) visible++;
        });

        searchHint.classList.toggle('d-none', !q);
        noMatch.classList.toggle('d-none', visible !== 0);
        if (pagination) pagination.classList.toggle('d-none', !!q);
    });
}
