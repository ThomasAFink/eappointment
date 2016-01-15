COMPOSER=php -d suhosin.executor.include.whitelist=phar bin/composer.phar


.PHONY: help now dev live watch coverage

help: # This help
	@echo "Possible Targets:"
	@grep -P "^\w+:" Makefile|sort|perl -pe 's/^(\w+):([^\#]+)(\#\s*(.*))?/ \1\n\t\4\n/'

now: # Dummy target

dev: # init development system
	$(COMPOSER) update

live: # init live system, delete unnecessary libs
	$(COMPOSER) update --no-dev

coverage: # create a code coverage report for testing
	php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-html coverage/
