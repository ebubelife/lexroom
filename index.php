<?php
/**
 * FirstMediator - Root Index
 * This file redirects to the public folder for Laravel
 */

// Check if we're in the right directory
if (file_exists('public/index.php')) {
    // Redirect to public folder
    header('Location: /public/');
    exit;
} else {
    // Show debug info if Laravel isn't set up yet
    echo "<h1>FirstMediator Setup</h1>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
    echo "<p>Looking for: public/index.php</p>";
    echo "<h3>Files in current directory:</h3>";
    echo "<pre>";
    print_r(scandir('.'));
    echo "</pre>";
    
    if (file_exists('deploy.sh')) {
        echo "<p><strong>Next step:</strong> Run <code>./deploy.sh</code> in cPanel Terminal</p>";
    }
}
?>