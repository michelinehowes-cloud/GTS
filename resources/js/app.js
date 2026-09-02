import './bootstrap';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';
// import './dark-mode'; // Disabled - using inline script instead
import './notification-handler'; // Import the new notification handler
import './ui-enhancements'; // Import UI enhancements
import './bottom-nav'; // Import bottom navigation
import './advanced-features'; // Import advanced features
import './advanced-features-part2'; // Import advanced features part 2
import './enhanced-ui'; // Import enhanced UI features

// Make Sortable globally available for inline scripts
window.Sortable = Sortable;


// Advanced Reports Specific Scripts (from admin/career-guidance/advanced-reports.blade.php)

// This 'routes' object needs to be defined in the Blade file and passed to JavaScript.
// For example, in your Blade file:
// <script>
//     window.routes = {
//         pdf: "{{ route('admin.career-guidance.export-reports.pdf') }}",
//         excel: "{{ route('admin.career-guidance.export-reports.excel') }}"
//     };
// </script>
// Then in app.js, you can access it as `window.routes`.
// For now, I'll define a placeholder.
const routes = window.routes || {
    pdf: '',
    excel: ''
};

let charts = {};
let chartsLoaded = 0;
const totalCharts = 6;



// تهيئة جميع المخططات
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded, initializing charts...');
    setTimeout(() => {
        try {
            // Conditionally initialize charts
            if (document.getElementById('majorsChart')) {
                initMajorsChart();
            } else {
                checkAllChartsLoaded(); // Increment count even if chart element not found
            }

            if (document.getElementById('employmentChart')) {
                initEmploymentChart();
            } else {
                checkAllChartsLoaded();
            }
            if (document.getElementById('nominationsChart')) {
                initNominationsChart();
            } else {
                checkAllChartsLoaded();
            }
            if (document.getElementById('monthlyPerformanceChart')) {
                initMonthlyPerformanceChart();
            } else {
                checkAllChartsLoaded();
            }
            if (document.getElementById('successByMajorChart')) {
                initSuccessByMajorChart();
            } else {
                checkAllChartsLoaded();
            }
            if (document.getElementById('opportunitiesChart')) {
                initOpportunitiesChart();
            } else {
                checkAllChartsLoaded();
            }
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    }, 500);
});

// تحقق من صحة بيانات المخطط
function validateChartData(chartData, chartName) {
    if (!chartData || !chartData.labels || !chartData.datasets) {
        console.warn(`Invalid data for ${chartName}:`, chartData);
        return false;
    }

    if (chartData.labels.length === 0 || chartData.datasets.length === 0) {
        console.warn(`Empty data for ${chartName}`);
        return false;
    }

    const hasData = chartData.datasets.some(dataset => dataset.data && dataset.data.length > 0);
    if (!hasData) {
        console.warn(`No data values for ${chartName}`);
        return false;
    }

    return true;
}

// إظهار رسالة بديلة
function showFallbackMessage(chartId, chartName) {
    const fallbackElement = document.getElementById(chartId + 'Fallback');
    if (fallbackElement) {
        fallbackElement.style.display = 'flex';
    }
    console.log(`Showing fallback for ${chartName}`);
}

// تحديث حالة التحميل
function checkAllChartsLoaded() {
    chartsLoaded++;
    console.log(`Chart loaded: ${chartsLoaded}/${totalCharts}`);

    if (chartsLoaded === totalCharts) {
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
        console.log('All charts loaded successfully');
    }
}

// مخطط توزيع التخصصات
function initMajorsChart() {
    const ctx = document.getElementById('majorsChart');
    if (!ctx) {
        console.error('majorsChart element not found');
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.majorsDistribution, 'majorsChart')) {
        showFallbackMessage('majors', 'توزيع التخصصات');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.majorsChart = new Chart(ctx, {
            type: 'bar',
            data: window.chartData.majorsDistribution,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'عدد الخريجين'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'التخصصات'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
        console.log('Majors Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Majors Chart:', error);
        showFallbackMessage('majors', 'توزيع التخصصات');
    } finally {
        checkAllChartsLoaded();
    }
}

// مخطط حالة التوظيف
function initEmploymentChart() {
    const ctx = document.getElementById('employmentChart');
    if (!ctx) {
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.employmentStatus, 'employmentChart')) {
        showFallbackMessage('employment', 'حالة التوظيف');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.employmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: window.chartData.employmentStatus,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '60%'
            }
        });
        console.log('Employment Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Employment Chart:', error);
        showFallbackMessage('employment', 'حالة التوظيف');
    } finally {
        checkAllChartsLoaded();
    }
}

// مخطط الترشيحات
function initNominationsChart() {
    const ctx = document.getElementById('nominationsChart');
    if (!ctx) {
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.nominationsStatus, 'nominationsChart')) {
        showFallbackMessage('nominations', 'حالة الترشيحات');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.nominationsChart = new Chart(ctx, {
            type: 'pie',
            data: window.chartData.nominationsStatus,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        console.log('Nominations Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Nominations Chart:', error);
        showFallbackMessage('nominations', 'حالة الترشيحات');
    } finally {
        checkAllChartsLoaded();
    }
}

// مخطط الأداء الشهري
function initMonthlyPerformanceChart() {
    const ctx = document.getElementById('monthlyPerformanceChart');
    if (!ctx) {
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.monthlyPerformance, 'monthlyPerformanceChart')) {
        showFallbackMessage('monthlyPerformance', 'الأداء الشهري');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.monthlyPerformanceChart = new Chart(ctx, {
            type: 'line',
            data: window.chartData.monthlyPerformance,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'عدد الترشيحات'
                        }
                    }
                }
            }
        });
        console.log('Monthly Performance Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Monthly Performance Chart:', error);
        showFallbackMessage('monthlyPerformance', 'الأداء الشهري');
    } finally {
        checkAllChartsLoaded();
    }
}

// مخطط النجاح حسب التخصص
function initSuccessByMajorChart() {
    const ctx = document.getElementById('successByMajorChart');
    if (!ctx) {
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.successByMajor, 'successByMajorChart')) {
        showFallbackMessage('successByMajor', 'النجاح حسب التخصص');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.successByMajorChart = new Chart(ctx, {
            type: 'radar',
            data: window.chartData.successByMajor,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        console.log('Success By Major Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Success By Major Chart:', error);
        showFallbackMessage('successByMajor', 'النجاح حسب التخصص');
    } finally {
        checkAllChartsLoaded();
    }
}

// مخطط فرص العمل
function initOpportunitiesChart() {
    const ctx = document.getElementById('opportunitiesChart');
    if (!ctx) {
        checkAllChartsLoaded();
        return;
    }

    if (!validateChartData(window.chartData.opportunitiesDistribution, 'opportunitiesChart')) {
        showFallbackMessage('opportunities', 'توزيع فرص العمل');
        checkAllChartsLoaded();
        return;
    }

    try {
        charts.opportunitiesChart = new Chart(ctx, {
            type: 'polarArea',
            data: window.chartData.opportunitiesDistribution,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        console.log('Opportunities Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing Opportunities Chart:', error);
        showFallbackMessage('opportunities', 'توزيع فرص العمل');
    } finally {
        checkAllChartsLoaded();
    }
}

// تحميل المخططات كصور
function downloadChart(chartId) {
    const chart = charts[chartId];
    if (!chart) {
        alert('المخطط غير متاح للتحميل');
        return;
    }

    const link = document.createElement('a');
    link.download = `${chartId}_${new Date().toISOString().split('T')[0]}.png`;
    link.href = chart.toBase64Image();
    link.click();
}

// تحديث البيانات
document.getElementById('refreshBtn')?.addEventListener('click', function () {
    const btn = this;
    const originalText = btn.innerHTML;

    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2 spin"></i>جاري التحديث...';
    btn.disabled = true;

    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
    }
    chartsLoaded = 0;

    setTimeout(() => {
        location.reload();
    }, 1500);
});

// تصدير التقرير - نسخة آمنة
function exportReport() {
    try {
        const exportType = confirm('هل تريد تصدير التقرير كـ PDF؟\n\nموافق: تصدير PDF\nإلغاء: تصدير Excel');

        if (exportType) {
            if (routes.pdf && routes.pdf !== '') {
                window.open(routes.pdf, '_blank');
            } else {
                showExportMessage('ميزة تصدير PDF قيد التطوير');
            }
        } else {
            if (routes.excel && routes.excel !== '') {
                window.open(routes.excel, '_blank');
            } else {
                showExportMessage('ميزة تصدير Excel قيد التطوير');
            }
        }
    } catch (error) {
        console.error('Export error:', error);
        showExportMessage('حدث خطأ أثناء التصدير. يرجى المحاولة مرة أخرى.');
    }
}

// عرض رسالة تصدير
function showExportMessage(message) {
    alert(message);
}

// تحديث المخططات حسب الفلتر
function updateChart(chartType, filter) {
    console.log(`Updating ${chartType} chart with filter: ${filter}`);
}

// حل بديل للتصدير بدون روات
function safeExport() {
    const modalHtml = `
        <div class="modal fade" id="exportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تصدير التقرير</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            ميزة التصدير قيد التطوير حالياً.
                        </div>
                        <p>سيتم تفعيل التصدير إلى PDF و Excel في الإصدار القادم.</p>
                        <p>يمكنك حالياً:</p>
                        <ul>
                            <li>تحميل كل مخطط على حدة باستخدام زر التحميل</li>
                            <li>أخذ لقطة شاشة للتقرير</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (!document.getElementById('exportModal')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

// Commenting out this line to prevent conflict with "Add Question" button
// document.querySelector('.btn-outline-primary').addEventListener('click', safeExport);
