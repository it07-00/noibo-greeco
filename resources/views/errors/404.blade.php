<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | GREECO</title>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
            overflow: hidden;
            padding: 20px;
        }
        .container {
            max-width: 550px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .error-card {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 50px 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        /* Background subtle glow objects */
        .glow-circle {
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            filter: blur(30px);
            pointer-events: none;
        }
        .glow-circle-1 {
            top: -100px;
            left: -100px;
        }
        .glow-circle-2 {
            bottom: -100px;
            right: -100px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
        }
        /* Illustration/Animation area */
        .illustration-wrapper {
            position: relative;
            margin-bottom: 30px;
            display: inline-block;
        }
        .error-code {
            font-size: 110px;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            letter-spacing: -2px;
            animation: pulseCode 3s ease-in-out infinite;
        }
        .error-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 160px;
            height: 160px;
            border: 2px dashed rgba(96, 165, 250, 0.2);
            border-radius: 50%;
            animation: rotateRing 20s linear infinite;
            pointer-events: none;
        }
        .error-ring-2 {
            width: 200px;
            height: 200px;
            border: 1px dashed rgba(139, 92, 246, 0.15);
            animation: rotateRingReverse 25s linear infinite;
        }
        .error-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .error-msg {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 35px;
            font-weight: 400;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        }
        .btn-home:active {
            transform: translateY(0);
        }
        /* Keyframes */
        @keyframes pulseCode {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.2)); }
            50% { transform: scale(1.03); filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.4)); }
        }
        @keyframes rotateRing {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }
        @keyframes rotateRingReverse {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(-360deg); }
        }
    </style>
</head>
<body>
    <div class="glow-circle glow-circle-1"></div>
    <div class="glow-circle glow-circle-2"></div>
    
    <div class="container">
        <div class="error-card">
            <div class="illustration-wrapper">
                <div class="error-ring"></div>
                <div class="error-ring error-ring-2"></div>
                <h1 class="error-code">404</h1>
            </div>
            <h2 class="error-title">Không tìm thấy trang</h2>
            <p class="error-msg">Đường dẫn bạn yêu cầu không tồn tại hoặc đã bị di chuyển khỏi hệ thống GREECO.</p>
            <a href="/" class="btn-home">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Quay lại Trang chủ
            </a>
        </div>
    </div>
</body>
</html>
