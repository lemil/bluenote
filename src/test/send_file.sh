#!/bin/bash 
echo "Sending $1 messages , file"

for ((n=0;n<$1;n++));
do
  echo "Message $n"

  curl  --request POST \
        --url http://10.200.1.143/notification/queue/int-chef  \
	    --header 'content-type: application/json' \
	    --data-binary '@/tmp/bn_chef.log_ERROR.dump' 

  echo "."
done
