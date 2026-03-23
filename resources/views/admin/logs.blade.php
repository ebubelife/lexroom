<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Logs - FirstMediator</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #e0e0e0;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .header {
            background: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            color: #C9A84C;
        }
        .btn {
            background: #C9A84C;
            color: #0D1B2A;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background: #E8C96A;
        }
        .log-container {
            background: #2d2d2d;
            border-radius: 5px;
            padding: 20px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid #444;
        }
        .log-line {
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 3px;
        }
        .log-line.error {
            background: rgba(220, 38, 38, 0.1);
            border-left: 3px solid #dc2626;
        }
        .log-line.warning {
            background: rgba(245, 158, 11, 0.1);
            border-left: 3px solid #f59e0b;
        }
        .log-line.info {
            background: rgba(59, 130, 246, 0.1);
            border-left: 3px solid #3b82f6;
        }
        .log-line.debug {
            background: rgba(107, 114, 128, 0.1);
            border-left: 3px solid #6b7280;
        }
        .timestamp {
            color: #9ca3af;
            font-size: 0.9em;
        }
        .level {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .level.error { background: #dc2626; color: white; }
        .level.warning { background: #f59e0b; color: white; }
        .level.info { background: #3b82f6; color: white; }
        .level.debug { background: #6b7280; color: white; }
        .auto-refresh {
            margin-left: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Laravel Logs</h1>
        <div>
            <a href="{{ route('logs.clear') }}" class="btn" onclick="return confirm('Clear all logs?')">Clear Logs</a>
            <a href="{{ route('logs.index') }}" class="btn">Refresh</a>
            <span class="auto-refresh">Auto-refresh: <span id="countdown">30</span>s</span>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 3px solid #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <div class="log-container">
        @if(empty($logs))
            <div style="text-align: center; color: #9ca3af; padding: 40px;">
                No logs found
            </div>
        @else
            @foreach($logs as $log)
                @php
                    $logClass = '';
                    $level = 'debug';
                    if (str_contains($log, '.ERROR:')) {
                        $logClass = 'error';
                        $level = 'error';
                    } elseif (str_contains($log, '.WARNING:')) {
                        $logClass = 'warning';
                        $level = 'warning';
                    } elseif (str_contains($log, '.INFO:')) {
                        $logClass = 'info';
                        $level = 'info';
                    }
                @endphp
                <div class="log-line {{ $logClass }}">
                    @if(preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $log, $matches))
                        <span class="timestamp">{{ $matches[1] }}</span>
                        <span class="level {{ $level }}">{{ strtoupper($level) }}</span>
                    @endif
                    <span>{{ $log }}</span>
                </div>
            @endforeach
        @endif
    </div>

    <script>
        let countdown = 30;
        const countdownEl = document.getElementById('countdown');
        
        setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            
            if (countdown <= 0) {
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>