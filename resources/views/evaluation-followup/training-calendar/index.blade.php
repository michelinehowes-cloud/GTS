@extends('layouts.app')

@section('title', 'تقويم التدريبات - التقييم والمتابعة')
@section('page-title', 'تقويم التدريبات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h3 class="mb-0 text-dark">
                        <i class="fas fa-calendar-alt me-3 text-primary"></i>
                        تقويم التدريبات
                    </h3>
                    <div class="d-flex gap-2">
                        <!-- تنقل الشهور -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('evaluation-followup.training-calendar', ['month' => $month - 1, 'year' => $year]) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-chevron-right me-2"></i>الشهر السابق
                            </a>
                            <button class="btn btn-dark btn-sm fw-bold" disabled>
                                <i class="fas fa-calendar me-2"></i>
                                {{ $calendar['month_name'] }} {{ $year }}
                            </button>
                            <a href="{{ route('evaluation-followup.training-calendar', ['month' => $month + 1, 'year' => $year]) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                الشهر التالي <i class="fas fa-chevron-left ms-2"></i>
                            </a>
                        </div>
                        
                        <!-- Removed "عرض القائمة" button as it's specific to training coordinator -->
                    </div>
                </div>
                <div class="card-body bg-light">
                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-0 bg-white shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="fas fa-play-circle text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-dark">{{ $trainings->where('status', 'active')->count() }}</h4>
                                            <small class="text-muted">التدريبات النشطة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-0 bg-white shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="fas fa-calendar-check text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-dark">{{ $trainings->count() }}</h4>
                                            <small class="text-muted">إجمالي التدريبات</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-0 bg-white shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="fas fa-users text-warning fs-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-dark">{{ $trainings->sum('seats') }}</h4>
                                            <small class="text-muted">إجمالي المقاعد</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-0 bg-white shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-container bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="fas fa-map-marker-alt text-info fs-5"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-dark">{{ $trainings->unique('location')->count() }}</h4>
                                            <small class="text-muted">المواقع المختلفة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التقويم -->
                    <div class="calendar-clean">
                        <div class="calendar-header-clean">
                            @foreach($calendar['days'] as $day)
                                <div class="calendar-day-header-clean">
                                    <span class="day-name">{{ $day }}</span>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="calendar-grid-clean">
                            @foreach($calendar['weeks'] as $week)
                                @foreach($week as $dayData)
                                    <div class="calendar-day-clean {{ !$dayData['day'] ? 'empty' : '' }} {{ $dayData['day'] && $dayData['day']->isToday() ? 'today' : '' }} {{ $dayData['day'] && $dayData['day']->isWeekend() ? 'weekend' : '' }}">
                                        @if($dayData['day'])
                                            <div class="day-header-clean">
                                                <span class="day-number-clean">{{ $dayData['day']->day }}</span>
                                                @if($dayData['day']->isToday())
                                                    <span class="today-indicator"></span>
                                                @endif
                                            </div>
                                            <div class="day-content-clean">
                                                @foreach($dayData['trainings'] as $training)
                                                    <div class="training-item-clean training-{{ $training->status }}"
                                                         data-bs-toggle="tooltip"
                                                         data-bs-placement="top"
                                                         title="{{ $training->title }} - {{ $training->location }}">
                                                        <div class="training-icon">
                                                            @if($training->type == 'workshop')
                                                                <i class="fas fa-tools"></i>
                                                            @elseif($training->type == 'course')
                                                                <i class="fas fa-book-open"></i>
                                                            @elseif($training->type == 'seminar')
                                                                <i class="fas fa-chalkboard-teacher"></i>
                                                            @else
                                                                <i class="fas fa-briefcase"></i>
                                                            @endif
                                                        </div>
                                                        <div class="training-details-clean">
                                                            <div class="training-title-clean">{{ Str::limit($training->title, 15) }}</div>
                                                            <div class="training-meta-clean">
                                                                <span class="training-time">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ $training->start_date->format('H:i') }}
                                                                </span>
                                                                <span class="training-location">
                                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                                    {{ Str::limit($training->location, 10) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- مفتاح الألوان -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-white shadow-sm">
                                <div class="card-body py-3">
                                    <h6 class="card-title mb-3 fw-bold text-dark">
                                        <i class="fas fa-tags me-2"></i>مفتاح الألوان
                                    </h6>
                                    <div class="d-flex flex-wrap gap-4">
                                        <div class="d-flex align-items-center">
                                            <span class="color-indicator-clean bg-success me-2"></span>
                                            <small class="fw-medium">تدريب نشط</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="color-indicator-clean bg-warning me-2"></span>
                                            <small class="fw-medium">تدريب متوقف</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="color-indicator-clean bg-secondary me-2"></span>
                                            <small class="fw-medium">تدريب مكتمل</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="color-indicator-clean bg-primary me-2"></span>
                                            <small class="fw-medium">اليوم الحالي</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* تصميم نظيف وأنيق */
.calendar-clean {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.calendar-header-clean {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    margin-bottom: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-day-header-clean {
    background: #f8f9fa;
    padding: 12px 8px;
    text-align: center;
    font-weight: 700;
    font-size: 0.85rem;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.calendar-grid-clean {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-day-clean {
    background: white;
    min-height: 130px;
    padding: 12px;
    border: 1px solid #f8f9fa;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
}

.calendar-day-clean:hover {
    background: #f8f9fa;
    transform: none;
}

.calendar-day-clean.empty {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
}

.calendar-day-clean.today {
    background: #e7f1ff;
    border: 1px solid #b3d4ff;
}

.calendar-day-clean.weekend {
    background: #fff3e0;
}

.day-header-clean {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f3f4;
}

.day-number-clean {
    font-weight: 700;
    font-size: 1rem;
    color: #212529;
}

.today-indicator {
    width: 6px;
    height: 6px;
    background: #007bff;
    border-radius: 50%;
}

.day-content-clean {
    flex: 1;
    overflow-y: auto;
    max-height: 90px;
}

/* بطاقات التدريب المحسنة */
.training-item-clean {
    background: white;
    border-radius: 6px;
    padding: 6px;
    margin-bottom: 5px;
    border-left: 3px solid;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid #f1f3f4;
}

.training-item-clean:hover {
    background: #f8f9fa;
    border-left-width: 4px;
}

.training-active {
    border-left-color: #28a745;
}

.training-inactive {
    border-left-color: #ffc107;
}

.training-completed {
    border-left-color: #6c757d;
}

.training-icon {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
}

.training-icon i {
    font-size: 0.7rem;
    color: #6c757d;
}

.training-details-clean {
    flex: 1;
    min-width: 0;
}

.training-title-clean {
    font-weight: 600;
    color: #212529;
    font-size: 0.75rem;
    margin-bottom: 2px;
    line-height: 1.2;
}

.training-meta-clean {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.training-time, .training-location {
    font-size: 0.65rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 3px;
}

.training-time i, .training-location i {
    font-size: 0.6rem;
    width: 10px;
}

/* أيقونات الإحصائيات */
.icon-container {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* مفتاح الألوان */
.color-indicator-clean {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    display: inline-block;
}

/* تخصيص شريط التمرير */
.day-content-clean::-webkit-scrollbar {
    width: 3px;
}

.day-content-clean::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.day-content-clean::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.day-content-clean::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* تحسين المحاذاة والمسافات */
.card-header h3 {
    display: flex;
    align-items: center;
}

.btn-group .btn {
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn i {
    line-height: 1;
}

/* تصميم متجاوب */
@media (max-width: 1200px) {
    .calendar-day-clean {
        min-height: 120px;
        padding: 10px;
    }
}

@media (max-width: 768px) {
    .calendar-clean {
        padding: 15px;
    }
    
    .calendar-day-clean {
        min-height: 100px;
        padding: 8px;
    }
    
    .calendar-day-header-clean {
        padding: 10px 5px;
        font-size: 0.75rem;
    }
    
    .day-number-clean {
        font-size: 0.9rem;
    }
    
    .training-item-clean {
        padding: 5px;
        gap: 6px;
    }
    
    .training-title-clean {
        font-size: 0.7rem;
    }
    
    .training-time, .training-location {
        font-size: 0.6rem;
    }
}

@media (max-width: 576px) {
    .calendar-grid-clean {
        gap: 0;
    }
    
    .calendar-day-clean {
        min-height: 85px;
        padding: 6px;
    }
    
    .day-content-clean {
        max-height: 65px;
    }
    
    .training-item-clean {
        padding: 4px;
        margin-bottom: 3px;
    }
    
    .training-icon {
        width: 16px;
        height: 16px;
    }
    
    .training-icon i {
        font-size: 0.6rem;
    }
}
</style>

<script>
// تفعيل الـ tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endsection
