FROM mariadb:10.3

RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
RUN { \
  echo '[mysqld]'; \
  echo 'character-set-server = utf8mb4'; \
  echo 'collation-server = utf8mb4_general_ci'; \
} >> /etc/mysql/mariadb.conf.d/server.cnf
