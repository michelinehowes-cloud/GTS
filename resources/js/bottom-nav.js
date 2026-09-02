// Bottom Navigation & FAB Handler
document.addEventListener('DOMContentLoaded', function () {
    const isMobile = window.innerWidth <= 768;

    if (!isMobile) return; // تشغيل فقط في الموبايل

    // إنشاء Bottom Navigation
    createBottomNav();

    // إنشاء FAB
    createFAB();
});

function createBottomNav() {
    // التحقق من وجود المستخدم
    const isAuthenticated = document.querySelector('meta[name="user-authenticated"]');
    if (!isAuthenticated || isAuthenticated.content !== 'true') return;

    const userRole = document.querySelector('meta[name="user-role"]')?.content || 'guest';

    // تحديد عناصر القائمة حسب الدور
    const navItems = getNavItemsByRole(userRole);

    if (navItems.length === 0) return;

    // إنشاء HTML
    const bottomNav = document.createElement('nav');
    bottomNav.className = 'bottom-nav';
    bottomNav.innerHTML = `
        <div class="bottom-nav-items">
            ${navItems.map(item => `
                <a href="${item.url}" class="bottom-nav-item ${isCurrentPage(item.url) ? 'active' : ''}">
                    <i class="${item.icon}"></i>
                    <span>${item.label}</span>
                    ${item.badge ? `<span class="bottom-nav-badge">${item.badge}</span>` : ''}
                </a>
            `).join('')}
        </div>
    `;

    document.body.appendChild(bottomNav);
}

function getNavItemsByRole(role) {
    const commonItems = [
        { url: getDashboardUrl(role), icon: 'fas fa-home', label: 'الرئيسية' },
    ];

    const roleSpecificItems = {
        'graduate': [
            { url: '/graduate/trainings', icon: 'fas fa-graduation-cap', label: 'التدريبات' },
            { url: '/graduate/job-opportunities', icon: 'fas fa-briefcase', label: 'الوظائف' },
            { url: '/notifications', icon: 'fas fa-bell', label: 'الإشعارات', badge: getUnreadCount() },
            { url: '/graduate/profile', icon: 'fas fa-user', label: 'الملف' },
        ],
        'training_coordinator': [
            { url: '/coordinator/trainings', icon: 'fas fa-graduation-cap', label: 'التدريب' },
            { url: '/coordinator/applications', icon: 'fas fa-users', label: 'الطلبات' },
            { url: '/coordinator/calendar', icon: 'fas fa-calendar-alt', label: 'التقويم' },
            { url: '/coordinator/reports', icon: 'fas fa-chart-bar', label: 'التقارير' },
        ],
        'admin': [
            { url: '/admin/users', icon: 'fas fa-users-cog', label: 'المستخدمين' },
            { url: '/admin/trainings', icon: 'fas fa-graduation-cap', label: 'التدريب' },
            { url: '/admin/companies', icon: 'fas fa-building', label: 'الشركات' },
            { url: '/admin/applications', icon: 'fas fa-clipboard-list', label: 'الطلبات' },
        ],
        'company': [
            { url: '/company/profile', icon: 'fas fa-building', label: 'الملف' },
            { url: '/job-opportunities', icon: 'fas fa-briefcase', label: 'الفرص' },
            { url: '/company/job-fairs', icon: 'fas fa-store', label: 'المعارض' },
            { url: '/messages', icon: 'fas fa-envelope', label: 'الرسائل' },
        ],
        'career_guidance_officer': [
            { url: '/career-guidance/graduates', icon: 'fas fa-users', label: 'الخريجين' },
            { url: '/career-guidance/nominations', icon: 'fas fa-user-check', label: 'الترشيحات' },
            { url: '/notifications', icon: 'fas fa-bell', label: 'الإشعارات', badge: getUnreadCount() },
            { url: '/career-guidance/reports', icon: 'fas fa-chart-line', label: 'التقارير' },
        ],
        'partnership_officer': [
            { url: '/partnership/companies', icon: 'fas fa-building', label: 'الشركات' },
            { url: '/partnership/nominations', icon: 'fas fa-handshake', label: 'الترشيحات' },
            { url: '/job-opportunities', icon: 'fas fa-briefcase', label: 'الفرص' },
            { url: '/partnership/reports', icon: 'fas fa-chart-line', label: 'التقارير' },
        ],
        'evaluation_followup': [
            { url: '/evaluation-followup/surveys', icon: 'fas fa-poll-h', label: 'الاستبيانات' },
            { url: '/evaluation-followup/evaluations', icon: 'fas fa-star', label: 'التقييمات' },
            { url: '/evaluation-followup/training-calendar', icon: 'fas fa-calendar-alt', label: 'التقويم' },
            { url: '/evaluation-followup/evaluation-reports', icon: 'fas fa-chart-pie', label: 'التقارير' },
        ],
        'media_officer': [
            { url: '/media/gallery', icon: 'fas fa-photo-video', label: 'الوسائط' },
            { url: '/media/news', icon: 'fas fa-newspaper', label: 'الأخبار' },
            { url: '/media/announcements', icon: 'fas fa-bullhorn', label: 'الإعلانات' },
            { url: '/media/reports', icon: 'fas fa-chart-bar', label: 'التقارير' },
        ],
    };

    return [...commonItems, ...(roleSpecificItems[role] || [])];
}

function getDashboardUrl(role) {
    const dashboards = {
        'graduate': '/graduate/dashboard',
        'admin': '/admin/dashboard',
        'career_guidance_officer': '/career-guidance/dashboard',
        'partnership_officer': '/partnership/dashboard',
        'evaluation_followup': '/evaluation-followup/dashboard',
        'training_coordinator': '/training-coordinator/dashboard',
        'company': '/company/dashboard',
    };
    return dashboards[role] || '/dashboard';
}

function isCurrentPage(url) {
    return window.location.pathname === url || window.location.pathname.startsWith(url + '/');
}

function getUnreadCount() {
    const badge = document.getElementById('notification-badge');
    if (!badge) return null;
    const count = parseInt(badge.textContent.replace(/\D/g, ''), 10);
    return count > 0 ? (count > 99 ? '99+' : count.toString()) : null;
}

function createFAB() {
    const userRole = document.querySelector('meta[name="user-role"]')?.content;

    // تحديد الإجراء الرئيسي حسب الدور
    const fabActions = getFABActionsByRole(userRole);

    if (fabActions.length === 0) return;

    // إنشاء FAB
    const fab = document.createElement('button');
    fab.className = 'fab';
    fab.innerHTML = '<i class="fas fa-plus"></i>';

    // إنشاء قائمة الإجراءات
    const fabMenu = document.createElement('div');
    fabMenu.className = 'fab-menu';
    fabMenu.innerHTML = fabActions.map(action => `
        <div class="fab-menu-item">
            <span class="fab-menu-label">${action.label}</span>
            <a href="${action.url}" class="fab-menu-button">
                <i class="${action.icon}"></i>
            </a>
        </div>
    `).join('');

    document.body.appendChild(fab);
    document.body.appendChild(fabMenu);

    // Toggle menu
    fab.addEventListener('click', function () {
        fabMenu.classList.toggle('active');
        fab.querySelector('i').classList.toggle('fa-plus');
        fab.querySelector('i').classList.toggle('fa-times');
    });

    // إغلاق عند النقر خارج القائمة
    document.addEventListener('click', function (e) {
        if (!fab.contains(e.target) && !fabMenu.contains(e.target)) {
            fabMenu.classList.remove('active');
            fab.querySelector('i').classList.remove('fa-times');
            fab.querySelector('i').classList.add('fa-plus');
        }
    });
}

function getFABActionsByRole(role) {
    const actions = {
        'graduate': [
            { url: '/graduate/trainings', icon: 'fas fa-graduation-cap', label: 'تصفح التدريبات' },
            { url: '/graduate/job-opportunities', icon: 'fas fa-briefcase', label: 'تصفح الوظائف' },
            { url: '/graduate/profile', icon: 'fas fa-user-edit', label: 'تعديل الملف' },
        ],
        'training_coordinator': [
            { url: '/coordinator/trainings/create', icon: 'fas fa-plus-circle', label: 'إضافة برنامج تدريب' },
            { url: '/coordinator/applications', icon: 'fas fa-clipboard-check', label: 'إدارة الطلبات' },
            { url: '/coordinator/calendar', icon: 'fas fa-calendar-alt', label: 'تقويم التدريبات' },
        ],
        'admin': [
            { url: '/admin/career-guidance/graduates/create', icon: 'fas fa-user-plus', label: 'إضافة خريج' },
            { url: '/admin/trainings/create', icon: 'fas fa-plus-circle', label: 'إضافة تدريب' },
            { url: '/admin/companies', icon: 'fas fa-building', label: 'إدارة الشركات' },
            { url: '/job-opportunities/create', icon: 'fas fa-briefcase', label: 'إضافة وظيفة' },
        ],
        'company': [
            { url: '/job-opportunities/create', icon: 'fas fa-plus-circle', label: 'نشر فرصة عمل' },
            { url: '/company/profile', icon: 'fas fa-building', label: 'ملف الشركة' },
        ],
        'career_guidance_officer': [
            { url: '/career-guidance/graduates/create', icon: 'fas fa-user-plus', label: 'إضافة خريج' },
            { url: '/career-guidance/nominations/create', icon: 'fas fa-user-check', label: 'ترشيح جديد' },
        ],
        'partnership_officer': [
            { url: '/partnership/companies/create', icon: 'fas fa-building', label: 'إضافة شركة' },
            { url: '/job-opportunities/create', icon: 'fas fa-briefcase', label: 'إضافة فرصة' },
        ],
        'evaluation_followup': [
            { url: '/evaluation-followup/surveys/create', icon: 'fas fa-poll-h', label: 'إنشاء استبيان' },
            { url: '/evaluation-followup/evaluations/create', icon: 'fas fa-star', label: 'إضافة تقييم' },
        ],
        'media_officer': [
            { url: '/media/upload', icon: 'fas fa-cloud-upload-alt', label: 'رفع وسائط' },
            { url: '/media/news/create', icon: 'fas fa-newspaper', label: 'إضافة خبر' },
        ],
    };

    return actions[role] || [];
}

// تحديث Bottom Nav عند تغيير الصفحة
window.addEventListener('popstate', function () {
    const bottomNav = document.querySelector('.bottom-nav');
    if (bottomNav) {
        document.querySelectorAll('.bottom-nav-item').forEach(item => {
            item.classList.toggle('active', isCurrentPage(item.getAttribute('href')));
        });
    }
});
