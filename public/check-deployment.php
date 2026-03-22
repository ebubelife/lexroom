<!DOCTYPE html>
<html>
<head>
    <title>LexRoom - Deployment Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #0D1B2A; border-bottom: 3px solid #C9A84C; padding-bottom: 10px; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .command { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
        .command code { background: transparent; color: #f8f8f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 LexRoom Deployment Status Check</h1>
        
        <?php
        $checks = [];
        
        // Check if controllers exist
        $controllers = [
            'ReportsController' => app_path('Http/Controllers/ReportsController.php'),
            'WalletController' => app_path('Http/Controllers/WalletController.php'),
            'LexReferController' => app_path('Http/Controllers/LexReferController.php'),
            'ProfileController' => app_path('Http/Controllers/ProfileController.php'),
        ];
        
        foreach ($controllers as $name => $path) {
            $checks[] = [
                'name' => $name,
                'status' => file_exists($path),
                'path' => $path
            ];
        }
        
        // Check git status
        $gitPull = shell_exec('cd ' . base_path() . ' && git log -1 --pretty=format:"%h - %s (%cr)" 2>&1');
        
        // Check if storage link exists
        $storageLink = file_exists(public_path('storage'));
        
        // Display results
        $allGood = true;
        foreach ($checks as $check) {
            if (!$check['status']) {
                $allGood = false;
                echo '<div class="status error">';
                echo '<strong>❌ ' . $check['name'] . '</strong> - NOT FOUND<br>';
                echo '<small>Expected at: ' . $check['path'] . '</small>';
                echo '</div>';
            } else {
                echo '<div class="status success">';
                echo '<strong>✅ ' . $check['name'] . '</strong> - Found';
                echo '</div>';
            }
        }
        
        echo '<div class="status info">';
        echo '<strong>📝 Latest Git Commit:</strong><br>';
        echo '<code>' . ($gitPull ?: 'Unable to check git status') . '</code>';
        echo '</div>';
        
        echo '<div class="status ' . ($storageLink ? 'success' : 'error') . '">';
        echo '<strong>' . ($storageLink ? '✅' : '❌') . ' Storage Symlink:</strong> ';
        echo $storageLink ? 'Exists' : 'Missing (run: php artisan storage:link)';
        echo '</div>';
        
        if (!$allGood) {
            echo '<div class="status error">';
            echo '<h3>⚠️ Action Required</h3>';
            echo '<p>Some controllers are missing. Please run these commands on your server:</p>';
            echo '<div class="command"><code>';
            echo 'cd ' . base_path() . '<br>';
            echo 'git pull origin main<br>';
            echo 'composer install --no-dev --optimize-autoloader<br>';
            echo 'php artisan optimize:clear<br>';
            echo 'php artisan config:cache<br>';
            echo 'php artisan route:cache';
            echo '</code></div>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h3>✅ All Controllers Found!</h3>';
            echo '<p>If you\'re still seeing errors, run:</p>';
            echo '<div class="command"><code>';
            echo 'php artisan optimize:clear<br>';
            echo 'php artisan config:cache<br>';
            echo 'php artisan route:cache';
            echo '</code></div>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;">
            <a href="/dashboard" style="color: #C9A84C; text-decoration: none; font-weight: bold;">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
