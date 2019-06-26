@foreach($css as $c)
    <link rel="stylesheet" href="{{ tw_asset("$c") }}?version=1.02">
@endforeach