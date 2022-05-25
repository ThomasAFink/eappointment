
COMPOSER=php -d suhosin.executor.include.whitelist=phar bin/composer.phar

help:
	grep -P "^\w+:" Makefile|sort|perl -pe 's/^(\w+):([^\#]+)(\#\s*(.*))?/\1\n\t\4\n/'

now: # Dummy target

build: # Build javascript and css
	npm run js
	npm run css

update: # update with devel composer.json
	COMPOSER=composer.devel.json $(COMPOSER) update

fix: # run code fixing
	php vendor/bin/phpcbf --standard=psr2 src/
	php vendor/bin/phpcbf --standard=psr2 tests/
	npm run fix

css: now
	npm run css

js: now
	npm run js


live: # init live system
	$(COMPOSER) install --no-dev --prefer-dist

dev: # init development system
	$(COMPOSER) update
	npm install

coverage:
	php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-html public/_tests/coverage/

paratest: # init parallel unit testing with 5 processes
	vendor/bin/paratest --coverage-html public/_tests/coverage/
