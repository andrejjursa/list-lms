build:
	docker build -t list/sandbox .

run:
	docker run -t -i --rm=true list/sandbox

test:
	docker run -t -i --rm=true --volume="$(PWD)/test_volume:/list_test_files" list/sandbox
