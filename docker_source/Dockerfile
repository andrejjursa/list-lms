FROM ubuntu:15.10

MAINTAINER Andrej Jursa <jursa@freevision.sk>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y openjdk-8-jdk junit4
RUN apt-get install -y python3
RUN apt-get install -y vim
RUN apt-get install -y haskell-platform

RUN apt-get install -y lib32z1 lib32ncurses5 lib32bz2-1.0 

RUN adduser test_runner

RUN mkdir /list_test_files
RUN chmod a+xr,a-w,u+w,g+w /list_test_files
RUN chgrp test_runner /list_test_files
VOLUME /list_test_files

RUN mkdir /list_compilation_process
RUN chmod a+xr,a-w,u+w,g+w /list_compilation_process
RUN chgrp test_runner /list_compilation_process 

ADD run_with_timeout /usr/local/bin/
RUN chmod a+xr,a-w,u+w /usr/local/bin/run_with_timeout
RUN chgrp test_runner /usr/local/bin/run_with_timeout

ADD run_java_jUnit /usr/local/bin/
RUN chmod a+xr,a-w,u+w /usr/local/bin/run_java_jUnit
RUN chgrp test_runner /usr/local/bin/run_java_jUnit

ADD run_python_pyUnit /usr/local/bin/
RUN chmod a+xr,a-w,u+w /usr/local/bin/run_python_pyUnit
RUN chgrp test_runner /usr/local/bin/run_python_pyUnit

ADD run_python_io /usr/local/bin/
RUN chmod a+xr,a-w,u+w /usr/local/bin/run_python_io
RUN chgrp test_runner /usr/local/bin/run_python_io

ADD run_ghc_hunit /usr/local/bin/
RUN chmod a+xr,a-w,u+w /usr/local/bin/run_ghc_hunit
RUN chgrp test_runner /usr/local/bin/run_ghc_hunit

ADD run_test /usr/local/bin/
RUN chmod a-xrw,u+xrw /usr/local/bin/run_test
