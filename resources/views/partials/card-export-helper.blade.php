<script>
window.CardExportHelper = (function () {
    function isUnsafeForAnchorDownload() {
        const ua = navigator.userAgent || '';
        const isInAppWebView = /FBAN|FBAV|FBIOS|FB_IAB|FBBV|Instagram|Messenger|Line\/|TikTok|BytedanceWebview|Twitter|Snapchat|Telegram|WhatsApp|; wv\)|\bWebView\b/i.test(ua);
        const isIOS = /iPad|iPhone|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        return isInAppWebView || isIOS || !('download' in HTMLAnchorElement.prototype);
    }

    function showInAppPreview(options, dataUrl) {
        const modalId = options.modalId || 'cardExportPreviewModal';
        const imgId = options.imgId || 'cardExportPreviewImg';
        const imgAlt = options.imgAlt || 'معاينة البطاقة';

        let modal = document.getElementById(modalId);
        if (!modal) {
            modal = document.createElement('div');
            modal.id = modalId;
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.92);z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:18px;overflow-y:auto;backdrop-filter:blur(6px);';
            modal.innerHTML =
                '<div style="max-width:440px;width:100%;text-align:center;color:#fff;">' +
                    '<div style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.5);color:#a7f3d0;padding:10px 14px;border-radius:14px;font-weight:700;font-size:0.88rem;margin-bottom:12px;line-height:1.6;">' +
                        'اضغط مطولاً على صورة البطاقة أدناه ثم اختر <strong>«حفظ الصورة»</strong> أو <strong>«تنزيل الصورة»</strong>' +
                    '</div>' +
                    '<img id="' + imgId + '" src="" alt="' + imgAlt + '" style="width:100%;max-height:72vh;object-fit:contain;border-radius:18px;box-shadow:0 20px 50px rgba(0,0,0,0.5);background:#fff;padding:4px;" />' +
                    '<button type="button" data-close-card-modal style="margin-top:14px;width:100%;background:#ffffff;color:#0f172a;border:none;padding:11px 18px;border-radius:12px;font-weight:800;font-size:0.95rem;cursor:pointer;">إغلاق</button>' +
                '</div>';
            document.body.appendChild(modal);
            modal.addEventListener('click', function (ev) {
                if (ev.target === modal || ev.target.hasAttribute('data-close-card-modal')) {
                    modal.style.display = 'none';
                }
            });
        }

        const previewImg = modal.querySelector('#' + imgId);
        if (previewImg) {
            previewImg.src = dataUrl;
        }
        modal.style.display = 'flex';
    }

    function syncCanvasesToImages(sourceRoot, clonedRoot) {
        const sourceCanvases = sourceRoot.querySelectorAll('canvas');
        const clonedCanvases = clonedRoot.querySelectorAll('canvas');

        sourceCanvases.forEach(function (srcCanvas, index) {
            const targetCanvas = clonedCanvases[index];
            if (!targetCanvas || !targetCanvas.parentNode) return;

            try {
                const dataUrl = srcCanvas.toDataURL('image/png');
                if (!dataUrl || dataUrl === 'data:,') return;
                const parent = targetCanvas.parentNode;
                const existingSiblingImgs = Array.from(parent.querySelectorAll('img'));
                let preferredSize = 0;
                existingSiblingImgs.forEach(function (img) {
                    if (!preferredSize && img.offsetWidth > 0) {
                        preferredSize = img.offsetWidth;
                    }
                    img.remove();
                });

                const qrImg = document.createElement('img');
                qrImg.src = dataUrl;
                qrImg.alt = 'QR Code';
                qrImg.style.display = 'block';
                const displaySize = preferredSize || srcCanvas.offsetWidth || parseInt(srcCanvas.style.width, 10) || 180;
                qrImg.style.width = displaySize + 'px';
                qrImg.style.height = displaySize + 'px';
                qrImg.style.maxWidth = '100%';
                qrImg.style.margin = '0 auto';
                qrImg.style.imageRendering = 'crisp-edges';
                parent.replaceChild(qrImg, targetCanvas);
            } catch (e) {
                // Keep original cloned canvas if toDataURL is blocked.
            }
        });
    }

    function waitForImages(root) {
        const imgs = Array.from(root.querySelectorAll('img'));
        return Promise.all(imgs.map(function (img) {
            if (img.complete && img.naturalWidth > 0) {
                return Promise.resolve();
            }
            return new Promise(function (resolve) {
                const done = function () { resolve(); };
                img.addEventListener('load', done, { once: true });
                img.addEventListener('error', done, { once: true });
                setTimeout(done, 2500);
            });
        }));
    }

    function saveCanvas(canvas, options) {
        const fileName = options.fileName || 'card.png';
        const shareTitle = options.shareTitle || 'بطاقة الخريج';
        const unsafe = isUnsafeForAnchorDownload();

        return new Promise(function (resolve) {
            canvas.toBlob(async function (blob) {
                try {
                    if (unsafe) {
                        if (blob && typeof File !== 'undefined' && navigator.canShare) {
                            const file = new File([blob], fileName, { type: 'image/png' });
                            if (navigator.canShare({ files: [file] })) {
                                try {
                                    await navigator.share({ files: [file], title: shareTitle });
                                    resolve();
                                    return;
                                } catch (shareErr) {
                                    if (shareErr && shareErr.name === 'AbortError') {
                                        resolve();
                                        return;
                                    }
                                }
                            }
                        }
                        showInAppPreview(options, canvas.toDataURL('image/png'));
                        resolve();
                        return;
                    }

                    if (blob && window.URL && URL.createObjectURL) {
                        const blobUrl = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.download = fileName;
                        link.href = blobUrl;
                        link.rel = 'noopener';
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 15000);

                        if (typeof options.onSuccess === 'function') {
                            options.onSuccess();
                        }
                    } else {
                        showInAppPreview(options, canvas.toDataURL('image/png'));
                    }
                } catch (err) {
                    showInAppPreview(options, canvas.toDataURL('image/png'));
                }
                resolve();
            }, 'image/png');
        });
    }

    async function exportCardToPng(options) {
        const sourceElement = options.element;
        if (!sourceElement) return;

        const exportWidth = options.width || 412;
        const exportMinHeight = options.minHeight || 760;
        const exportScale = options.scale || 3;

        if (document.fonts && document.fonts.ready) {
            try { await document.fonts.ready; } catch (e) {}
        }

        const stage = document.createElement('div');
        stage.style.cssText = 'position:fixed;left:-99999px;top:0;width:' + exportWidth + 'px;margin:0;padding:0;background:#ffffff;z-index:-1;direction:rtl;box-sizing:border-box;';

        const cloned = sourceElement.cloneNode(true);
        cloned.style.width = exportWidth + 'px';
        cloned.style.maxWidth = exportWidth + 'px';
        cloned.style.minWidth = exportWidth + 'px';
        cloned.style.minHeight = exportMinHeight + 'px';
        cloned.style.margin = '0';
        cloned.style.borderRadius = '0';
        cloned.style.boxShadow = 'none';
        cloned.style.border = 'none';
        cloned.style.transform = 'none';
        cloned.style.backgroundColor = '#ffffff';
        cloned.style.direction = 'rtl';
        cloned.style.letterSpacing = 'normal';
        cloned.style.display = 'flex';
        cloned.style.flexDirection = 'column';
        cloned.style.justifyContent = 'space-between';

        cloned.querySelectorAll('*').forEach(function (el) {
            el.style.letterSpacing = 'normal';
        });

        syncCanvasesToImages(sourceElement, cloned);

        if (typeof options.customizeClone === 'function') {
            options.customizeClone(cloned, stage);
        }

        stage.appendChild(cloned);
        document.body.appendChild(stage);

        try {
            await waitForImages(cloned);
            await new Promise(function (resolve) {
                requestAnimationFrame(function () {
                    requestAnimationFrame(resolve);
                });
            });

            const canvas = await html2canvas(cloned, {
                scale: exportScale,
                useCORS: true,
                backgroundColor: '#ffffff',
                width: exportWidth,
                windowWidth: exportWidth,
                scrollX: 0,
                scrollY: 0,
                logging: false
            });

            await saveCanvas(canvas, options);
        } finally {
            if (stage.parentNode) {
                stage.parentNode.removeChild(stage);
            }
        }
    }

    return {
        exportCardToPng: exportCardToPng
    };
})();
</script>
