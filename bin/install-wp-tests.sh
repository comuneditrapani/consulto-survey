#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]"
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}
SKIP_DB_CREATE=${6-false}

# il resto è documentazione!
cat <<<EOF

questo file, così come creato da wp scaffold plugin-tests, qui è meglio levarlo di mezzo.

è vero che lo ho utilizzato, ma poi ho dovuto correggere di tutto:

creare il database di test a fianco del database corrente (utente root)
dare all‘utente normale tutti i privilegi sul database di test.
utilizzare il sito wordpress di sviluppo anche per i test.
affiancare al sito di sviluppo i file wordpress per i test (sparse checkout).

qualche comando in pratica?

$ mysql -u root -p -e 'CREATE DATABASE wp_sandbox_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON wp_sandbox_test.* TO "wp_user"@"localhost";
flush privileges;'

git clone --depth=1 --filter=blob:none --sparse https://github.com/WordPress/wordpress-develop.git /var/www/wordpress-develop
cd /var/www/wordpress-develop
git sparse-checkout set tests/phpunit/includes tests/phpunit/data

aggiunte le variabili di ambiente WP_CORE_DIR e WP_TESTS_DIR in phpunit.xml

poi ho dovuto copiare/spostare i due file:
cp /tmp/wordpress-tests-lib/wp-tests-config.php /var/www/wordpress-develop/
cp /tmp/wordpress-develop/wp-tests-config-sample.php /var/www/wordpress-develop/


EOF
