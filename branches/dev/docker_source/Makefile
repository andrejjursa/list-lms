update:
	docker build --rm=true -t list/sandbox .

rebuild:
	docker build --rm=true --no-cache=true --pull=true -t list/sandbox .

run:
	docker run -t -i --rm=true list/sandbox

test:
	docker run -t -i --rm=true --net="none" --volume="$(PWD)/test_volume:/list_test_files" list/sandbox
