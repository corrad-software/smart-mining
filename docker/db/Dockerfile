FROM postgis/postgis:17-3.7

COPY docker/db/init-restore.sh /docker-entrypoint-initdb.d/10-restore.sh
COPY database/smart_mining.dump /dump/smart_mining.dump

RUN chmod 755 /docker-entrypoint-initdb.d/10-restore.sh \
 && sed -i 's/\r$//' /docker-entrypoint-initdb.d/10-restore.sh
