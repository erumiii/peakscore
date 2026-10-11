ARG APP_IMAGE=keenanmz/peakscore:latest
FROM ${APP_IMAGE} AS app

FROM nginx:alpine
COPY --from=app /var/www/html/public /var/www/html/public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf