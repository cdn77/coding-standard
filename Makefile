COMPOSER_ARGS += --no-progress --no-interaction

### BEGIN main targets

.PHONY: build
build: vendor
	@echo "Build successfully done."

.PHONY: list
list:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$'

### END

### BEGIN secondary targets

.PHONY: vendor
vendor: vendor/lock

vendor/lock: composer.json
	composer update $(COMPOSER_ARGS)
	touch vendor/lock

### END

### BEGIN tests

.PHONY: test
test:
	vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY: cs
cs:
	vendor/bin/phpcs

.PHONY: fix
fix:
	vendor/bin/phpcbf

.PHONY: static-analysis
static-analysis:
	vendor/bin/phpstan analyse

.PHONY: check
check: build cs static-analysis test

### END

### BEGIN cleaning

.PHONY: clean
clean: clean-vendor

.PHONY: clean-vendor
clean-vendor:
	rm -rf vendor

### END
