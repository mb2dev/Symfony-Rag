ARG UID=1000
ARG GID=1000

FROM nginx:latest

ADD nginx.conf /etc/nginx/

RUN echo "upstream php-upstream { server php-fpm:9000; }" > /etc/nginx/conf.d/upstream.conf

# Set working directory
WORKDIR /var/www