#!/bin/bash

cd src

docker-compose down

cd ..

sudo chmod 755 ./static/rabbitmq/.erlang.cookie

git add .

git commit -m "auto commit y push : $1"

git push -f

sudo chmod 500 ./static/rabbitmq/.erlang.cookie



