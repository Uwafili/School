<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status }} | FoodStore</title>
    <style>
        :root { color-scheme: light; font-family: Georgia, 'Times New Roman', serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; color: #27221b; background: #fff8eb; }
        main { width: min(100%, 560px); padding: 48px 32px; text-align: center; background: #fff; border: 1px solid #f2dfbd; border-radius: 24px; box-shadow: 0 20px 50px rgba(116, 78, 23, .12); }
        .mark { width: 56px; height: 56px; margin: 0 auto 24px; display: grid; place-items: center; border-radius: 18px; background: #facc15; color: #27221b; font: 800 24px/1 Arial, sans-serif; }
        .status { margin: 0; color: #c2410c; font: 800 12px/1.2 Arial, sans-serif; letter-spacing: .16em; text-transform: uppercase; }
        h1 { margin: 12px 0; font-size: clamp(30px, 6vw, 46px); line-height: 1.05; }
        p { margin: 0 auto 28px; max-width: 420px; color: #6b6256; font: 16px/1.6 Arial, sans-serif; }
        a { display: inline-block; padding: 13px 20px; border-radius: 12px; color: #fff; background: #292524; font: 700 14px/1 Arial, sans-serif; text-decoration: none; }
        a:hover { background: #57534e; }
    </style>
</head>
<body>
    <main>
        <div class="mark" aria-hidden="true">FS</div>
        <p class="status">Error {{ $status }}</p>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <a href="{{ url('/') }}">Return to FoodStore</a>
    </main>
</body>
</html>