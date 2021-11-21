FROM webdevops/php-apache-dev:7.1

RUN apt update && apt upgrade -y && apt install -y npm sudo procmail software-properties-common

# RUN mkdir -p /usr/share/man/man1 && apt update && apt upgrade -y && apt install -y default-jdk

# RUN add-apt-repository -y ppa:openjdk-r/ppa \
#     && (apt update || apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys $(apt update | grep -i no_pubkey | cut -d 'NO_PUBKEY' -f 2) && apt-key list && apt update) \
#     && apt-upgrade -y \
#     && mkdir -p /usr/share/man/man1 \
#     && apt install -y openjdk-15-jdk

RUN eval cdir=$(pwd) && \
    mkdir -p /javaInstall && \
    cd /javaInstall && \
    wget https://download.java.net/openjdk/jdk17/ri/openjdk-17+35_linux-x64_bin.tar.gz && \
    tar xvf openjdk-17+35_linux-x64_bin.tar.gz && \
    ln -s /javaInstall/jdk-17/bin/java /usr/bin/java && \
    ln -s /javaInstall/jdk-17/bin/javac /usr/bin/javac && \
    wget -O junit4.jar https://search.maven.org/remotecontent?filepath=junit/junit/4.13.2/junit-4.13.2.jar && \
    mkdir -p /usr/share/java 2> /dev/null && \
    mv /javaInstall/junit4.jar /usr/share/java/junit4.jar && \
    cd $cdir

# RUN apt update && apt upgrade -y && apt install -y junit junit4

RUN adduser testovac

RUN apt update && apt upgrade -y && apt install -y bc

RUN usermod -a -G application www-data

RUN echo "www-data\tALL=(testovac) NOPASSWD: /app/test_scripts/execute_jUnit_test" > /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac) NOPASSWD: /app/test_scripts/execute_pyUnit_test" >> /etc/sudoers.d/list && \
    echo "www-data\tAll=(testovac) NOPASSWD: /app/test_scripts/exec_pythonIO_test" >> /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac) NOPASSWD: /app/test_scripts/execute_hUnit_test" >> /etc/sudoers.d/list && \
    echo "www-data\tALL=(testovac) NOPASSWD: /app/test_scripts/run_cp_simple_test" >> /etc/sudoers.d/list && \
    chmod 0440 /etc/sudoers.d/list