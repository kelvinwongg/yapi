# Use this docker command to start development server
# docker run -d -p 8081:80 --name yapi -v "$PWD":/var/www/html php:7.4.25-apache

# Use this docker command to start development server with XDebug enabled
docker run -d -p 8081:80 --name yapi -v "$PWD":/var/www/html --entrypoint="/var/www/html/example/docker-entrypoint.sh" php:7.4.25-apache