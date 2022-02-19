FROM gitpod/workspace-mysql

USER root

ARG PHP_VERSIONS="7.4"
ARG PHP_EXTENSIONS_VSPECIFIC="bcmath bz2 curl exif ftp gd gmp imagick imap intl json ldap mbstring mysql opcache pdo soap ssh2 xml xsl zip"
ARG PHP_EXTENSIONS_COMMON="php-redis"

# Install extra PHP extensions
RUN \
    echo '######################' && \
    echo '# Installing PHP FPM #' && \
    echo '######################' && \
    apt-get update -qq && \
    APT_PACKAGES='' && \
    for PHP_VERSION in $PHP_VERSIONS; do \
        APT_PACKAGES="$APT_PACKAGES php$PHP_VERSION-fpm"; \
        for PHP_EXTENSION in $PHP_EXTENSIONS_VSPECIFIC; do \
            APT_PACKAGES="$APT_PACKAGES php$PHP_VERSION-$PHP_EXTENSION"; \
        done \
    done;

# Configure nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Set Public Path
ENV NGINX_DOCROOT_IN_REPO=""

# Fix file permissions
RUN chown -R gitpod:gitpod /var/www/
RUN chown -R gitpod:gitpod /etc/nginx/
RUN chown -R gitpod:gitpod /etc/mysql/
RUN chown -R gitpod:gitpod /etc/php/7.4