FROM hub.babydev.de/base/nginx:1.10

WORKDIR /var/www/web

ENV APP_UPSTREAM="php-fpm:9000"
RUN rm /etc/nginx/conf.d/default.conf
RUN echo "upstream php-fpm {server php-fpm:9000;}" > /etc/nginx/conf.d/upstream.conf

COPY nginx.conf /etc/nginx/
COPY service.conf /etc/nginx/sites-enabled/default

CMD ["nginx", "-g", "daemon off;"]

# Keep this line at the bottom to keep the container update small
#COPY ./application /var/www/
