<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Queue_model extends CI_Model {

    public $channel = null;
    public $connection = null;
    public $exchange_name = 'bluenote.topics';


    //
    public function __construct(){
        parent::__construct();
    }



    public function open_conn()
    {
        //
        $user =  $this->config->item('rabbitmq_user');                
        $pass =  $this->config->item('rabbitmq_pass');
        $this->connection = new AMQPStreamConnection('mq', 5672, $user, $pass);
        $this->channel = $this->connection->channel();
    }

    public function open_queue($queue_name,$binding_keys = array()){
        //
        $this->channel->queue_declare($queue = $queue_name,
                                $passive = false,
                                $durable = true,
                                $exclusive = false,
                                $auto_delete = false,
                                $nowait = false,
                                $arguments = null,
                                $ticket = null
                                );

        if(sizeof($binding_keys) >0){
            foreach ($binding_keys as $bkey) {
                //echo $queue_name .':'. $bkey."\n";
                $this->channel->queue_bind($queue_name, $this->exchange_name, $bkey);
            }        
        }
    }

    public function open_exchange(){

        //Exchange
        $this->channel->exchange_declare($this->exchange_name, 'topic', false, true, false);

    }

    public function close_conn()
    {
        //Close
        $this->channel->close();
        $this->connection->close();
    }


    public function get_all($queue_name)
    {
        $this->open_queue($queue_name);

        //
        $cont = true;
        $msgs = array();
        while ($cont) {
            $msg = $this->channel->basic_get($queue_name);
            if( isset($msg) ){
                $tag = $msg->delivery_info['delivery_tag'];
                $msgs[$tag] = $msg->body; 
            } else {
                $cont = false;
            }
        }

        return $msgs;
    }

    public function ack($queue_name,$tag)
    {
        $this->open_queue($queue_name);

        $acks = 0;
        $cont = true;

        while ($cont) {
            $msg = $this->channel->basic_get($queue_name);
            if(!isset($msg)){
                $cont = false;
            } else {
                $mtag = $msg->delivery_info['delivery_tag'];
                if($tag == $mtag || $tag == 'all'){
                    $this->channel->basic_ack($mtag);
                    $acks ++; 
                }
            }          
        }

        return $acks;
    }

    public function size($queue_name)
    {
        //
        $this->open_queue($queue_name);

        $total = 0;
        $cont = true;

        while ($cont) {
            $msg = $this->channel->basic_get($queue_name);
            if(!isset($msg)){
                $cont = false;
            } else {
                $total++;
            }          
        }

        return $total;
    }


    public function publish_queue($queue_name,$msg,$binding_keys = array())
    {
        $this->open_queue($queue_name,$binding_keys);

        //
        $amqp_msg = new AMQPMessage(json_encode($msg));
        $this->channel->basic_publish($amqp_msg, '', $queue_name);
    }

    public function publish_topic($routing_key,$msg)
    {
        //
        $this->open_exchange();

        //Send Message
        $amqp_msg = new AMQPMessage(json_encode($msg));
        $this->channel->basic_publish($amqp_msg, $this->exchange_name, $routing_key);
    }


}
