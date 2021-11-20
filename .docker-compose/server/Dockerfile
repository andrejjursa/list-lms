FROM webdevops/php-apache-dev:7.1

RUN apt update && apt upgrade -y && apt install -y npm sudo procmail

RUN mkdir -p /usr/share/man/man1 && apt update && apt upgrade -y && apt install -y default-jdk

RUN apt update && apt upgrade -y && apt install -y junit junit4

RUN adduser testovac

