FROM debian:stretch

LABEL maintainer = 'pea@poulpocreations.fr'

ENV DEBIAN_FRONTEND noninteractive
VOLUME ["/symfony"]

# Installation
RUN apt-get update && apt-get -y upgrade && apt-get -y -q --no-install-recommends install cron apt-transport-https ca-certificates wget \
    && wget -q -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list \
    && apt-get update && apt-get -y -q --no-install-recommends install php7.2-fpm php7.2-cli php7.2-soap php7.2-gd php7.2-intl \
    	    php7.2-pgsql php7.2-mysql php7.2-xml php7.2-zip php7.2-mbstring xvfb wkhtmltopdf xauth \
    && apt-get clean && rm -fR /var/lib/apt/lists/*

# Timezone
RUN echo 'Europe/Paris' > /etc/timezone && rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime \
    && dpkg-reconfigure -f noninteractive tzdata 

ADD conf/container_web /etc/cron.d/
RUN chmod 644 /etc/cron.d/container_web
RUN service cron start
ENTRYPOINT ["/usr/sbin/cron", "-f", "-L", "15"]

