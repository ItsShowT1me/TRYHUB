// JavaScript สำหรับการสร้างกราฟ
document.addEventListener("DOMContentLoaded", function () {
    // User Growth Chart
    const ctx1 = document.getElementById('userChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            datasets: [{
                label: 'User Growth',
                data: [500, 700, 800, 1200, 1500],
                backgroundColor: 'rgba(60, 145, 230, 0.2)',
                borderColor: '#3C91E6',
                borderWidth: 2
            }]
        }
    });

    // Performance Stats Chart
    const ctx2 = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [{
                label: 'Performance',
                data: [90, 92, 95, 98],
                backgroundColor: ['#FFCE26', '#FD7238', '#3C91E6', '#DB504A']
            }]
        }
    });
});

// In loadModalMembers function, replace pagination block:
if (data.total_pages > 1) {
    html += '<div class="member-modal-pagination">';
    for (let i = 1; i <= data.total_pages; i++) {
        html += `<button class="${i==data.page?'active':''} member-page-btn" data-page="${i}">${i}</button>`;
    }
    html += '</div>';
}
