<div class="tu-page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <div class="university-logo">
                    جامعة<br>طرابلس
                </div>
            </div>
            <div class="col-md-8 text-center">
                <h1 class="display-5 fw-bold mb-2">نظام تدريب وتوظيف الخريجين</h1>
                <p class="lead mb-0">Graduate Training & Employment System</p>
                <p class="mb-0">جامعة طرابلس - Tripoli University</p>
            </div>
            <div class="col-md-2 text-center">
                <div class="text-light">
                    <small>الوقت:</small>
                    <div id="current-time" class="fw-bold"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-EG');
        document.getElementById('current-time').textContent = timeString;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>