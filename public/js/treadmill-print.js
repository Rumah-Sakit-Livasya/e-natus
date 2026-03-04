(function () {
    async function renderPdfPages(url, container, fallback) {
        if (!url || typeof pdfjsLib === 'undefined') {
            return false;
        }

        try {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
            const pdf = await pdfjsLib.getDocument(url).promise;

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
                const page = await pdf.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 1.45 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.className = 'attachment-canvas';
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({
                    canvasContext: context,
                    viewport,
                }).promise;

                const wrapper = document.createElement('div');
                wrapper.className = 'attachment-page';
                wrapper.appendChild(canvas);
                container.appendChild(wrapper);
            }

            return pdf.numPages > 0;
        } catch (error) {
            if (fallback) {
                fallback.classList.remove('hidden');
            }
            return false;
        }
    }

    window.addEventListener('DOMContentLoaded', async function () {
        const payload = document.getElementById('treadmill-payload');
        const container = document.getElementById('treadmill-attachment-pages');
        const fallback = document.getElementById('treadmill-attachment-fallback');

        if (!payload) {
            return;
        }

        const attachmentUrl = payload.dataset.attachmentUrl || '';
        const isPdf = payload.dataset.isPdf === '1';

        if (isPdf && container) {
            await renderPdfPages(attachmentUrl, container, fallback);
        }

        setTimeout(function () {
            window.print();
        }, 700);
    });
})();
