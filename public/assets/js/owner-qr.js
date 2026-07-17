(function () {
    var checkinUrl = window.QR_CHECKIN_URL || '';
    var qrWrap = document.getElementById('qrCodeWrap');

    // ---- Copy check-in URL ----
    var copyBtn = document.getElementById('qrCopyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            if (!navigator.clipboard) return;
            navigator.clipboard.writeText(checkinUrl).then(function () {
                copyBtn.classList.add('copied');
                copyBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
                setTimeout(function () {
                    copyBtn.classList.remove('copied');
                    copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 1500);
            });
        });
    }

    // ---- Download QR as a high-res PNG ----
    // The QR is rendered as inline SVG; rasterize it onto a canvas so the
    // downloaded file works everywhere (printers/photo apps handle PNG far
    // more reliably than SVG).
    var downloadBtn = document.getElementById('qrDownloadBtn');
    if (downloadBtn && qrWrap) {
        downloadBtn.addEventListener('click', function () {
            var svg = qrWrap.querySelector('svg');
            if (!svg) return;

            var originalHtml = downloadBtn.innerHTML;
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span><span>Preparing...</span>';

            function restore() {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = originalHtml;
            }

            var svgData = new XMLSerializer().serializeToString(svg);
            var svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            var blobUrl = URL.createObjectURL(svgBlob);
            var img = new Image();

            img.onload = function () {
                var scale = 4; // high-res export for crisp printing
                var size = Math.max(img.width, img.height) || 300;
                var canvas = document.createElement('canvas');
                canvas.width = size * scale;
                canvas.height = size * scale;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                URL.revokeObjectURL(blobUrl);

                canvas.toBlob(function (blob) {
                    if (!blob) { restore(); return; }
                    var a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'library-checkin-qr.png';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    restore();
                }, 'image/png');
            };
            img.onerror = function () {
                URL.revokeObjectURL(blobUrl);
                restore();
                alert('Could not prepare the QR image for download. Please try Print instead.');
            };
            img.src = blobUrl;
        });
    }

    // ---- Share (native share sheet — only where the browser supports it) ----
    var shareBtn = document.getElementById('qrShareBtn');
    if (shareBtn && navigator.share) {
        shareBtn.classList.remove('d-none');
        shareBtn.addEventListener('click', function () {
            navigator.share({
                title: 'Library Check-in',
                text: 'Scan this QR code to check in / check out.',
                url: checkinUrl,
            }).catch(function () {});
        });
    }
})();
