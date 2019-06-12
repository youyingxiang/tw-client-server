<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title>选手排名</title>
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/home/css/server.css")}}">
</head>
<body id="screen_body">
<div class="screen">
    <!--标题-->
    <div class="screen_title">
        <img id="screen_title_img" src="{{tw_asset("/vendor/tw/home/img/rank_title.png")}}" alt="">
    </div>
    <!--LOGO-->
    <div class="screen_logo">
        <p><b>新天维</b>评分系统</p>
    </div>
    <!--选手-->
    <div class="screen_player">
        <h1>恒大华府青少年歌舞大赛</h1>
    </div>
    <!--选手排名-->
    <div class="rank_list">
        <div class="rank_top">
            <ul>
                <li class="top_img" style="margin-top: 5%"><img src="{{$aRank[1]['img']}}" alt=""><p>{{$aRank[1]['name']}} <b>{{$aRank[1]['score']}}</b></p><img  style="width: 40px !important;" src="{{tw_asset("/vendor/tw/home/img/two.png")}}" alt=""></li>
                <li class="top_img"><img src="{{$aRank[0]['img']}}" alt=""><p>{{$aRank[0]['name']}} <b>{{$aRank[0]['score']}}</b></p><img  style="width: 40px !important;" src="{{tw_asset("/vendor/tw/home/img/one.png")}}" alt=""></li>
                <li class="top_img" style="margin-top: 5%"><img src="{{$aRank[2]['img']}}" alt=""><p>{{$aRank[2]['name']}} <b>{{$aRank[2]['score']}}</b></p><img  style="width: 40px !important;" src="{{tw_asset("/vendor/tw/home/img/three.png")}}" alt=""></li>
            </ul>
        </div>
        <div class="rank_bot">
            <ul>
                @foreach(array_slice($aRank,3) as $v)
                <li><p class="p1">0{{$loop->iteration+3}}</p><p  class="p2"><img src="{{$v['img']}}" alt=""></p><p>{{$v['name']}}</p><p  class="p3">{{$v['score']}}</p></li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
</body>
</html>