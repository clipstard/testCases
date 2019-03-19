FROM mariadb:10.3.4

LABEL maintainer = 'pea@poulpocreations.fr'

# Timezone
RUN echo 'Europe/Paris' > /etc/timezone && rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

# Add mysql conf
ADD conf/70-tuning.cnf /etc/mysql/conf.d/
