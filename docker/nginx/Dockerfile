FROM nginx:alpine

RUN apk add --no-cache curl bash

COPY ./docker/nginx/conf.d /etc/nginx/conf.d
WORKDIR /var/www