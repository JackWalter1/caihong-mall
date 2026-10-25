<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $conf['wall_guide_title']?$conf['wall_guide_title']:'网站引导页';?></title>
    <style>
        :root {
            --primary-color: <?php echo !empty($conf['wall_guide_theme_color']) ? $conf['wall_guide_theme_color'] : '#007AFF'; ?>;
            --primary-rgb: <?php
                $hex = !empty($conf['wall_guide_theme_color']) ? $conf['wall_guide_theme_color'] : '#007AFF';
                list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
                echo "$r, $g, $b";
            ?>;

            /* Light theme variables */
            --bg-color: #ffffff;
            --surface-color: #ffffff;
            --text-primary: #1d1d1f;
            --text-secondary: #86868b;
            --text-tertiary: #a1a1a6;
            --border-color: #d2d2d7;
            --shadow-light: rgba(0, 0, 0, 0.04);
            --shadow-medium: rgba(0, 0, 0, 0.08);
            --shadow-heavy: rgba(0, 0, 0, 0.12);
            --overlay-bg: rgba(0, 0, 0, 0.4);
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
                --overlay-bg: rgba(0, 0, 0, 0.6);
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

        .guide-container {
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
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: center;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .guide-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 32px;
            background: rgba(var(--primary-rgb), 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .guide-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.2), rgba(var(--primary-rgb), 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .guide-icon:hover::before {
            opacity: 1;
        }

        .guide-icon svg {
            width: 28px;
            height: 28px;
            fill: var(--primary-color);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .guide-icon:hover svg {
            transform: scale(1.1);
        }

        .guide-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .guide-content {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 40px;
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }

        .continue-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 32px;
            font-size: 17px;
            font-weight: 600;
            color: white;
            background: var(--primary-color);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 160px;
            box-shadow:
                0 1px 3px rgba(var(--primary-rgb), 0.3),
                0 4px 12px rgba(var(--primary-rgb), 0.15);
        }

        .continue-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .continue-btn:hover {
            transform: translateY(-1px);
            box-shadow:
                0 2px 6px rgba(var(--primary-rgb), 0.4),
                0 8px 24px rgba(var(--primary-rgb), 0.2);
        }

        .continue-btn:hover::before {
            opacity: 1;
        }

        .continue-btn:active {
            transform: translateY(0);
        }

        .continue-btn svg {
            width: 16px;
            height: 16px;
            transition: transform 0.3s ease;
        }

        .continue-btn:hover svg {
            transform: translateX(2px);
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            body {
                padding: 16px;
            }

            .guide-container {
                padding: 32px 24px;
                border-radius: 20px;
            }

            .guide-icon {
                width: 56px;
                height: 56px;
                margin-bottom: 24px;
            }

            .guide-icon svg {
                width: 24px;
                height: 24px;
            }

            .guide-title {
                font-size: 24px;
                margin-bottom: 8px;
            }

            .guide-content {
                font-size: 16px;
                margin-bottom: 32px;
            }

            .continue-btn {
                padding: 14px 28px;
                font-size: 16px;
                min-width: 140px;
            }
        }
    </style>
</head>
<body>
    <div class="guide-container">
        <div class="guide-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z" fill="currentColor"/>
                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
            </svg>
        </div>

        <h1 class="guide-title"><?php echo $conf['wall_guide_title']?$conf['wall_guide_title']:'欢迎访问';?></h1>

        <p class="guide-content"><?php echo $conf['wall_guide_content']?$conf['wall_guide_content']:'点击下方按钮继续访问网站';?></p>

        <button type="button" onclick="continueVisit()" class="continue-btn">
            <span><?php echo $conf['wall_guide_btn']?$conf['wall_guide_btn']:'继续访问';?></span>
            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <script>
    function continueVisit() {
        const btn = document.querySelector('.continue-btn');
        const container = document.querySelector('.guide-container');

        // 禁用按钮
        btn.disabled = true;
        btn.style.pointerEvents = 'none';

        // 更新按钮状态
        btn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.8">
                    <animateTransform attributeName="transform" type="rotate" values="0 12 12;360 12 12" dur="1s" repeatCount="indefinite"/>
                </path>
            </svg>
            <span>正在进入...</span>
        `;

        // 设置Cookie
        const intervalValue = <?php echo !empty($conf['wall_guide_interval']) ? intval($conf['wall_guide_interval']) : 24; ?>;
        const intervalUnit = '<?php echo !empty($conf['wall_guide_interval_unit']) ? $conf['wall_guide_interval_unit'] : 'hour'; ?>';

        let intervalMs;
        switch(intervalUnit) {
            case 'second':
                intervalMs = intervalValue * 1000;
                break;
            case 'minute':
                intervalMs = intervalValue * 60 * 1000;
                break;
            case 'hour':
            default:
                intervalMs = intervalValue * 60 * 60 * 1000;
                break;
        }

        const exp = new Date();
        exp.setTime(exp.getTime() + intervalMs);
        
        // 使用更可靠的方式设置Cookie
        document.cookie = "wall_guide_skip=1; expires=" + exp.toUTCString() + "; path=/; SameSite=Lax";

        // 添加退出动画
        container.style.animation = 'slideDown 0.4s cubic-bezier(0.4, 0, 1, 1) forwards';

        // 页面跳转 - 先跳转到过渡页面
        setTimeout(() => {
            console.log('设置的Cookie:', document.cookie);
            window.location.href = '/template/wall_redirect.php';
        }, 500);
    }

    // 添加退出动画
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            to {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
        }
    `;
    document.head.appendChild(style);
    </script>
</body>
</html>