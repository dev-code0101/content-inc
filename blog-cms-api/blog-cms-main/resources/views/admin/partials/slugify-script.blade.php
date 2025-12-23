<script>
    $(document).on('change', '[data-slug-field]', function() {
        const $trigger = $(this); // Field that triggers the slug
        const route = $trigger.data('slug-route'); // route to get the slug
        const field = $trigger.data('slug-field'); // slug field
        const value = $trigger.val(); // field value
        const $slug = $('#slug'); // the slug field itself

        if (!route || !field || !value) return;

        $.get(route, {
                [field]: value
            })
            .done(function(data) {
                if (data && data.slug) $slug.val(data.slug);
            })
            .fail(function(xhr) {
                console.error('Slugify request failed:', xhr.status, xhr.statusText);
            });
    });
</script>
