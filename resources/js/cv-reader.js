/**
 * Read the text of a PDF (e.g. a CV) in the browser with pdf.js.
 * Loaded on demand by the AI assistant only when the user attaches a file, so normal pages stay light.
 * The file never leaves the device; only the extracted text is sent to the assistant.
 */
import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.js?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

const MAX_PAGES = 15;
const MAX_CHARS = 20000;

export async function extractPdfText(file) {
    const data = new Uint8Array(await file.arrayBuffer());
    const pdf = await pdfjsLib.getDocument({
        data,
        // Security: never evaluate code embedded in a PDF (mitigates CVE-2024-4367)
        isEvalSupported: false,
        disableFontFace: true,
    }).promise;

    let text = '';
    const pages = Math.min(pdf.numPages, MAX_PAGES);
    for (let i = 1; i <= pages && text.length < MAX_CHARS; i++) {
        const page = await pdf.getPage(i);
        const content = await page.getTextContent();
        let line = '';
        let lastY = null;
        for (const item of content.items) {
            const y = item.transform ? Math.round(item.transform[5]) : null;
            if (lastY !== null && y !== lastY) {
                text += line.trim() + '\n';
                line = '';
            }
            line += item.str + (item.hasEOL ? '\n' : ' ');
            lastY = y;
        }
        text += line.trim() + '\n\n';
    }

    await pdf.destroy();

    return {
        text: text.replace(/[ \t]+\n/g, '\n').replace(/\n{3,}/g, '\n\n').trim().slice(0, MAX_CHARS),
        pages: pdf.numPages,
    };
}
