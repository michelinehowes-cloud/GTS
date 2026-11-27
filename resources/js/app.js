import './bootstrap';
import Sortable from 'sortablejs';
// import './dark-mode'; // Disabled - using inline script instead
import './notification-handler'; // Import the new notification handler
import './ui-enhancements'; // Import UI enhancements
import './bottom-nav'; // Import bottom navigation
import './advanced-features'; // Import advanced features
import './advanced-features-part2'; // Import advanced features part 2
import './enhanced-ui'; // Import enhanced UI features
import './mobile-tables'; // Tables become stacked cards on phones

// Make Sortable globally available for inline scripts
window.Sortable = Sortable;

// PDF reader for the AI assistant's CV analysis, loaded only when a file is attached
window.loadCvReader = () => import('./cv-reader');

// Load the advanced-reports charts (and Chart.js) only on pages that need them
const ADVANCED_REPORT_CHART_IDS = ['majorsChart', 'employmentChart', 'nominationsChart', 'monthlyPerformanceChart', 'successByMajorChart', 'opportunitiesChart'];
function loadAdvancedReportsIfNeeded() {
    if (ADVANCED_REPORT_CHART_IDS.some((id) => document.getElementById(id)) || document.getElementById('refreshBtn')) {
        import('./advanced-reports');
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAdvancedReportsIfNeeded);
} else {
    loadAdvancedReportsIfNeeded();
}
