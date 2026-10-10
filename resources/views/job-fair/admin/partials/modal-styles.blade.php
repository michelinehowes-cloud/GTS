<style>
/* ═══════════════════════════════════════════════════════════════
   🏛️ أنماط النوافذ المنبثقة المعتمدة — هوية جامعة طرابلس الفاخرة
   ═══════════════════════════════════════════════════════════════ */
.uot-modal-content {
    border: none !important;
    border-radius: 20px !important;
    box-shadow: 0 25px 65px rgba(10, 43, 102, 0.3) !important;
    overflow: hidden !important;
    background: #ffffff !important;
}

.uot-modal-header {
    background: linear-gradient(135deg, #0a2b66 0%, #0d3882 60%, #1565c0 100%) !important;
    border-bottom: 3.5px solid #eeca3e !important;
    padding: 1.25rem 1.75rem !important;
    color: #ffffff !important;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.uot-modal-header-edit {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 65%, #334155 100%) !important;
    border-bottom: 3.5px solid #eeca3e !important;
}

.uot-modal-header-review {
    background: linear-gradient(135deg, #064e3b 0%, #047857 65%, #059669 100%) !important;
    border-bottom: 3.5px solid #eeca3e !important;
}

.uot-modal-icon-badge {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: rgba(238, 202, 62, 0.16);
    border: 1.5px solid #eeca3e;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #eeca3e;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.uot-modal-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: #ffffff !important;
    margin: 0;
    letter-spacing: -0.2px;
}

.uot-modal-subtitle {
    font-size: 0.8rem;
    color: #eeca3e !important;
    font-weight: 600;
    margin: 0;
    margin-top: 3px;
    opacity: 0.95;
}

.uot-btn-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
    font-size: 1rem;
    line-height: 1;
    text-decoration: none;
}
.uot-btn-close:hover {
    background: #eeca3e;
    color: #0a2b66 !important;
    border-color: #eeca3e;
    transform: rotate(90deg) scale(1.08);
}

.uot-section-divider {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    margin: 1.6rem 0 1.1rem;
    padding-bottom: 0.55rem;
    border-bottom: 2px solid #e2e8f0;
}
.uot-section-divider:first-of-type {
    margin-top: 0.2rem;
}
.uot-section-divider .divider-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: rgba(13, 56, 130, 0.08);
    color: #0d3882;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: bold;
    border: 1px solid rgba(13, 56, 130, 0.15);
}
.uot-section-divider .divider-title {
    font-size: 0.96rem;
    font-weight: 800;
    color: #0d3882;
    margin: 0;
}
.uot-section-divider .divider-badge {
    font-size: 0.72rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #64748b;
    padding: 2px 9px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
}

.uot-form-label {
    font-size: 0.86rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 0.35rem;
    display: block;
}
.uot-form-label .req {
    color: #ef4444;
    font-weight: bold;
    margin-right: 2px;
}

.uot-input, .uot-select, .uot-textarea {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 10px !important;
    padding: 0.65rem 0.95rem !important;
    font-size: 0.9rem !important;
    transition: all 0.2s ease !important;
    background-color: #ffffff !important;
    color: #1e293b !important;
}
.uot-input:focus, .uot-select:focus, .uot-textarea:focus {
    border-color: #0d3882 !important;
    box-shadow: 0 0 0 3px rgba(13, 56, 130, 0.14) !important;
    background-color: #ffffff !important;
}

.uot-admin-box {
    background: linear-gradient(135deg, #fffdf5 0%, #fefce8 100%);
    border: 1.5px solid #fde047;
    border-radius: 16px;
    padding: 1.35rem;
    margin-top: 1.35rem;
    position: relative;
    box-shadow: 0 4px 15px rgba(234, 179, 8, 0.08);
}
.uot-admin-box-header {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    color: #854d0e;
    font-weight: 800;
    font-size: 0.92rem;
    margin-bottom: 1.1rem;
    padding-bottom: 0.55rem;
    border-bottom: 1.5px dashed #fde047;
}

.uot-modal-footer {
    background: #f8fafc !important;
    border-top: 1.5px solid #e2e8f0 !important;
    padding: 1.1rem 1.75rem !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.uot-btn-cancel {
    border-radius: 50rem !important;
    padding: 0.55rem 1.5rem !important;
    font-weight: 700 !important;
    font-size: 0.88rem !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #64748b !important;
    background: #ffffff !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
}
.uot-btn-cancel:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
}
.uot-btn-submit {
    border-radius: 50rem !important;
    padding: 0.55rem 1.75rem !important;
    font-weight: 800 !important;
    font-size: 0.88rem !important;
    background: linear-gradient(135deg, #0a2b66 0%, #0d3882 100%) !important;
    color: #ffffff !important;
    border: 1.5px solid #eeca3e !important;
    box-shadow: 0 4px 14px rgba(10, 43, 102, 0.22) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.45rem !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}
.uot-btn-submit:hover {
    background: linear-gradient(135deg, #09275e 0%, #0a2b66 100%) !important;
    color: #eeca3e !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(10, 43, 102, 0.32) !important;
}
</style>
