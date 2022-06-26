# MOSS Comparator settings

This short manual describes the settings necessary to make the MOSS Comparator works.

## Configuration files

* `application/config/moss.php` this file contains definition of moss user id, server, port and language definitions (with their file extensions). For production copy the file into `application/config/production/moss.php`.
  
  You can set your moss user id obtained from moss service to the config parameter:
  ```php
  $config['moss_user_id'] = '<here goes your moss user id>';
  ```
  
  You can set the MOSS consumer to stop when it handles a message:
  ```php
  $config['moss_stop_on_message'] = true;
  ```
  
  Do not update other parameters!

* `application/config/amqp.php` configures the connection to the AMQP service, like __RabbitMQ__, for example.
  You can either copy the file into `application/config/production/amqp.php` and edit the config or you can set up several environmental variables which are defined in the default file.
  
  The configuration items are:
  * ```php
    $config['amqp_host'] = '<IPv4 or hostname>';
    ```
    The host where the AMQP service is running. You can use the env. variable `LIST_AMQP_HOST` instead if you leave this item untouched.
  * ```php
    $config['amqp_port'] = '<port number>';
    ```
    The port number on which the AMQP service is listening. You can use env. variable `LIST_AMQP_PORT` instead if you leave this item untouched.
  * ```php
    $config['amqp_user'] = '<amqp username>';
    ```
    The username on the AMQP service. You can use env. variable `LIST_AMQP_USER` instead if you leave this item untouched.
  * ```php
    $config['amqp_password'] = '<amqp user password>';
    ```
    The password corresponding to the username provided to the item before. You can use env. variable `LIST_AMQP_PASSWORD` instead if you leave this item untouched.
  * ```php
    $config['amqp_vhost'] = '<amqp virtual host name>';
    ```
    The virtual host on the AMQP server, which is dedicated to the LIST. Always starts with `/`. You can use env. variable `LIST_AMQP_VHOST` instead if you leave this item untouched.
  
  Note that _when you copy the file to the `production` subdirectory it will completely override the default one_.

* `application/config/redis.php` configures he connection to the Redis in-memory storage server. This one is used for mutual exclusion mechanisms in the MOSS Comparator algorithms.
  You can make the copy of the file into `application/config/production/redis.php`. This will allow you to make manual changes.
  There are several configuration items:
  * ```php
    $config['redis']['lock']['host'] = '<IPv4 or hostname>';
    ```
    The host where the Redis server is running. You can use env. variable `LIST_REDIS_LOCK_HOST` instead if you leave this item untouched.
  * ```php
    $config['redis']['lock']['port'] = '<port number>';
    ```
    The port number on which the Redis server is listening. You can use env. variable `LIST_REDIS_LOCK_PORT` instead if you leave this item untouched.
  * ```php
    $config['redis']['lock']['scheme'] = '<scheme of the communication protocol>';
    ```
    The scheme of the communication protocol of the Redis server. By default, Redis uses TCP protocol. You can use env. variable `LIST_REDIS_LOCK_SCHEME` instead if you leave this item untouched.

## Other configuration

* To run the MOSS Comparator, one or more instances of the task consumers must be run: 
  ```shell
  > bin/console moss_consume
  ```
  This process connects to the AMQP server and consumes the tasks (comparison jobs) that are created through the LIST administration. Usually it is enough to run single process (you can run more if the load is too high).
  
  Please consider that the process may fail and stop. In such case the tasks will not be processed anymore. Set up some kind of watcher to re-execute the process on failure. For example using [supervisord](http://supervisord.org/).

* There is also comparison tasks cleaner that has to be run by CRON:
  ```shell
  > bin/console moss_clean_up_comparisons
  ```
  Please set up the CRON job for this process to run once per 10 minutes. Single run of this process can clean up at most 1000 old comparison tasks. The tasks are cleared when they:
  * are finished successfully but the results are no longer available on the MOSS service,
  * are failed and older than 2 days,
  * are in processing state for more than 1 day (i.e. the task crashes).
  
  Please note that this process has it own locking mechanism and if it is called again while it is working, it will stop. The process may also stop when it looses his lock by timing-out. The lock timeout is set for 30 minutes.