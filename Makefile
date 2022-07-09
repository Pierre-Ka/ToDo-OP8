fixtures:
	@php bin/console --env=dev doctrine:database:drop -f -q --if-exists
	@php bin/console --env=dev doctrine:database:create -q
	@php bin/console --env=dev doctrine:migrations:migrate -q
	@php bin/console --env=dev doctrine:fixtures:load -n -q

