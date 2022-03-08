FROM gitpod/workspace-mysql

USER gitpod

ENV NGINX_DOCROOT_IN_REPO=""

  # Copy required files to /tmp
COPY --chown=gitpod:gitpod .gp/bash/update-composer.sh \
.gp/conf/nginx/nginx.conf \
.gp/bash/.bash_aliases \
.gp/bash/php.sh \
.gp/bash/install-core-packages.sh \
.gp/bash/utils.sh \
.gp/snippets/server-functions.sh \
.gp/bash/install-xdebug.sh \
/tmp/

  # Create log files and move required files to their proper locations
RUN sudo touch /var/log/workspace-image.log \
&& sudo chmod 666 /var/log/workspace-image.log \
&& sudo touch /var/log/workspace-init.log \
&& sudo chmod 666 /var/log/workspace-init.log \
&& sudo touch /var/log/xdebug.log \
&& sudo chmod 666 /var/log/xdebug.log \
&& sudo mv /tmp/nginx.conf /etc/nginx/nginx.conf \
&& sudo mv /tmp/.bash_aliases /home/gitpod/.bash_aliases \
&& sudo mv /tmp/server-functions.sh /home/gitpod/.bashrc.d/server-functions

# Needed for local development? Remove later?
RUN sudo chown gitpod:gitpod /workspace

# Install and configure php and php-fpm
RUN sudo bash -c ". /tmp/php.sh" && rm /tmp/php.sh

# Install core packages
RUN sudo bash -c ". /tmp/install-core-packages.sh" && rm /tmp/install-core-packages.sh

# Download, compile, install and configure xdebug from source
RUN sudo bash -c ". /tmp/install-xdebug.sh" && rm /tmp/install-xdebug.sh

# Update composer
RUN bash -c ". /tmp/update-composer.sh" && rm /tmp/update-composer.sh

# Force the docker image to build by incrementing this value
ENV INVALIDATE_CACHE=13