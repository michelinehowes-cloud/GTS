@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الشراكات والتوظيف')

@section('content')
<div class="container-fluid partnership-dashboard">
    <!-- الهيدر الرئيسي -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="header-content">
                    <h1 class="header-title">
                        <i class="fas fa-handshake"></i>
                        لوحة التحكم - الشراكات والتوظيف
                    </h1>
                    <p class="header-subtitle">إدارة الشراكات الاستراتيجية وفرص التوظيف للخريجين</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="header-stats">
                    <div class="stat-badge">
                        <span class="stat-number">{{ $stats['totalCompanies'] }}</span>
                        <span class="stat-label">شركة شريكة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- شبكة الإحصائيات -->
    <div class="stats-grid">
        <div class="row g-3">
            <!-- بطاقة الشركات -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card company-card">
                    <div class="card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="card-content">
                        <div class="stat-number">{{ $stats['totalCompanies'] }}</div>
                        <div class="stat-label">إجمالي الشركات</div>
                        <div class="stat-trend">
                            <span class="trend-up">{{ $stats['activePartnerships'] }} نشطة</span>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('partnership.companies') }}" class="action-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- بطاقة الفرص -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card opportunity-card">
                    <div class="card-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="card-content">
                        <div class="stat-number">{{ $stats['totalOpportunities'] }}</div>
                        <div class="stat-label">فرص العمل</div>
                        <div class="stat-trend">
                            <span class="trend-up">{{ $stats['openOpportunities'] }} مفتوحة</span>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('job-opportunities.index') }}" class="action-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- بطاقة الخريجين -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card graduate-card">
                    <div class="card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="card-content">
                        <div class="stat-number">{{ $stats['totalGraduates'] }}</div>
                        <div class="stat-label">خريج مسجل</div>
                        <div class="stat-trend">
                            <span class="trend-up">{{ $stats['activeNominations'] }} ترشيح</span>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('partnership.import-graduates') }}" class="action-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- بطاقة الوثائق -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card document-card">
                    <div class="card-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="card-content">
                        <div class="stat-number">{{ $stats['totalDocuments'] }}</div>
                        <div class="stat-label">وثيقة شراكة</div>
                        <div class="stat-trend">
                            <span class="trend-down">{{ $stats['expiringDocuments'] }} منتهية قريباً</span>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('partnership.documents') }}" class="action-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="dashboard-content">
        <div class="row g-4">
            <!-- الفرص الحديثة -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i>
                            الفرص الحديثة
                        </h3>
                        <a href="{{ route('job-opportunities.index') }}" class="header-action">
                            عرض الكل <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentOpportunities->count() > 0)
                            <div class="opportunities-list">
                                @foreach($recentOpportunities as $opportunity)
                                <div class="opportunity-item">
                                    <div class="opportunity-icon">
                                        @if($opportunity->type == 'job')
                                            <i class="fas fa-briefcase text-primary"></i>
                                        @elseif($opportunity->type == 'training')
                                            <i class="fas fa-chalkboard-teacher text-success"></i>
                                        @else
                                            <i class="fas fa-user-graduate text-info"></i>
                                        @endif
                                    </div>
                                    <div class="opportunity-details">
                                        <h4 class="opportunity-title">{{ $opportunity->title }}</h4>
                                        <p class="opportunity-company">{{ $opportunity->company->name }}</p>
                                        <div class="opportunity-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $opportunity->location }}
                                            </span>
                                            <span class="meta-item">
                                                <i class="fas fa-calendar"></i>
                                                {{ $opportunity->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="opportunity-status">
                                        <span class="status-badge status-{{ $opportunity->status }}">
                                            {{ $opportunity->status == 'open' ? 'مفتوحة' : 'مغلقة' }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-briefcase"></i>
                                <h4>لا توجد فرص حديثة</h4>
                                <p>يمكنك البدء بإضافة أول فرصة عمل</p>
                                <a href="{{ route('job-opportunities.create') }}" class="btn btn-primary">
                                    إضافة فرصة جديدة
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الوثائق الحديثة -->
            <div class="col-lg-6">
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-contract"></i>
                            وثائق الشراكة
                        </h3>
                        <a href="{{ route('partnership.documents') }}" class="header-action">
                            عرض الكل <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentDocuments->count() > 0)
                            <div class="documents-list">
                                @foreach($recentDocuments as $document)
                                <div class="document-item">
                                    <div class="document-icon">
                                        <i class="fas fa-file-signature text-warning"></i>
                                    </div>
                                    <div class="document-details">
                                        <h4 class="document-title">{{ $document->document_name }}</h4>
                                        <p class="document-company">{{ $document->company->name }}</p>
                                        <div class="document-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-tag"></i>
                                                {{ $document->document_type_text }}
                                            </span>
                                            @if($document->expiry_date)
                                            <span class="meta-item">
                                                <i class="fas fa-clock"></i>
                                                {{ $document->expiry_date->diffForHumans() }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="document-status">
                                        <span class="status-badge status-{{ $document->document_status }}">
                                            {{ $document->document_status_text }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-file-contract"></i>
                                <h4>لا توجد وثائق</h4>
                                <p>يمكنك البدء برفع أول وثيقة شراكة</p>
                                <a href="{{ route('partnership.documents.create') }}" class="btn btn-primary">
                                    رفع وثيقة جديدة
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإجراءات السريعة -->
    <div class="quick-actions">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('partnership.companies') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="action-content">
                        <h3>إدارة الشركات</h3>
                        <p>عرض وتعديل الشركات الشريكة</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('job-opportunities.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-content">
                        <h3>فرصة جديدة</h3>
                        <p>إضافة فرصة عمل أو تدريب</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('partnership.import-graduates') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="action-content">
                        <h3>استيراد بيانات</h3>
                        <p>استيراد بيانات الخريجين</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('partnership.reports') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="action-content">
                        <h3>التقارير</h3>
                        <p>تقارير وإحصائيات مفصلة</p>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #2c5aa0;
    --primary-dark: #1e3d72;
    --primary-light: #4a7bc8;
    --secondary-color: #6c757d;
    --success-color: #28a745;
    --info-color: #17a2b8;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    --gradient-success: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    --gradient-info: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
    --gradient-warning: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.partnership-dashboard {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* الهيدر المحسن */
.dashboard-header {
    background: var(--gradient-primary);
    color: white;
    padding: 2.5rem 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 15px 35px rgba(44, 90, 160, 0.3);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.dashboard-header::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: -50px;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-title i {
    margin-left: 1rem;
    color: rgba(255, 255, 255, 0.9);
}

.header-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 0;
    position: relative;
    z-index: 2;
    font-weight: 300;
}

.header-stats {
    text-align: left;
    position: relative;
    z-index: 2;
}

.stat-badge {
    background: rgba(255, 255, 255, 0.15);
    padding: 1.2rem 1.8rem;
    border-radius: 18px;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 800;
    display: block;
    line-height: 1;
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
}

/* بطاقات الإحصائيات المحسنة */
.stats-grid {
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.8rem;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient-primary);
}

.company-card::before { background: var(--gradient-primary); }
.opportunity-card::before { background: var(--gradient-success); }
.graduate-card::before { background: var(--gradient-info); }
.document-card::before { background: var(--gradient-warning); }

.card-icon {
    width: 80px;
    height: 80px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1.5rem;
    font-size: 2rem;
    color: white;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.stat-card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.company-card .card-icon { 
    background: var(--gradient-primary);
    box-shadow: 0 8px 20px rgba(44, 90, 160, 0.3);
}
.opportunity-card .card-icon { 
    background: var(--gradient-success);
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
}
.graduate-card .card-icon { 
    background: var(--gradient-info);
    box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
}
.document-card .card-icon { 
    background: var(--gradient-warning);
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
}

.card-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.5rem;
    background: none;
    -webkit-text-fill-color: initial;
    background-clip: initial;
}

.company-card .stat-number { color: var(--primary-color); }
.opportunity-card .stat-number { color: var(--success-color); }
.graduate-card .stat-number { color: var(--info-color); }
.document-card .stat-number { color: var(--warning-color); }

.stat-label {
    font-size: 1.1rem;
    color: #64748b;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.stat-trend {
    font-size: 0.9rem;
    font-weight: 500;
}

.trend-up { 
    color: var(--success-color);
    background: rgba(40, 167, 69, 0.1);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    display: inline-block;
}
.trend-down { 
    color: var(--danger-color);
    background: rgba(220, 53, 69, 0.1);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    display: inline-block;
}

.card-action {
    margin-right: auto;
}

.action-link {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.action-link:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(44, 90, 160, 0.4);
}

/* بطاقات المحتوى المحسنة */
.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
}

.content-card:hover {
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

.card-header {
    padding: 1.8rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
}

.card-title i {
    margin-left: 0.75rem;
    color: var(--primary-color);
    font-size: 1.6rem;
}

.header-action {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    background: rgba(44, 90, 160, 0.1);
    transition: all 0.3s ease;
}

.header-action:hover {
    background: var(--primary-color);
    color: white;
    transform: translateX(-3px);
}

.header-action i {
    margin-right: 0.5rem;
    transition: all 0.3s ease;
}

.header-action:hover i {
    transform: translateX(-3px);
}

.card-body {
    padding: 1.8rem;
}

/* قوائم العناصر المحسنة */
.opportunities-list,
.documents-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.opportunity-item,
.document-item {
    display: flex;
    align-items: center;
    padding: 1.2rem;
    background: #f8fafc;
    border-radius: 15px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.opportunity-item:hover,
.document-item:hover {
    background: white;
    transform: translateX(-5px);
    border-color: var(--primary-color);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.opportunity-icon,
.document-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1.2rem;
    font-size: 1.4rem;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.opportunity-item:hover .opportunity-icon,
.document-item:hover .document-icon {
    transform: scale(1.1);
}

.opportunity-details,
.document-details {
    flex: 1;
}

.opportunity-title,
.document-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.3rem;
}

.opportunity-company,
.document-company {
    font-size: 0.95rem;
    color: #64748b;
    margin-bottom: 0.6rem;
    font-weight: 500;
}

.opportunity-meta,
.document-meta {
    display: flex;
    gap: 1.2rem;
    font-size: 0.85rem;
}

.meta-item {
    color: #94a3b8;
    display: flex;
    align-items: center;
    font-weight: 500;
}

.meta-item i {
    margin-left: 0.3rem;
    font-size: 0.9rem;
}

.opportunity-status,
.document-status {
    margin-right: auto;
}

.status-badge {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-open { 
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #10b981;
}
.status-closed { 
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}
.status-active { 
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #10b981;
}
.status-expired { 
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #ef4444;
}

/* الحالة الفارغة المحسنة */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.empty-state i {
    font-size: 4.5rem;
    margin-bottom: 1.5rem;
    opacity: 0.3;
    color: var(--primary-color);
}

.empty-state h4 {
    color: #475569;
    margin-bottom: 0.8rem;
    font-size: 1.4rem;
    font-weight: 600;
}

.empty-state p {
    margin-bottom: 2rem;
    font-size: 1rem;
    opacity: 0.8;
}

.empty-state .btn {
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(44, 90, 160, 0.3);
}

/* الإجراءات السريعة المحسنة */
.quick-actions {
    margin-top: 3rem;
}

.action-card {
    background: white;
    border-radius: 20px;
    padding: 2.2rem;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 4px;
    background: var(--gradient-primary);
}

.action-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    border-color: var(--primary-color);
    color: inherit;
}

.action-icon {
    width: 75px;
    height: 75px;
    border-radius: 18px;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 1.8rem;
    font-size: 2rem;
    color: white;
    box-shadow: 0 8px 20px rgba(44, 90, 160, 0.3);
    transition: all 0.3s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 25px rgba(44, 90, 160, 0.4);
}

.action-content {
    flex: 1;
}

.action-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.6rem;
}

.action-content p {
    color: #64748b;
    margin-bottom: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

.action-arrow {
    color: #cbd5e1;
    font-size: 1.4rem;
    transition: all 0.3s ease;
}

.action-card:hover .action-arrow {
    color: var(--primary-color);
    transform: translateX(-8px);
}

/* تأثيرات إضافية */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.content-card,
.action-card {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.2s; }
.stat-card:nth-child(4) { animation-delay: 0.3s; }

/* التجاوب المحسن */
@media (max-width: 768px) {
    .dashboard-header {
        padding: 2rem 1.5rem;
        text-align: center;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .header-subtitle {
        font-size: 1.1rem;
    }
    
    .stat-badge {
        padding: 1rem 1.2rem;
        margin-top: 1rem;
    }
    
    .stat-number {
        font-size: 2.2rem;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 1.8rem 1.2rem;
    }
    
    .card-icon {
        margin-left: 0;
        margin-bottom: 1.2rem;
        width: 70px;
        height: 70px;
    }
    
    .opportunity-item,
    .document-item {
        flex-direction: column;
        text-align: center;
        gap: 1.2rem;
        padding: 1.5rem;
    }
    
    .opportunity-icon,
    .document-icon {
        margin-left: 0;
    }
    
    .opportunity-meta,
    .document-meta {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .action-card {
        flex-direction: column;
        text-align: center;
        padding: 2rem 1.5rem;
    }
    
    .action-icon {
        margin-left: 0;
        margin-bottom: 1.5rem;
    }
    
    .action-content h3 {
        font-size: 1.2rem;
    }
}
</style>
@endsection