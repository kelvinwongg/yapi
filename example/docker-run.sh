# Use this docker command to start development server
docker run -d -p 8080:80 --name php-apache -v "$PWD":/var/www/html php:7.4.25-apache