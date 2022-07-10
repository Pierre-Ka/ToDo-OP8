fixtures:
	@php bin/console --env=dev doctrine:database:drop -f -q --if-exists
	@php bin/console --env=dev doctrine:database:create -q
	@php bin/console --env=dev doctrine:migrations:migrate -q
	@php bin/console --env=dev doctrine:fixtures:load -n -q

init-test:
	@php bin/console --env=test doctrine:database:drop -f -q --if-exists
	@php bin/console --env=test doctrine:database:create -q
	@php bin/console --env=test doctrine:migrations:migrate -q
	@php bin/console --env=test doctrine:fixtures:load -n -q

tests: init-test
	@php bin/phpunit
