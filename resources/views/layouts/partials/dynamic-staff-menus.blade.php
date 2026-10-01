{{-- 
    =============================================================================
    🚀 القوائم الديناميكية للموظفين بحسب الصلاحيات الممنوحة
    Dynamic Permission-Based Menus for Staff & Officers
    تضمن ظهور أي وحدة إدارية يُمنح الموظف صلاحيتها فوراً في الشريط الجانبي
    =============================================================================
--}}

@auth
    @if(auth()->user()->isStaff() && !auth()->user()->isAdmin())

        {{-- 🎪 1. معرض التوظيف والفعاليات السنوية --}}
        @if(auth()->user()->canManageJobFair() || auth()->user()->hasAnyPermission([
            'job_fair.view', 'job_fair.create', 'job_fair.edit', 'job_fair.delete',
            'job_fair.manage', 'job_fair.events', 'job_fair.projects', 'job_fair.sponsors',
            'job_fair.registrations', 'job_fair.attendance', 'job_fair.live'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('job-fair.*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-jobfair-menu')">
                    <i class="fas fa-store text-warning"></i>
                    معرض التوظيف والفعاليات
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('job-fair.*') ? 'show' : '' }}" id="dyn-jobfair-menu">
                    <a href="{{ route('job-fair.admin.index') }}"
                        class="submenu-item {{ request()->routeIs('job-fair.admin.index') || request()->routeIs('job-fair.admin.show') || request()->routeIs('job-fair.admin.edit') ? 'active' : '' }}">
                        <i class="fas fa-layer-group me-1 opacity-75"></i> إدارة المعارض والفعاليات
                    </a>
                    <a href="{{ route('job-fair.tasks.index') }}"
                        class="submenu-item {{ request()->routeIs('job-fair.tasks.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks me-1 opacity-75"></i> مهام المعرض 2026
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->hasAnyPermission(['job_fair.attendance', 'job_fair.manage']))
                        <a href="{{ route('job-fair.admin.attendance', 1) }}"
                            class="submenu-item {{ request()->routeIs('job-fair.admin.attendance') ? 'active' : '' }}">
                            <i class="fas fa-qrcode me-1 opacity-75"></i> مسح التذاكر والـ QR
                        </a>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->hasAnyPermission(['job_fair.live', 'job_fair.view']))
                        <a href="{{ route('job-fair.admin.live', 1) }}"
                            class="submenu-item {{ request()->routeIs('job-fair.admin.live') ? 'active' : '' }}">
                            <i class="fas fa-broadcast-tower me-1 opacity-75"></i> شاشة البث المباشر
                        </a>
                    @endif
                    <a href="{{ route('job-fair.public') }}" target="_blank" class="submenu-item">
                        <i class="fas fa-external-link-alt me-1 opacity-75"></i> بوابة المعرض العامة
                    </a>
                </div>
            </li>
        @endif

        {{-- 🎓 2. برامج التدريب والتأهيل (للأدوار الأخرى عند امتلاك الصلاحية) --}}
        @if(auth()->user()->role != 'training_coordinator' && auth()->user()->hasAnyPermission([
            'trainings.view', 'trainings.create', 'trainings.edit', 'trainings.applications',
            'trainings.attendance', 'trainings.trainers'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-trainings-menu')">
                    <i class="fas fa-graduation-cap text-purple"></i>
                    برامج التدريب والتأهيل
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'show' : '' }}" id="dyn-trainings-menu">
                    @if(auth()->user()->hasPermission('trainings.view'))
                        <a href="{{ route('training-coordinator.trainings') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.trainings') ? 'active' : '' }}">
                            عرض البرامج التدريبية
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.create'))
                        <a href="{{ route('training-coordinator.trainings.create') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}">
                            إضافة برنامج تدريبي
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.applications'))
                        <a href="{{ route('training-coordinator.applications') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.applications*') ? 'active' : '' }}">
                            طلبات التدريب
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.attendance'))
                        <a href="{{ route('training-coordinator.calendar') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.calendar') ? 'active' : '' }}">
                            تقويم التدريبات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.trainers'))
                        <a href="{{ route('training-coordinator.trainers.index') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.trainers*') ? 'active' : '' }}">
                            إدارة المدربين
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.view'))
                        <a href="{{ route('training-coordinator.reports') }}"
                            class="submenu-item {{ request()->routeIs('training-coordinator.reports') ? 'active' : '' }}">
                            تقارير التدريب
                        </a>
                    @endif
                </div>
            </li>
        @endif

        {{-- 🤝 3. الشراكات وفرص العمل (للأدوار الأخرى عند امتلاك الصلاحية) --}}
        @if(auth()->user()->role != 'partnership_officer' && auth()->user()->hasAnyPermission([
            'companies.view', 'companies.create', 'companies.edit',
            'jobs.view', 'jobs.manage', 'partnerships.documents', 'nominations.manage'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('partnership.*') || request()->routeIs('job-opportunities*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-partnerships-menu')">
                    <i class="fas fa-handshake text-success"></i>
                    الشراكات والتوظيف
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('partnership.*') || request()->routeIs('job-opportunities*') ? 'show' : '' }}" id="dyn-partnerships-menu">
                    @if(auth()->user()->hasPermission('companies.view'))
                        <a href="{{ route('partnership.companies') }}"
                            class="submenu-item {{ request()->routeIs('partnership.companies*') ? 'active' : '' }}">
                            دليل الشركات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('jobs.view'))
                        <a href="{{ route('job-opportunities.index') }}"
                            class="submenu-item {{ request()->routeIs('job-opportunities*') ? 'active' : '' }}">
                            فرص العمل
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('partnerships.documents'))
                        <a href="{{ route('partnership.documents') }}"
                            class="submenu-item {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}">
                            إدارة الوثائق
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('nominations.manage'))
                        <a href="{{ route('partnership.nominations') }}"
                            class="submenu-item {{ request()->routeIs('partnership.nominations*') ? 'active' : '' }}">
                            إدارة الترشيحات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.view'))
                        <a href="{{ route('partnership.reports') }}"
                            class="submenu-item {{ request()->routeIs('partnership.reports*') ? 'active' : '' }}">
                            تقارير الشراكات
                        </a>
                    @endif
                </div>
            </li>
        @endif

        {{-- 🧭 4. شؤون الخريجين والإرشاد المهني (للأدوار الأخرى عند امتلاك الصلاحية) --}}
        @if(auth()->user()->role != 'career_guidance_officer' && auth()->user()->hasAnyPermission([
            'graduates.view', 'graduates.create', 'graduates.edit', 'graduates.approve',
            'graduates.import_export', 'nominations.manage'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('career-guidance.*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-career-menu')">
                    <i class="fas fa-user-graduate text-primary"></i>
                    شؤون الخريجين والإرشاد
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('career-guidance.*') ? 'show' : '' }}" id="dyn-career-menu">
                    @if(auth()->user()->hasPermission('graduates.view'))
                        <a href="{{ route('career-guidance.graduates') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.graduates') ? 'active' : '' }}">
                            بيانات الخريجين
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('graduates.approve'))
                        <a href="{{ route('career-guidance.pending-approvals') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.pending-approvals') ? 'active' : '' }}">
                            طلبات التسجيل
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('graduates.create'))
                        <a href="{{ route('career-guidance.graduates.create') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.graduates.create') ? 'active' : '' }}">
                            إضافة خريج جديد
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('nominations.manage'))
                        <a href="{{ route('career-guidance.nominations') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.nominations*') ? 'active' : '' }}">
                            إدارة الترشيحات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('graduates.import_export'))
                        <a href="{{ route('career-guidance.import.graduates.create') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.import.graduates*') ? 'active' : '' }}">
                            استيراد Excel
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.view'))
                        <a href="{{ route('career-guidance.advanced-reports') }}"
                            class="submenu-item {{ request()->routeIs('career-guidance.advanced-reports*') ? 'active' : '' }}">
                            التقارير المتقدمة
                        </a>
                    @endif
                </div>
            </li>
        @endif

        {{-- 📊 5. التقييم والاستبيانات (للأدوار الأخرى عند امتلاك الصلاحية) --}}
        @if(auth()->user()->role != 'evaluation_followup' && auth()->user()->hasAnyPermission([
            'surveys.manage', 'evaluations.manage'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('evaluation-followup.*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-evaluations-menu')">
                    <i class="fas fa-poll-h text-info"></i>
                    التقييم والاستبيانات
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('evaluation-followup.*') ? 'show' : '' }}" id="dyn-evaluations-menu">
                    @if(auth()->user()->hasPermission('surveys.manage'))
                        <a href="{{ route('evaluation-followup.surveys.index') }}"
                            class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.index') ? 'active' : '' }}">
                            إدارة الاستبيانات
                        </a>
                        <a href="{{ route('evaluation-followup.surveys.templates.index') }}"
                            class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.templates*') ? 'active' : '' }}">
                            مكتبة قوالب ونماذج الاستبيانات
                        </a>
                        <a href="{{ route('evaluation-followup.survey-responses.index') }}"
                            class="submenu-item {{ request()->routeIs('evaluation-followup.survey-responses*') ? 'active' : '' }}">
                            ردود الاستبيانات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('evaluations.manage'))
                        <a href="{{ route('evaluation-followup.evaluations.index') }}"
                            class="submenu-item {{ request()->routeIs('evaluation-followup.evaluations*') ? 'active' : '' }}">
                            إدارة التقييمات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.view'))
                        <a href="{{ route('evaluation-followup.performance-reports') }}"
                            class="submenu-item {{ request()->routeIs('evaluation-followup.performance-reports') ? 'active' : '' }}">
                            تقارير الأداء والاستبيانات
                        </a>
                    @endif
                </div>
            </li>
        @endif

        {{-- 📸 6. الإعلام والتغطيات (للأدوار الأخرى عند امتلاك الصلاحية) --}}
        @if(auth()->user()->role != 'media_officer' && auth()->user()->hasAnyPermission([
            'media.manage', 'news.manage'
        ]))
            <li class="nav-item menu-group">
                <a class="nav-link {{ request()->routeIs('media.*') ? 'active' : '' }}"
                    href="#" onclick="toggleSubmenu('dyn-media-menu')">
                    <i class="fas fa-camera text-orange"></i>
                    الإعلام والتغطيات
                    <i class="fas fa-chevron-down menu-arrow"></i>
                </a>
                <div class="submenu {{ request()->routeIs('media.*') ? 'show' : '' }}" id="dyn-media-menu">
                    @if(auth()->user()->hasPermission('media.manage'))
                        <a href="{{ route('media.coverage-calendar') }}"
                            class="submenu-item {{ request()->routeIs('media.coverage-calendar*') ? 'active' : '' }}">
                            تقويم التغطيات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('news.manage'))
                        <a href="{{ route('media.news.index') }}"
                            class="submenu-item {{ request()->routeIs('media.news*') ? 'active' : '' }}">
                            الأخبار الصحفية
                        </a>
                        <a href="{{ route('media.announcements.index') }}"
                            class="submenu-item {{ request()->routeIs('media.announcements*') ? 'active' : '' }}">
                            التعميمات والإعلانات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('media.manage'))
                        <a href="{{ route('media.reports.coverage') }}"
                            class="submenu-item {{ request()->routeIs('media.reports.coverage*') ? 'active' : '' }}">
                            تقارير التغطية الإعلامية
                        </a>
                    @endif
                </div>
            </li>
        @endif

    @endif
@endauth
