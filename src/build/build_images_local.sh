#!/bin/bash

IMAGE_NAME="bluenote_api"
IMAGE_VERSION="1"
IMAGE_TAG="$IMAGE_NAME:$IMAGE_VERSION"

#build bluenote_api
docker build -t $IMAGE_TAG ../etc/docker/api 



IMAGE_NAME="bluenote_worker"
IMAGE_VERSION="1"
IMAGE_TAG="$IMAGE_NAME:$IMAGE_VERSION"

#build bluenote_worker 
docker build -t $IMAGE_TAG ../etc/docker/worker 


