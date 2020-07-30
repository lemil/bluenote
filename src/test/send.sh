#!/bin/bash 

echo "Sending $1 messages"

for ((n=0;n<$1;n++));
do
  echo "Message $n"

  curl --request POST \
    --url http://bluenote-local.embluemail.com/Notification/enqueue/1000 \
    --header 'content-type: application/json' \
    --data '{"messages":[{"type":"1","tit":"Compaña finalizada","msg":"La campaña 072020_welcome_email finalizó correctamente", "ts":"2020-07-09 18:50:24"},
            {"type":"3","tit":"Error al enviar campaña","msg":"La campaña 072020_welcome_email finalizó con errores", "ts":"2020-07-09 18:50:24"}]}'

  echo "."
done


curl --request POST --url http://10.200.1.143/notification/queue/int-chef  \
    --header 'content-type: application/json' \
    --data-binary '@/tmp/bn_chef.log_ERROR.dump' 

