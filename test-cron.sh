#!/bin/bash

# Simulate cPanel cron jobs running every minute

echo "=== Simulating cPanel Cron Jobs ==="
echo "Press Ctrl+C to stop"
echo ""

while true; do
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Running cron cycle..."
    
    # Queue Worker (processes jobs)
    echo "  → Processing queue..."
    php artisan queue:work --stop-when-empty --quiet
    
    # Scheduler (runs scheduled tasks)
    echo "  → Running scheduler..."
    php artisan schedule:run --quiet
    
    echo "  ✓ Cycle complete"
    echo ""
    
    # Wait 60 seconds (like cPanel cron)
    sleep 60
done
