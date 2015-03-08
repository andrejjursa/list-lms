FROM ubuntu:14.04

MAINTAINER Andrej Jursa <jursa@freevision.sk>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y openjdk-7-jdk junit4
RUN apt-get install -y python3

RUN apt-get install -y lib32z1 lib32ncurses5 lib32bz2-1.0 

RUN mkdir /list_test_files
RUN chmod 777 /list_test_files
VOLUME /list_test_files

RUN mkdir /list_compilation_process
RUN chmod 777 /list_compilation_process

RUN mkdir /list_shared_files
RUN chmod 777 /list_shared_files

ADD LISTTests.java /list_shared_files/

ADD run_with_timeout /usr/local/bin/
RUN chmod 777 /usr/local/bin/run_with_timeout

ADD run_java_junit /usr/local/bin/
RUN chmod 777 /usr/local/bin/run_java_junit

ADD run_python_punit /usr/local/bin/
RUN chmod 777 /usr/local/bin/run_python_punit
