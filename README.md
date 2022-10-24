# Longterm internet storage of tasks

Learning management system with longterm tasks persistance.

## Application documentations:

* [MOSS Comparator setup](./docs/moss.md)

## Local instalation guide

### Windows Prerequisites
1. Install Docker desktop and WSL (linux console on windows)
2. Enable WSL integration in Docker 
   Settings > General > **Enable the WSL 2 based engine**
3. In ```\Windows\System32\drivers\etc\hosts``` file add ```127.0.0.1 server.listdev```

### Linux Prerequisites
1. Install Docker Engine
2. In ```\etc\hosts``` file add ```127.0.0.1 server.listdev```


### Inicialize LIST locally
For Windows use the WSL console, for Linux use the systems Terminal
1. Within the project directory execute ```docker-compose up -d```
2. Execute ```docker-compose exec list-server bash```
3. Execute ```sudo ./initDependencies```
4. To add a new teacher user account execute ```bin/console new_teacher```

Finally the web server should be running on *[http://server.listdev/admin](http://server.listdev/admin)*. You may use the teacher account created in the last step to login.

You may access the database via Adminer GUI at *[http://server.listdev:8080](http://server.listdev:8080)*