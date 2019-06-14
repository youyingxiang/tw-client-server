@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'活动续费',"pageBtnName"=>'扫码支付'])
@section('content')
    <section class="content">
        <div class="row">

            <div class="col-md-3">
            </div>
            <div class="col-md-6">
             {!! $qrcode !!}
            </div>
            <div class="col-md-3">
            </div>
            <div class="col-md-3"><a id="pay_res" class="btn btn-default"> 查看支付结果</a></div>
        </div>
    </section>
    <script>
        
    </script>
@endsection