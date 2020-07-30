FROM python:3.6-slim

WORKDIR /usr/emblue

RUN pip install requests
RUN pip install pika
RUN pip install redis
RUN pip install pymssql
RUN pip install rabbitpy
RUN pip install slack-cli

CMD [ "python", "-u", "./worker.py" ]

RUN echo "alias ll='ls -la --color'" >> /root/.bashrc
