FROM webdevops/php-apache-dev:7.1
ENV DEBIAN_FRONTEND="noninteractive"

RUN apt update && apt upgrade -y && apt install -y npm sudo procmail software-properties-common

RUN eval cdir=$(pwd) && mkdir -p /opt/java && cd /opt/java && \
    wget https://download.java.net/openjdk/jdk15/ri/openjdk-15+36_linux-x64_bin.tar.gz && \
    tar xvf openjdk-15+36_linux-x64_bin.tar.gz && \
    ln -s /opt/java/jdk-15/bin/java /usr/bin/java && \
    ln -s /opt/java/jdk-15/bin/javac /usr/bin/javac && \
    rm -rf /opt/java/openjdk-15+36_linux-x64_bin.tar.gz && \
#    wget -O junit4.jar https://search.maven.org/remotecontent?filepath=junit/junit/4.13.2/junit-4.13.2.jar && \
#    mkdir -p /usr/share/java 2> /dev/null && \
#    mv /opt/java/junit4.jar /usr/share/java/junit4.jar && \
    cd $cdir && \
    echo 'JAVA_HOME="/opt/java/jdk-15/bin"' >> /etc/environment

RUN apt update && apt upgrade -y && mkdir -p /usr/share/man/man1 && apt install -y junit4 && \
    mv /usr/bin/java /usr/bin/java.backup && \
    ln -s /opt/java/jdk-15/bin/java /usr/bin/java

RUN adduser testovac

RUN apt update && apt upgrade -y && apt install -y bc

RUN usermod -a -G application www-data

RUN echo "www-data\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_jUnit_test" > /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_pyUnit_test" >> /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/exec_pythonIO_test" >> /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_hUnit_test" >> /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/run_cp_simple_test" >> /etc/sudoers.d/list && \
    echo "application\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_jUnit_test" >> /etc/sudoers.d/list && \
    echo "application\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_pyUnit_test" >> /etc/sudoers.d/list && \
    echo "application\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/exec_pythonIO_test" >> /etc/sudoers.d/list && \
    echo "application\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/execute_hUnit_test" >> /etc/sudoers.d/list && \
    echo "application\tALL=(testovac:testovac) NOPASSWD: /app/test_scripts/run_cp_simple_test" >> /etc/sudoers.d/list && \
    chmod 0440 /etc/sudoers.d/list

RUN usermod -a -G testovac www-data