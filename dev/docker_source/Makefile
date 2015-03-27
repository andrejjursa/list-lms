update:
	docker build -t list/sandbox .

rebuild:
	docker build --no-cache=true --pull=true -t list/sandbox .

run:
	docker run -t -i --rm=true list/sandbox

test:
	docker run -t -i --rm=true --volume="$(PWD)/test_volume:/list_test_files" list/sandbox

clear:
	docker images -a | grep --text "<none>" | awk '{print $3}' | xargs docker rmi
