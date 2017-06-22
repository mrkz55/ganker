#!/bin/sh

if [ "$(ls -A /var/lib/mysql)" ]; then
  echo "[i] MySQL directory already present, skipping creation"

  exec mysqld --user=root --console
fi

echo "[i] MySQL data directory not found, creating initial DBs"

mysql_install_db --user=root > /dev/null

cat >/root/init.sql <<EOL
USE mysql;
CREATE USER '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON * . * TO '${DB_USERNAME}'@'localhost';
FLUSH PRIVILEGES;
EOL

exec mysqld --user=root --bootstrap --verbose=0 < /root/init.sql
#exec sleep 10
#exec mysql < /root/init.sql
#exec mysqld --user=root --console

#exec mysqld --user=root --skip-ssl --console --initialize-insecure --verbose=0 < /root/init.sql
rm -f /root/init.sql
