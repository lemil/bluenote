#!/bin/bash

REGISTRY="localhost"

echo -n "Docker registry hostname?"
read answer
REGISTRY="$answer"

#login
docker login $REGISTRY


IMAGE_NAME="bluenote_api"
IMAGE_VERSION="1"
IMAGE_TAG="$IMAGE_NAME:$IMAGE_VERSION"

#build bluenote_api
docker build -t $IMAGE_TAG ../etc/docker/api 

#upload to registry 
docker image push $REGISTRY/$IMAGE_NAME


IMAGE_NAME="bluenote_worker"
IMAGE_VERSION="1"
IMAGE_TAG="$IMAGE_NAME:$IMAGE_VERSION"

#build bluenote_worker 
docker build -t $IMAGE_TAG ../etc/docker/worker 

#upload to registry 
docker image push $REGISTRY/$IMAGE_NAME


#logout
docker logout

