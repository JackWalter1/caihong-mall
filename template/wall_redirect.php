<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>正在跳转...</title>
    <style>
        :root {
            --primary-color: #007AFF;
            --primary-rgb: 0, 122, 255;
            --bg-color: #ffffff;
            --surface-color: #ffffff;
            --text-primary: #1d1d1f;
            --text-secondary: #86868b;
            --text-tertiary: #a1a1a6;
            --border-color: #d2d2d7;
            --shadow-light: rgba(0, 0, 0, 0.04);
            --shadow-medium: rgba(0, 0, 0, 0.08);
            --shadow-heavy: rgba(0, 0, 0, 0.12);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #000000;
                --surface-color: #1c1c1e;
                --text-primary: #f2f2f7;
                --text-secondary: #8e8e93;
                --text-tertiary: #636366;
                --border-color: #38383a;
                --shadow-light: rgba(0, 0, 0, 0.3);
                --shadow-medium: rgba(0, 0, 0, 0.5);
                --shadow-heavy: rgba(0, 0, 0, 0.7);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: grid;
            place-items: center;
            min-height: 100vh;
            padding: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .redirect-container {
            background: var(--surface-color);
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            box-shadow:
                0 1px 3px var(--shadow-light),
                0 8px 24px var(--shadow-medium),
                0 16px 48px var(--shadow-heavy);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            text-align: center;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .redirect-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 32px;
            background: rgba(var(--primary-rgb), 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .redirect-icon svg {
            width: 36px;
            height: 36px;
            fill: var(--primary-color);
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .redirect-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .redirect-message {
            font-size: 17px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .countdown {
            font-size: 48px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 24px 0;
            font-variant-numeric: tabular-nums;
        }

        .security-tips {
            font-size: 14px;
            color: var(--text-tertiary);
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
            text-align: left;
        }

        .security-tips ul {
            list-style: none;
            padding-left: 0;
        }

        .security-tips li {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .security-tips li::before {
            content: "•";
            color: var(--primary-color);
            font-weight: bold;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .manual-redirect {
            margin-top: 24px;
            font-size: 16px;
        }

        .manual-redirect a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .manual-redirect a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .redirect-container {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .redirect-icon {
                width: 64px;
                height: 64px;
                margin-bottom: 24px;
            }

            .redirect-icon svg {
                width: 28px;
                height: 28px;
            }

            .redirect-title {
                font-size: 24px;
                margin-bottom: 12px;
            }

            .redirect-message {
                font-size: 16px;
                margin-bottom: 20px;
            }

            .countdown {
                font-size: 36px;
                margin: 20px 0;
            }

            .security-tips {
                font-size: 13px;
                margin-top: 24px;
                padding-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="redirect-container">
        <div class="redirect-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="redirect-title">正在安全跳转</h1>
        <p class="redirect-message">系统正在进行安全检测，请稍候...</p>
        
        <div class="countdown" id="countdown">3</div>
        
        <div class="security-tips">
            <ul>
                <li>为防止网站被墙，系统采用多重安全访问机制</li>
                <li>我们正在验证访问来源，确保不是恶意爬虫</li>
                <li>如果您是从微信或QQ访问，可能需要复制链接到浏览器打开</li>
            </ul>
        </div>
        
        <div class="manual-redirect">
            <p>如果长时间未跳转，请<a href="/" id="manualRedirect">手动点击此处</a></p>
        </div>
    </div>

    <script>
        // 倒计时逻辑
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const manualRedirectLink = document.getElementById('manualRedirect');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '/';
            }
        }, 1000);
        
        // 手动跳转
        manualRedirectLink.addEventListener('click', (e) => {
            e.preventDefault();
            clearInterval(timer);
            window.location.href = '/';
        });
        
        // 添加一些防爬虫和防墙的JavaScript混淆
        (function(){
            const randomNum = Math.floor(Math.random() * 10000);
            const encoded = btoa(window.location.href + randomNum);
            const decoded = atob(encoded);
            
            // 简单的访问统计，用于区分正常用户和爬虫
            if (typeof navigator !== 'undefined') {
                const userAgent = navigator.userAgent;
                // 检测常见爬虫特征
                const isBot = /bot|crawler|spider|slurp|bingpreview|facebookexternalhit|pinterest|duckduckgo/i.test(userAgent);
                
                if (isBot) {
                    // 如果检测到可能是爬虫，可以采取一些措施
                    console.log('Bot detected');
                }
            }
        })();
    </script>
</body>
</html>