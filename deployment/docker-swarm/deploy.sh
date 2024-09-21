#!/bin/bash

set -e

cd /usr/src

sudo docker config rm my_env_file
sudo docker config create my_env_file .env

sudo $(cat .env | xargs) docker stack deploy -c docker-compose.prod.yml laravel_devops