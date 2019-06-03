<script type="text/javascript">
    $(document).ready(function() {
        $(function () {
            @foreach($script as $s)
            {!! $s !!}
            @endforeach
        });
    });
</script>