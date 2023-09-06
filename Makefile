path = docker

clean:
	docker system prune -a -f

build:
	cd $(path) && docker build -t popp .

rebuild: clean build

run:
	cd $(path) && docker run --rm -p 81:80 popp

rerun: rebuild run

compose: clean
	docker-compose up --build -d

bash-app:
	docker-compose exec app sh

bash-db:
	docker-compose exec db sh

create-db:
	docker-compose exec app sh -c 'php bin/console doctrine:database:create'

generate-db:
	docker-compose exec app sh -c 'php bin/console doctrine:schema:update'

ps:
	docker-compose ps

down:
	docker-compose down

up:
	docker-compose up -d

reload: down up