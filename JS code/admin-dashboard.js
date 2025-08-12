document.addEventListener("DOMContentLoaded", function () {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");
    const menuIcon = document.getElementById("menu-icon");
    const allSideMenu = document.querySelectorAll("#sidebar .sidebar-menu li a");

    // Function to toggle Sidebar
    function toggleSidebar() {
        sidebar.classList.toggle("active");
        document.body.classList.toggle("sidebar-open");
    }

    // Toggle Sidebar on button click
    sidebarToggle.addEventListener("click", toggleSidebar);
    menuIcon.addEventListener("click", toggleSidebar);

    // Highlight active menu item
    allSideMenu.forEach(item => {
        item.addEventListener("click", function () {
            allSideMenu.forEach(menu => menu.parentElement.classList.remove("active"));
            this.parentElement.classList.add("active");
        });
    });

    // Charts
    const ctx1 = document.getElementById('userChart').getContext('2d');
    const ctx2 = document.getElementById('performanceChart').getContext('2d');

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
