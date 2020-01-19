<div class="sticky-top">

    <div class="app-block p-2">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="请输入...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button">搜索</button>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <img class="card-img-top" src="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=2243550038,2878113222&fm=26&gp=0.jpg" alt="Card image cap">
        <div class="card-body">
            <h5 class="card-title">魅力郑州，共筑百万居民梦想</h5>
            <p class="card-text">即日起凡参加 “魅力郑州” 的参与者，即可有机会得到有河南郑州人们政府送出随机抽奖的机会</p>
            <a href="#" class="btn btn-primary">马上报名</a>
        </div>
    </div>

    <div class="app-block">
        @component('components.carousel',[
            'data'=>[
                'http://www.33lc.com/article/UploadPic/2012-7/201272693919814.jpg',
                'http://www.deyu.ln.cn/images/o53xoltemvzwwy3boixgg33n/desktop/else/2011109114827/7.jpg',
            ]
        ])
        @endcomponent
    </div>

    <div class="card mb-3">
        <div class="card-header">相关链接</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#">iView3.0北京发布演讲会</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Vue3.0发布杭州发布演讲会</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">PHP7.5最新消息</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">ThinkPHP6.0 国庆发布！！！</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">小程序最新动向</a>
            </li>
        </ul>
    </div>

</div>
