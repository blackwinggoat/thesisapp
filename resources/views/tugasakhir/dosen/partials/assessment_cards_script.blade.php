<script>
    $(function () {
        $('.assessment-card-search-input').on('input', function () {
            var keyword = $(this).val().toLowerCase().trim();
            var visible = 0;
            $('.assessment-card-column').each(function () {
                var matches = $(this).data('search').toLowerCase().indexOf(keyword) !== -1;
                $(this).toggle(matches);
                if (matches) {
                    visible++;
                }
            });
            $('.assessment-card-no-result').toggle(visible === 0);
        });
    });
</script>
