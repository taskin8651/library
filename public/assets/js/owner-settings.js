function previewImage(input, previewId) {
    if (!input.files || !input.files[0]) return;

    const preview = document.getElementById(previewId);
    const reader = new FileReader();
    reader.onload = function (e) {
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            // Replace the empty-state placeholder span with a real <img>
            const img = document.createElement('img');
            img.id = previewId;
            img.className = preview.className.replace('upload-preview-empty', '').trim();
            img.src = e.target.result;
            preview.replaceWith(img);
        }
    };
    reader.readAsDataURL(input.files[0]);
}
