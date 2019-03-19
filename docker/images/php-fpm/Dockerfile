FROM php:7.2-fpm

# variables available at runtime
ENV DEBIAN_FRONTEND noninteractive

# Add filters for documentation
COPY config/dpkg_nodoc /etc/dpkg/dpkg.cfg.d/01_nodoc
COPY config/dpkg_nolocales /etc/dpkg/dpkg.cfg.d/01_nolocales
COPY config/apt_nocache /etc/apt/apt.conf.d/02_nocache
COPY config/remove_doc.sh /usr/local/bin/remove_doc

# add some apt tweaks
COPY config/apt_speedup /etc/dpkg/dpkg.cfg.d/02_apt-speedup

RUN apt-get update && apt-get install -my wget gnupg



# install usefull binaries
RUN apt-get -yqq update && \
    apt-get install -yqq --no-install-recommends git imagemagick libmagickwand-dev openssh-client \
                                                 unzip libpcre3 libfcgi0ldbl libmemcached-dev \
                                                 libpcre3-dev libssl-dev \
                                                 jq vim \
                                                 sudo \
                                                 cron \
                                                 xvfb xauth


# sudoers config - if the user change to be able to do things in the container - bad but may help
COPY config/sudoers /etc

# ------------------------------
# install PHP extensions
# ------------------------------

# exif
RUN docker-php-ext-install exif
RUN docker-php-ext-install soap
RUN docker-php-ext-install zip

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) intl gd opcache mbstring zip pdo_mysql mysqli bcmath

# imagick
RUN echo '' | pecl install imagick
RUN echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini

# memcache
RUN cd /tmp \
    && curl -sSL -o memcache.zip https://github.com/websupport-sk/pecl-memcache/archive/NON_BLOCKING_IO_php7.zip \
    && unzip memcache \
    && cd pecl-memcache-NON_BLOCKING_IO_php7 \
    && /usr/local/bin/phpize \
    && ./configure --with-php-config=/usr/local/bin/php-config  \
    && make \
    && make install \
    && echo "extension=memcache.so" > /usr/local/etc/php/conf.d/ext-memcache.ini \
    && rm -rf /tmp/pecl-memcache-php7 php7.zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ovverides entrypoint
COPY start.sh /usr/local/bin/start.sh
RUN chmod a+x /usr/local/bin/start.sh

# health monitoring
RUN sed -i -e 's/^;ping\.path/ping\.path/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i -e 's/^;pm\.status_path/pm\.status_path/' /usr/local/etc/php-fpm.d/www.conf

COPY config/php-fpm-ping.sh /usr/local/bin/php-fpm-ping
COPY config/php-fpm-status.sh /usr/local/bin/php-fpm-status

RUN chmod a+x /usr/local/bin/php-fpm-ping
RUN chmod a+x /usr/local/bin/php-fpm-status

HEALTHCHECK --interval=1m --timeout=3s CMD /usr/local/bin/php-fpm-ping

# Install wkhtmltopdf
RUN apt-get -yqq update && apt-get install -yqq xvfb wkhtmltopdf xauth

# cleaning
RUN apt-get remove -yqq --purge libpcre3-dev libssl-dev && \
    apt-get clean -yqq && rm -rf /tmp/* /var/lib/apt/cache/* && \
    bash /usr/local/bin/remove_doc && \
    rm -rf /root/*


#Xdebug env variables
ARG XDEBUG_REMOTE_HOST
ARG XDEBUG_REMOTE_PORT
ARG XDEBUG_IDE_KEY
ENV XDEBUG_REMOTE_HOST ${XDEBUG_REMOTE_HOST}
ENV XDEBUG_REMOTE_PORT ${XDEBUG_REMOTE_PORT}
ENV XDEBUG_IDE_KEY ${XDEBUG_IDE_KEY}


## Include the config files
#COPY config/symfony.ini /usr/local/etc/php/conf.d/
#COPY config/symfony.pool.conf /usr/local/etc/php-fpm.d/


# Install nodejs (new version)
RUN curl -sL https://deb.nodesource.com/setup_9.x | bash -
RUN apt-get update
RUN apt-get install -y nodejs
RUN apt-get install -y locales-all

# Install the php memcached extension
RUN curl -L -o /tmp/memcached.tar.gz "https://github.com/php-memcached-dev/php-memcached/archive/php7.tar.gz" \
&& mkdir -p memcached \
&& tar -C memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
&& ( \
  cd memcached \
  && phpize \
  && ./configure \
  && make -j$(nproc) \
  && make install \
) \
&& rm -r memcached \
&& rm /tmp/memcached.tar.gz \
&& docker-php-ext-enable memcached


# Install xDebug, if enabled
ARG INSTALL_XDEBUG=false
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
  # Install the xdebug extension
  pecl install xdebug && \
  docker-php-ext-enable xdebug \
;fi

# Copy xdebug configration for remote debugging
COPY config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Perform the composer update
RUN composer global require hirak/prestissimo

# install yarn
RUN npm install -g yarn

# add crontab
ADD crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab

# aliases
ADD config/alias.sh /etc/profile.d/alias.sh
RUN cat /etc/profile.d/alias.sh > ~/.bashrc

#APCU
RUN pecl install apcu-5.1.5 \
    && docker-php-ext-enable apcu

# images parameters
EXPOSE 9000

WORKDIR /

CMD ["start.sh"]
