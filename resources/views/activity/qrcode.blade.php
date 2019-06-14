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
    @if(request('order_no'))
    <script type="text/javascript">
        $(function() {

            $("#pay_res").on('click',function () {
                var url = "{{route('tw.payorder.check',request('order_no'))}}";
                $.ajax({
                    url: url,
                    type:'get',
                    dataType: "json",
                    error:function(data){
                        $.amaran({'message':"服务器繁忙, 请联系管理员！"});
                        return;
                    },
                    success:function(result){
                        if(result.status == 1){
                            $.amaran({'message':result.info});
                            $.pjax({url: result.url, container: '#pjax-container', fragment:'#pjax-container'})
                        } else {
                            $.amaran({'message':result.info});
                        }
                    },
                })
            })

        })
    </script>
    @endif
@endsection