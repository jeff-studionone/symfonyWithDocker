# Symfony 5 with Docker Example
This is an example how to configure Symfony with Docker with an small app running adding comment and replies to the comments. 

## Prerequisites
This example requires that you have nginx or somthing similar to run it in you localhost. However, you can use this repository that already has the nginx configured [nginx-proxy](https://github.com/studionone/docker-nginx-proxy).

- Docker
- docker-compose

## Run
1. If you want to run it with `nginx-proxy` just download or clone the project and run the container.
    ```
        $ cd docker-nginx-proxy/
        $ docker-compose up -d
    ```
1. Clone **Symfony docker Example** and build it with docker-compose
    ```
        $ cd symfonyWithDocker/
        $ docker-compose build && docker-compose up -d
    ```
1. Connect into the container and run `composer`, the following command will install the vendors. 
    ```$xslt
        $ docker exec -it comments bash
        $ composer install 
    ```
1. Now you will need to run `Doctrine` to migrate the database. 
```$xslt
    $ docker exec -it comments-db bash
    $ php bin/console doctrine:migrations:migrate
```
1. Save the virtual host `comments.docker` into your `/etc/hosts`
1. Now open your browser and go to [comments.docker](http://comments.docker/)
1. Create an `.env` file in your root directory with the following variables, it is up to you if you change the config. 
    ```$xslt
        VIRTUAL_HOST= comments.docker
        DATABASE_URL= comments-db
        MYSQL_HOST= comments-db
        MYSQL_USER=  admin
        MYSQL_PASSWORD= 123456
        MYSQL_DATABASE= symfony
        MYSQL_ROOT_PASSWORD= 123456
    ```
1. Perhaps you are going to have a problem with permissions and this is because the container is running with super user and it create the folder `docker/local/mysql`, so please change the permission of this folder if you have any problem and that will work fine again. 


## Test
You can start listing and creating new submissions in the home page or list all replies of the main comment.

## Code
This code is pretty simple and you can follow through. However, it follows the basic principals of Symfony.
- `code/src/Entity` This folder has two entities one with comments and the other with responses both with the `assert` validation and the relationship between comments `OneToMany` with responses
- `code/src/Repository` Here you can create your custom queries or overwrite the existing ones. 
- `code/src/Controller` Here is where the main logic of you code should be. 


## Note
For more information about Symfony check its official [documentation](https://symfony.com/doc). 
