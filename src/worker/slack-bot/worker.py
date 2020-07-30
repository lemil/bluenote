import time
from collections import defaultdict
import logging
import os
import rabbitpy
import signal
import requests
import json
from pprint import pprint


logging.basicConfig(
    format='%(asctime)s [ %(levelname)s ] | %(filename)s | %(funcName)s | %(message)s',
    level=logging.INFO)
logger = logging.getLogger(__name__)


MQ_HOST = os.environ['MQ_HOST']
MQ_USER = os.environ['MQ_USER']
MQ_PASS = os.environ['MQ_PASS']
MQ_SLACK_QUEUE = os.environ['MQ_SLACK_QUEUE']


def send_post(message):
    msg = message.json()
    pload = '{"text":"*'+msg['tit']+"* : "+msg['msg']+ '"}'
    headers = {'content-type': 'application/json'}
    url = 'https://hooks.slack.com/services/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'
    r = requests.post(url,data=pload, headers=headers)
    print('Response '+r.text)
    if r.text == 'ok':
        message.ack(True)


def do_work():
    global stop
    with rabbitpy.Connection(f'amqp://{MQ_USER}:{MQ_PASS}@{MQ_HOST}:5672/%2f') as conn:
        with conn.channel() as channel:
            queue = rabbitpy.Queue(channel, MQ_SLACK_QUEUE)
            logger.info('Reading queue '+MQ_SLACK_QUEUE)
            while not stop:
                try:
                    logger.info(f'Queue len {len(queue)}')
                    while len(queue) > 0:
                        message = queue.get(True)
                        if(message):
                            send_post(message)

                except Exception as err:
                    logger.exception("Unhandled exception.")

                logger.info('sleeping 5')  
                time.sleep(5)


def sigterm_handler(*args):
    global stop
    logger.info('SIGTERM received, time to leave.')
    stop = True


if __name__ == '__main__':
    logger.info("Waiting MQ start...")
    # time.sleep(15)
    stop = False
    # Register the signal to the handler
    signal.signal(signal.SIGTERM, sigterm_handler)  # Used by this script
    logger.info('Started, waiting for SIGTERM')
    do_work()

