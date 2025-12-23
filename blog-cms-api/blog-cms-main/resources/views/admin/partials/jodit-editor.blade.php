<script src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Jodit.make('#editor', {
            height: 350,
            toolbarSticky: false,
            uploader: { insertImageAsBase64URI: false },
            buttons: [
                'bold', 'italic', 'underline', '|',
                'ul', 'ol', '|',
                'image', 'link', 'align', '|',
                'undo', 'redo', 'source'
            ],
            placeholder: 'Start typing your post content here...'
        });
    });
</script>
