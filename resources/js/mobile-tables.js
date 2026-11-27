/**
 * Mobile card layout for data tables (styles in resources/css/mobile-tables.css).
 *
 * Labels every body cell with its column title (data-label) and marks the table with
 * "table-mobile-cards", so on phones each row is shown as a card instead of a wide,
 * horizontally scrolling table. Desktop rendering is unchanged.
 *
 * Opt out: add class "no-mobile-cards" to the table or its .table-responsive wrapper.
 */

const ACTION_HINT = /إجراء|اجراء|الإجراءات|العمليات|إدارة الحساب|التحكم|actions?/i;

function headerLabels(table) {
    const headRow = table.tHead && table.tHead.rows[table.tHead.rows.length - 1];
    if (!headRow) return null;

    const labels = [];
    for (const th of headRow.cells) {
        const span = th.colSpan || 1;
        const text = th.textContent.replace(/\s+/g, ' ').trim();
        for (let i = 0; i < span; i++) labels.push(text);
    }
    return labels;
}

function labelRows(table, labels) {
    const columns = labels.length;

    for (const body of table.tBodies) {
        for (const row of body.rows) {
            const cells = row.cells;

            // "No results" style row: one cell spanning the whole table
            if (cells.length === 1 && (cells[0].colSpan || 1) > 1) {
                row.classList.add('mobile-card-empty');
                continue;
            }

            let col = 0;
            for (let i = 0; i < cells.length; i++) {
                const cell = cells[i];
                const label = labels[col] || '';
                cell.setAttribute('data-label', label);

                if (i === 0) cell.classList.add('mobile-card-title');
                const isLast = i === cells.length - 1;
                const hasButtons = cell.querySelector('.btn, button, form') !== null;
                if ((ACTION_HINT.test(label) || (isLast && label === '')) && hasButtons) {
                    cell.classList.add('mobile-card-actions');
                }

                col += cell.colSpan || 1;
            }

            if (col > columns + 1) return false;
        }
    }
    return true;
}

function enhanceTable(table) {
    if (table.dataset.mobileCards === 'done') return;
    if (table.classList.contains('no-mobile-cards') || table.closest('.no-mobile-cards')) return;
    // Nested tables (tables inside a cell) are left alone
    if (table.parentElement && table.parentElement.closest('table')) return;

    const labels = headerLabels(table);
    if (!labels || labels.length < 3) return;

    if (labelRows(table, labels)) {
        table.classList.add('table-mobile-cards');
    }
    table.dataset.mobileCards = 'done';
}

function enhanceAll(root = document) {
    root.querySelectorAll('.table-responsive > table, table.table').forEach(enhanceTable);
}

function init() {
    enhanceAll();

    // Rows added later (AJAX search, pagination, live updates) get labels too
    const observer = new MutationObserver((mutations) => {
        for (const m of mutations) {
            const table = m.target.closest && m.target.closest('table[data-mobile-cards="done"]');
            if (table) {
                const labels = headerLabels(table);
                if (labels) labelRows(table, labels);
            } else if (m.addedNodes.length) {
                m.addedNodes.forEach((n) => n.nodeType === 1 && enhanceAll(n.parentElement || document));
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
