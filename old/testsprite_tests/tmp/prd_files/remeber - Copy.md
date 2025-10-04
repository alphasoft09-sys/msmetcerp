
You can add this to your cron job for automatic cleanup:
0 2 * * * cd /path/to/your/app && php artisan login:clean

Cleanup Command:
# Clean attempts older than 30 days (default)
php artisan login:clean

# Clean attempts older than specified days
php artisan login:clean --days=60