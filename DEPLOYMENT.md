# Deployment Guide - 725 LED Tracko

## Server Requirements

- PHP 8.2+
- MySQL/MariaDB
- Composer
- Node.js & NPM (for asset compilation)

## Production Deployment Steps

### 1. Server Environment Configuration

Update your production `.env` file with these critical settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://laravel.725tracko.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

# Session Configuration for HTTPS
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DOMAIN=null

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 2. HTTPS & Reverse Proxy Setup

If your server is behind a reverse proxy (e.g., Nginx, Apache with mod_proxy, Cloudflare):

- The app is already configured to trust proxy headers in `bootstrap/app.php`
- Ensure `APP_URL` uses `https://` protocol
- Set `SESSION_SECURE_COOKIE=true` to enforce HTTPS-only session cookies

### 3. Installation Commands

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key (if not set)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed default users (configure DEFAULT_USERS_JSON in .env first)
php artisan db:seed --class=UserSeeder

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build frontend assets
npm ci
npm run build
```

### 4. File Permissions

```bash
# Storage and bootstrap cache must be writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name laravel.725tracko.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name laravel.725tracko.com;
    root /path/to/725ledtracko/public;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secured
- [ ] SSL/TLS certificate installed and valid
- [ ] `SESSION_SECURE_COOKIE=true` for HTTPS
- [ ] File permissions properly set
- [ ] `.env` file not publicly accessible
- [ ] Default users seeded with strong passwords (via `DEFAULT_USERS_JSON`)

### 7. Troubleshooting "Not Secure" Warning

If you see "Not secure" on an HTTPS site:

1. **Verify SSL Certificate**: Ensure your SSL certificate is valid and not expired
   ```bash
   openssl s_client -connect laravel.725tracko.com:443
   ```

2. **Check Mixed Content**: Ensure all assets load via HTTPS
   - Update `APP_URL=https://...` in `.env`
   - Rebuild with `npm run build`

3. **Verify Session Cookies**: Check browser DevTools → Application → Cookies
   - Cookie should have `Secure` flag when `SESSION_SECURE_COOKIE=true`
   - `SameSite` should match your `SESSION_SAME_SITE` setting

4. **Clear All Caches**:
   ```bash
   php artisan optimize:clear
   ```

5. **Check Proxy Headers**: If behind Cloudflare/proxy, ensure:
   - `X-Forwarded-Proto: https` header is sent
   - `X-Forwarded-Host` matches your domain

### 8. User Seeding (Production)

Instead of hardcoded users, configure via environment:

```env
DEFAULT_USERS_JSON='[{"username":"admin","password":"secure_password_here","role":"Admin","full_name":"System Administrator"}]'
```

Then run:
```bash
php artisan db:seed --class=UserSeeder
```

### 9. Maintenance Mode

To enable/disable maintenance mode:

```bash
# Enable
php artisan down --secret="bypass-token"

# Disable
php artisan up
```

Access site during maintenance with: `https://yoursite.com/bypass-token`

## Post-Deployment

1. Test login functionality
2. Verify session persistence across page loads
3. Check all features (orders, reception, repair, QC, tracking)
4. Monitor logs: `storage/logs/laravel.log`

## Support

For issues, check:
- Laravel logs: `storage/logs/`
- Web server logs: `/var/log/nginx/` or `/var/log/apache2/`
- PHP-FPM logs: `/var/log/php-fpm/`
