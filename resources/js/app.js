import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.getElementById('menu-toggle').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar-wrapper');
    const content = document.querySelector('.flex-grow-1');
    const isCollapsed = sidebar.style.marginLeft === '-250px';
    
    sidebar.style.marginLeft = isCollapsed ? '0' : '-250px';
    content.style.marginLeft = isCollapsed ? '250px' : '0';
});
