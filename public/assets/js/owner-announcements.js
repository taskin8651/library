const annTitle = document.getElementById('annTitle');
const annMessage = document.getElementById('annMessage');
const annTypePicker = document.getElementById('annTypePicker');
const preview = document.getElementById('annPreview');
const previewTitle = document.getElementById('previewTitle');
const previewMessage = document.getElementById('previewMessage');

const typeIcons = {
    info: 'bi-info-circle-fill',
    success: 'bi-check-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    danger: 'bi-megaphone-fill',
};

// Escape first (via a text node), THEN apply a fixed safe set of tags.
// This can never let raw user input introduce real HTML/script tags.
function mdLiteToSafeHtml(raw) {
    const div = document.createElement('div');
    div.textContent = raw;
    let safe = div.innerHTML;
    safe = safe.replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>');
    safe = safe.replace(/\*(.+?)\*/gs, '<em>$1</em>');
    safe = safe.replace(/(?:^|\n)- (.+)/g, '<br>&bull; $1');
    return safe;
}

function updatePreview() {
    previewTitle.textContent = annTitle.value.trim() || 'Your announcement title';
    const msg = annMessage.value.trim();
    previewMessage.innerHTML = msg ? mdLiteToSafeHtml(msg) : 'Your message will appear here as you type.';
    const checked = annTypePicker.querySelector('input:checked');
    const type = checked ? checked.value : 'info';
    preview.dataset.type = type;
    const icon = preview.querySelector('.preview-icon');
    icon.className = 'bi ' + (typeIcons[type] || 'bi-info-circle-fill') + ' preview-icon';
}

if (annTitle && annMessage && annTypePicker) {
    annTitle.addEventListener('input', updatePreview);
    annMessage.addEventListener('input', updatePreview);
    annTypePicker.addEventListener('change', updatePreview);
    updatePreview();
}

// Rich-text-lite toolbar: wraps selected text with markdown-style tokens.
document.querySelectorAll('.rte-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const start = annMessage.selectionStart;
        const end = annMessage.selectionEnd;
        const value = annMessage.value;
        const selected = value.slice(start, end) || (btn.dataset.list !== undefined ? 'List item' : 'text');

        let replacement;
        if (btn.dataset.list !== undefined) {
            replacement = selected.split('\n').map(line => '- ' + line).join('\n');
        } else {
            const token = btn.dataset.wrap;
            replacement = token + selected + token;
        }

        annMessage.value = value.slice(0, start) + replacement + value.slice(end);
        annMessage.focus();
        annMessage.selectionStart = start;
        annMessage.selectionEnd = start + replacement.length;
        updatePreview();
    });
});
