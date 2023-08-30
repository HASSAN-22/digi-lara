<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{config('app.name')}}</title>
    <?php
        $style = [
            'bg'=>'
                border: 1px solid #e6e6e6;
                border-radius: 0.5rem;
                padding: 1rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin:3rem;
                background:#f0f0f0;
            ',
            'img-div'=>'text-align: center;',
            'img'=>'width:45%',
            'content'=>[
                'div'=>'
                    margin-top: 2rem;
                    background: #e3e3e3;
                    padding: 2rem;
                    border-radius: 0.4rem;
                ',
                'p'=>'word-wrap: break-word;direction:rtl;'
            ],
            'footer'=>[
                'footer'=>'
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    margin-top: 2rem;
                    gap:8px;
                ',
                'span'=>'font-size: 14px;',
                'a'=>'
                    font-size: 14px;
                    color: #0278e5;
                    text-decoration: none;
                ',
            ]
        ]
    ?>
</head>
<body>
    <div style="{{$style['bg']}}">
        <header style="{{$style['img-div']}}">
            <a href="{{config('app.url')}}" target="_blank">
                <img src="{{$logo}}" style="{{$style['img']}}" />
            </a>
        </header>
        <main style="{{$style['content']['div']}}">
            <p style="{{$style['content']['p']}}">
                {!! $msg !!}
            </p>
        </main>
        <footer style="{{$style['footer']['footer']}}">
            <span style="{{$style['footer']['span']}}">با سپاس و احترام</span>
            <a href="{{config('app.url')}}" target="_blank" style="{{$style['footer']['a']}}">{{config('app.name')}}</a>
        </footer>
    </div>
</body>
</html>
