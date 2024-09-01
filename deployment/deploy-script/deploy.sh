PROJECT_DIR="/var/www/html/blog"
cd $PROJECT_DIR
git pull

cd api
composer install --no-interaction --optimize-autoloader --no-dev

sudo php artisan optimize:clear
sudo php artisan down
sudo php artisan migrate --force
sudo php artisan optimize
sudo php artisan up

sudo chown -R www-data:www-data $PROJECT_DIR

sudo systemctl restart php8.3-fpm.service

sudo cp $PROJECT_DIR"/deployment/deploy-script/nginx.conf" /etc/nginx/nginx.conf
sudo nginx -t
sudo systemctl reload nginx

sudo cp $PROJECT_DIR"/deployment/deploy-script/supervisord.conf" /etc/supervisor/conf.d/supervisord.conf
sudo supervisorctl update
sudo supervisorctl restart workers:
