{{-- Tom Select (tags) --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new TomSelect('#tags', {
            plugins: ['remove_button'],
            create: false,
            persist: false,
            maxItems: null,
            closeAfterSelect: false,
            allowEmptyOption: false,
        });
    });
</script>
