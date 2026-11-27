/* Extracted from resources/views/layouts/app.blade.php so browsers can cache it. */
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing UI scripts...');

            // === شاشة الترحيب Splash Screen Logic ===
            // تظهر فقط في التطبيق (Native App) وليس في المتصفح
            // أو عند حدوث أخطاء في السيرفر

            const welcomeScreen = document.getElementById('welcome-screen');

            // التحقق من أن التطبيق يعمل في بيئة Capacitor (Native App)
            const isNativeApp = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();

            // دالة لإخفاء شاشة الترحيب
            window.hideSplashScreen = function () {
                if (welcomeScreen) {
                    welcomeScreen.classList.add('welcome-hidden');
                    setTimeout(() => {
                        welcomeScreen.style.display = 'none';
                    }, 800);
                }
            };

            // دالة لإظهار شاشة الترحيب (للأخطاء)
            window.showSplashScreen = function (message) {
                if (welcomeScreen && isNativeApp) {
                    welcomeScreen.style.display = 'flex';
                    welcomeScreen.classList.remove('welcome-hidden');
                    if (message) {
                        console.log('Splash Screen Message:', message);
                    }
                }
            };

            // إذا كان تطبيق Native، أظهر الشاشة ثم أخفها بعد ثانية واحدة
            if (isNativeApp) {
                if (welcomeScreen) {
                    welcomeScreen.style.display = 'flex';
                    setTimeout(() => {
                        window.hideSplashScreen();
                    }, 1000); // ثانية واحدة فقط
                }
            } else {
                // في المتصفح، أخفها فوراً
                if (welcomeScreen) {
                    welcomeScreen.style.display = 'none';
                }
            }


            // معالج الأخطاء - فقط للتسجيل في Console
            window.addEventListener('error', function (e) {
                if (isNativeApp) {
                    console.error('JavaScript Error:', e.error);
                }
            });

            // معالج الأخطاء غير المعالجة
            window.addEventListener('unhandledrejection', function (event) {
                if (isNativeApp) {
                    console.error('Unhandled Promise Rejection:', event.reason);
                }
            });

            // معالج أخطاء الشبكة
            window.addEventListener('offline', function () {
                if (isNativeApp) {
                    console.warn('No internet connection');
                }
            });

            // معالج أخطاء HTTP (للأخطاء من السيرفر)
            if (isNativeApp && window.fetch) {
                const originalFetch = window.fetch;
                window.fetch = function (...args) {
                    return originalFetch.apply(this, args)
                        .then(response => {
                            // التحقق من أخطاء HTTP
                            if (!response.ok) {
                                if (response.status >= 500) {
                                    showErrorSplash('خطأ في السيرفر');
                                } else if (response.status === 404) {
                                    showErrorSplash('الصفحة غير موجودة');
                                } else if (response.status === 403) {
                                    showErrorSplash('غير مصرح لك بالوصول');
                                } else if (response.status === 401) {
                                    showErrorSplash('يرجى تسجيل الدخول');
                                }
                            }
                            return response;
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            showErrorSplash('خطأ في الاتصال بالسيرفر');
                            throw error;
                        });
                };
            }

            // تعريف العناصر
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const navbarMain = document.getElementById('navbarMain');
            const toggleSidebar = document.getElementById('toggleSidebar');
            const toggleSidebarMain = document.getElementById('toggleSidebarMain');

            // إنشاء طبقة الـ Overlay للموبايل
            const sidebarOverlay = document.createElement('div');
            sidebarOverlay.className = 'sidebar-overlay';
            sidebarOverlay.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0, 0, 0, 0.5); z-index: 1035; display: none; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
            `;
            document.body.appendChild(sidebarOverlay);

            // دالة التبديل (Toggle Function)
            window.toggleSidebarFunc = function () {
                if (!sidebar) return;
                const isMobile = window.innerWidth < 768;

                if (isMobile) {
                    // منطق الموبايل
                    const isActive = sidebar.classList.contains('active-mobile');
                    if (isActive) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    // منطق الديسكتوب
                    sidebar.classList.toggle('collapsed');
                    mainContent?.classList.toggle('expanded');
                    navbarMain?.classList.toggle('expanded');

                    // تحديث الأيقونة
                    updateToggleIcon();

                    // حفظ الحالة
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            };

            function openMobileSidebar() {
                sidebar.classList.add('active-mobile');
                sidebar.classList.remove('collapsed');
                sidebarOverlay.style.display = 'block';
                sidebarOverlay.style.pointerEvents = 'auto';
                setTimeout(() => sidebarOverlay.style.opacity = '1', 10);
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('active-mobile');
                sidebar.classList.add('collapsed');
                sidebarOverlay.style.opacity = '0';
                sidebarOverlay.style.pointerEvents = 'none';
                setTimeout(() => {
                    sidebarOverlay.style.display = 'none';
                }, 300);
            }

            function updateToggleIcon() {
                const isMobile = window.innerWidth < 768;
                const isCollapsed = sidebar.classList.contains('collapsed');
                if (toggleSidebar) {
                    const icon = toggleSidebar.querySelector('i');
                    if (icon) icon.className = isCollapsed ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
                }
                const floatingBtn = document.getElementById('floatingSidebarToggle');
                if (floatingBtn) {
                    floatingBtn.style.display = (!isMobile && isCollapsed) ? 'flex' : 'none';
                }
                if (!isMobile && isCollapsed) {
                    document.body.classList.add('sidebar-is-collapsed');
                } else {
                    document.body.classList.remove('sidebar-is-collapsed');
                }
            }

            window.openMobileSidebar = openMobileSidebar;
            window.closeMobileSidebar = closeMobileSidebar;

            window.addEventListener('resize', function () {
                updateToggleIcon();
            });

            // ربط الأزرار بالدالة
            if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarFunc);
            if (toggleSidebarMain) toggleSidebarMain.addEventListener('click', toggleSidebarFunc);
            const floatingToggleBtn = document.getElementById('floatingSidebarToggle');
            if (floatingToggleBtn) floatingToggleBtn.addEventListener('click', toggleSidebarFunc);

            // النقر على Overlay يغلق القائمة
            sidebarOverlay.addEventListener('click', closeMobileSidebar);

            // استعادة الحالة عند التحميل
            function restoreSidebarState() {
                const isMobile = window.innerWidth < 768;
                const savedState = localStorage.getItem('sidebarCollapsed');

                if (isMobile) {
                    sidebar.classList.add('collapsed');
                    sidebar.classList.remove('active-mobile');
                    mainContent?.classList.add('expanded');
                    navbarMain?.classList.add('expanded');
                    updateToggleIcon();
                } else {
                    // الديسكتوب
                    if (savedState === 'true') {
                        sidebar.classList.add('collapsed');
                        mainContent?.classList.add('expanded');
                        navbarMain?.classList.add('expanded');
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent?.classList.remove('expanded');
                        navbarMain?.classList.remove('expanded');
                    }
                    updateToggleIcon();
                }
            }

            // التعامل مع القوائم الفرعية - نظام أكورديون سلس (إغلاق القوائم الأخرى لتفادي الطول الزائد)
            window.toggleSubmenu = function (menuId, event) {
                const evt = event || window.event;
                if (evt) {
                    try { evt.preventDefault(); } catch (e) { }
                    try { evt.stopPropagation(); } catch (e) { }
                }
                const submenu = document.getElementById(menuId);
                if (!submenu) return;
                const menuGroup = submenu.closest('.menu-group');
                const isAlreadyOpen = submenu.classList.contains('show');

                // إغلاق كل القوائم الفرعية المفتوحة الأخرى تلقائياً لتقليص طول الشريط الجانبي (Accordion Mode)
                document.querySelectorAll('.sidebar .submenu.show').forEach(s => {
                    if (s !== submenu) {
                        s.classList.remove('show');
                        const mg = s.closest('.menu-group');
                        if (mg) mg.classList.remove('active');
                    }
                });

                if (isAlreadyOpen) {
                    submenu.classList.remove('show');
                    if (menuGroup) menuGroup.classList.remove('active');
                } else {
                    submenu.classList.add('show');
                    if (menuGroup) menuGroup.classList.add('active');
                }
            };

            // تفعيل الروابط النشطة
            const currentPath = window.location.pathname;
            document.querySelectorAll('.submenu-item').forEach(item => {
                if (item.href && currentPath.includes(new URL(item.href).pathname)) {
                    item.classList.add('active');
                    const submenu = item.closest('.submenu');
                    const menuGroup = item.closest('.menu-group');
                    if (submenu) submenu.classList.add('show');
                    if (menuGroup) menuGroup.classList.add('active');
                }
            });

            // ==========================================
            // نظام إدارة الإشعارات الفوري (AJAX & Realtime)
            // ==========================================
            const notifDropdownBtn = document.getElementById('notificationsDropdown');
            const notifList = document.getElementById('notifications-list');
            const notifHeaderCount = document.getElementById('notification-header-count');
            const markAllBtn = document.getElementById('markAllNotificationsReadBtn');
            const clearReadBtn = document.getElementById('clearReadNotificationsBtn');
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

            // دالة تحديث عداد الإشعارات في الواجهة
            function updateUnreadBadge(count) {
                const badge = document.getElementById('notification-badge');
                if (count > 0) {
                    if (badge) {
                        badge.innerText = count;
                        badge.style.display = 'inline-block';
                    } else if (notifDropdownBtn) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notification-badge';
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.innerText = count;
                        newBadge.innerHTML = count + '<span class="visually-hidden">unread messages</span>';
                        notifDropdownBtn.appendChild(newBadge);
                    }
                    if (notifHeaderCount) {
                        notifHeaderCount.innerText = count + ' غير مقروء';
                        notifHeaderCount.style.display = 'inline-block';
                    }
                } else {
                    if (badge) badge.remove();
                    if (notifHeaderCount) {
                        notifHeaderCount.innerText = '0 غير مقروء';
                        notifHeaderCount.style.display = 'none';
                    }
                }
            }

            // دالة جلب وعرض الإشعارات في القائمة المنسدلة
            function fetchNotificationsList() {
                if (!notifList) return;
                notifList.innerHTML = `
                    <li class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div>
                        <span class="small">جاري تحميل الإشعارات...</span>
                    </li>
                `;

                fetch('/notifications/api/notifications', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        const notifications = data.notifications || [];
                        const unreadCount = data.unread_count ?? 0;
                        updateUnreadBadge(unreadCount);

                        if (notifications.length === 0) {
                            notifList.innerHTML = `
                            <li class="text-center py-4 text-muted">
                                <i class="fas fa-bell-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                <span class="small">لا توجد إشعارات حتى الآن</span>
                            </li>
                        `;
                            return;
                        }

                        let html = '';
                        notifications.forEach(n => {
                            const isUnread = !n.is_read;
                            const iconClass = n.type === 'success' ? 'check-circle text-success' :
                                (n.type === 'danger' ? 'exclamation-circle text-danger' :
                                    (n.type === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-primary'));
                            const bgClass = isUnread ? 'bg-primary-subtle bg-opacity-10' : 'bg-transparent';
                            const timeString = n.created_at ? new Date(n.created_at).toLocaleDateString('ar-LY', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

                            html += `
                            <li class="border-bottom notification-item-row notification-item-${n.id} ${bgClass}" style="transition: all 0.3s ease;">
                                <div class="d-flex align-items-center justify-content-between p-1">
                                    <a href="/notifications/${n.id}" class="d-flex align-items-start p-2 text-decoration-none text-dark notification-link flex-grow-1 min-w-0" style="transition: background 0.15s ease;">
                                        <div class="me-3 mt-1 flex-shrink-0">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(15, 23, 42, 0.06);">
                                                <i class="fas fa-${iconClass} fs-6"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                <h6 class="mb-0 fw-bold fs-6 text-truncate text-slate-800" style="max-width: 190px;">${n.title || 'إشعار جديد'}</h6>
                                                <small class="text-muted text-nowrap ms-2" style="font-size: 0.7rem;">${timeString}</small>
                                            </div>
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 230px;">${n.message || ''}</p>
                                        </div>
                                        ${isUnread ? '<span class="badge bg-primary rounded-circle p-1 ms-2 mt-2 flex-shrink-0" style="width: 8px; height: 8px;" title="غير مقروء"></span>' : ''}
                                    </a>
                                    ${isUnread ? `
                                        <button onclick="markSingleDropdownNotificationAsRead(${n.id}, this)" class="btn btn-sm btn-light border-0 text-success p-1 me-2 rounded-circle flex-shrink-0" title="تحديد كمقروء وإخفاء" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </li>
                        `;
                        });
                        notifList.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Error fetching notifications:', err);
                        notifList.innerHTML = `
                        <li class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            <span class="small">تعذر جلب الإشعارات حالياً</span>
                        </li>
                    `;
                    });
            }

            // تحديد إشعار فردي كمقروء وإخفاؤه مباشرة من القائمة المنسدلة
            window.markSingleDropdownNotificationAsRead = function(id, btn) {
                if (window.event) {
                    window.event.preventDefault();
                    window.event.stopPropagation();
                }
                const li = btn.closest('li');
                if (li) {
                    li.style.transition = 'all 0.3s ease';
                    li.style.opacity = '0';
                    li.style.transform = 'translateX(25px)';
                }
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (li) setTimeout(() => {
                        li.remove();
                        const remaining = notifList ? notifList.querySelectorAll('li') : [];
                        if (remaining.length === 0) {
                            notifList.innerHTML = `
                                <li class="text-center py-4 text-muted">
                                    <i class="fas fa-bell-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                    <span class="small">لا توجد إشعارات حتى الآن</span>
                                </li>
                            `;
                        }
                    }, 250);
                    updateUnreadBadge(data.unread_count ?? 0);
                })
                .catch(err => {
                    console.error(err);
                    if (li) {
                        li.style.opacity = '1';
                        li.style.transform = 'none';
                    }
                });
            };

            // الاستماع لفتح القائمة المنسدلة - نقل القائمة إلى الـ body لتجنب overflow:hidden
            if (notifDropdownBtn) {
                const dropdownMenu = notifDropdownBtn.closest('.dropdown')?.querySelector('.dropdown-menu');
                const originalParent = dropdownMenu?.parentElement;

                notifDropdownBtn.addEventListener('show.bs.dropdown', function () {
                    fetchNotificationsList();

                    // نقل القائمة إلى الـ body لتجاوز overflow:hidden
                    if (dropdownMenu && originalParent) {
                        const rect = notifDropdownBtn.getBoundingClientRect();
                        dropdownMenu.style.position = 'fixed';
                        dropdownMenu.style.top = (rect.bottom + 6) + 'px';
                        dropdownMenu.style.right = 'auto';
                        dropdownMenu.style.left = rect.left + 'px';
                        dropdownMenu.style.zIndex = '9999';
                        dropdownMenu.style.display = 'block';
                        document.body.appendChild(dropdownMenu);
                    }
                });

                notifDropdownBtn.addEventListener('hide.bs.dropdown', function () {
                    // إعادة القائمة لمكانها الأصلي
                    if (dropdownMenu && originalParent && dropdownMenu.parentElement === document.body) {
                        dropdownMenu.style.position = '';
                        dropdownMenu.style.top = '';
                        dropdownMenu.style.left = '';
                        dropdownMenu.style.zIndex = '';
                        dropdownMenu.style.display = '';
                        originalParent.appendChild(dropdownMenu);
                    }
                });
            }

            // زر تحديد كافة الإشعارات كمقروءة (تختفي فورياً)
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري التحديد...';

                    // إخفاء فوري لجميع الإشعارات غير المقروءة من القائمة
                    const unreadRows = notifList ? notifList.querySelectorAll('.bg-primary-subtle') : [];
                    unreadRows.forEach(r => {
                        r.style.transition = 'all 0.3s ease';
                        r.style.opacity = '0';
                        r.style.transform = 'translateX(20px)';
                        setTimeout(() => r.remove(), 250);
                    });

                    fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(() => {
                            updateUnreadBadge(0);
                            markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تم التحديد';
                            setTimeout(() => {
                                markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء';
                                const remaining = notifList ? notifList.querySelectorAll('li') : [];
                                if (remaining.length === 0) {
                                    notifList.innerHTML = `
                                        <li class="text-center py-4 text-muted">
                                            <i class="fas fa-bell-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                            <span class="small">لا توجد إشعارات جديدة</span>
                                        </li>
                                    `;
                                }
                            }, 300);
                        })
                        .catch(err => {
                            console.error('Error marking all as read:', err);
                            markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء';
                        });
                });
            }

            // زر مسح الإشعارات المقروءة
            if (clearReadBtn) {
                clearReadBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!confirm('هل تريد مسح جميع الإشعارات المقروءة؟')) return;

                    clearReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري المسح...';

                    fetch('/notifications/read/delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(() => {
                            fetchNotificationsList();
                            clearReadBtn.innerHTML = '<i class="fas fa-trash-alt text-danger me-1"></i> تنظيف المقروء';
                        })
                        .catch(err => {
                            console.error('Error clearing read notifications:', err);
                            clearReadBtn.innerHTML = '<i class="fas fa-trash-alt text-danger me-1"></i> تنظيف المقروء';
                        });
                });
            }

            // فحص دوري للإشعارات في الخلفية كل 60 ثانية لتحديث العداد تلقائياً
            setInterval(() => {
                fetch('/notifications/api/notifications', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data && typeof data.unread_count !== 'undefined') {
                            updateUnreadBadge(data.unread_count);
                        }
                    })
                    .catch(() => { });
            }, 60000);

            // إغلاق القائمة عند النقر على روابط (موبايل فقط)
            const allLinks = document.querySelectorAll('#sidebar a');
            allLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        const isToggle = this.classList.contains('dropdown-toggle') || this.getAttribute('onclick');
                        if (!isToggle) closeMobileSidebar();
                    }
                });
            });

            // إضافة CSS الخاص بالموبايل
            const mobileStyle = document.createElement('style');
            mobileStyle.innerHTML = `
                @media (max-width: 768px) {
                    html, body {
                        overflow-x: hidden !important;
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch !important;
                        position: static !important;
                        height: auto !important;
                    }
                    .sidebar {
                        transform: translateX(100%);
                        transition: transform 0.3s ease-in-out;
                        position: fixed !important; top: 0; right: 0; height: 100vh; z-index: 1040; width: 280px !important;
                    }
                    .sidebar.active-mobile { transform: translateX(0) !important; }
                    .sidebar.collapsed { transform: translateX(100%) !important; }
                    .main-content, .navbar-main { margin-right: 0 !important; width: 100% !important; overflow-y: visible !important; }
                }
            `;
            document.head.appendChild(mobileStyle);

            restoreSidebarState();

            // التعامل مع تغيير حجم الشاشة
            window.addEventListener('resize', () => {
                const isMobile = window.innerWidth < 768;
                if (!isMobile) {
                    // إزالة آثار الموبايل عند التكبير
                    sidebarOverlay.style.display = 'none';
                    sidebar.classList.remove('active-mobile');
                    // استعادة حالة الديسكتوب
                    restoreSidebarState();
                }
            });
        });
