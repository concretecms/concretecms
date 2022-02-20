FROM gitpod/workspace-mysql

USER gitpod

# Copy required files to /tmp
COPY --chown=gitpod:gitpod .gp/bash/update-composer.sh \
    starter.ini \
    .gp/conf/apache/apache2.conf \
    .gp/conf/nginx/nginx.conf \
    .gp/bash/.bash_aliases \
    .gp/bash/php.sh \
    .gp/bash/install-core-packages.sh \
    .gp/bash/install-project-packages.sh \
    .gp/bash/install-xdebug.sh \
    .gp/bash/update-composer.sh \
    .gp/bash/utils.sh \
    .gp/bash/scaffold-project.sh \
    .gp/snippets/server-functions.sh \
    .gp/snippets/browser-functions.sh \
    .gp/bash/bin/hot-reload.sh \
    /tmp/

# Create log files and move required files to their proper locations
RUN sudo touch /var/log/workspace-image.log \
    && sudo chmod 666 /var/log/workspace-image.log \
    && sudo touch /var/log/workspace-init.log \
    && sudo chmod 666 /var/log/workspace-init.log \
    && sudo touch /var/log/xdebug.log \
    && sudo chmod 666 /var/log/xdebug.log \
    && sudo cp /tmp/apache2.conf /etc/apache2/apache2.conf \
    && sudo mv /tmp/nginx.conf /etc/nginx/nginx.conf \
    && sudo mv /tmp/.bash_aliases /home/gitpod/.bash_aliases \
    && sudo mv /tmp/server-functions.sh /home/gitpod/.bashrc.d/server-functions \
    && sudo mv /tmp/browser-functions.sh /home/gitpod/.bashrc.d/browser-functions \
    && sudo mv /tmp/hot-reload.sh /usr/local/bin/hot-reload

# Install and configure php and php-fpm as specified in starter.ini
RUN sudo bash -c ". /tmp/php.sh" && rm /tmp/php.sh

# Install core packages for gitpod-laravel-starter
RUN sudo bash -c ". /tmp/install-core-packages.sh" && rm /tmp/install-core-packages.sh

# Install any user specified packages for the project
RUN sudo bash -c ". /tmp/install-project-packages.sh" && rm /tmp/install-project-packages.sh

# Download, compile, install and configure xdebug from source
RUN sudo bash -c ". /tmp/install-xdebug.sh" && rm /tmp/install-xdebug.sh

# Update composer
RUN bash -c ". /tmp/update-composer.sh" && rm /tmp/update-composer.sh

# Scaffold the Laravel project
RUN bash -c ". /tmp/scaffold-project.sh" && rm /tmp/scaffold-project.sh

# Force the docker image to build by incrementing this value
ENV INVALIDATE_CACHE=232
