<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {
	
	//
	public function __construct(){
		parent::__construct();
		$this->load->model('queue_model');
	}

	public function index() {
		echo "alive";
	}

	public function forbidden() {
		header('HTTP/1.0 403 Forbidden');
		echo 'Forbidden';
		die();
	}

	public function failed() {
		header('HTTP/1.0 400 Bad Gateway');
		echo 'Bad Gateway';
		die();
	}


	private function get_queue_name($group,$item)
	{
		//All* queues
		if(strpos($item,'all') === 0){
			return $item;
		} 

		if($group == 'customer'){
			return 'cust-'.$item;
		} else {
			return $item;
		}
	}

	private function get_queue_data_by_routingkey($routing_key)
	{
		$parts = explode('-', $routing_key);
		if(sizeof($parts) > 1){
			$group = '';
			if($parts[0] == 'cust'){
				$group = 'customer';
			} 
			return array('group'=>$group ,'item'=> $parts[1], 'queue_name'=> $routing_key );
		} else {
			return array('group'=>'','item'=> $routing_key, 'queue_name'=> $routing_key );
		}
	}

	private function get_binding_keys($group,$item)
	{
		$binding_keys = array();
		if($group == 'customer'){
			array_push($binding_keys,'allcustomers');
			if($item != 'allcustomers'){
				array_push($binding_keys,'cust-'.$item);
			}
		} else {
			array_push($binding_keys,$group);
			array_push($binding_keys,$item);
		} 
		return $binding_keys;
	}


	//
	public function create_basic_queues($range_max = 3){
		try {

			//Create customer queues
			$qcustq = 0;
			$qs = range(1, $range_max);
			$this->queue_model->open_conn();
			$this->queue_model->open_exchange(); //Create Exchange 

			foreach ($qs as $item) {
				$q_name = $this->get_queue_name('customer',$item);
				$binding_keys = $this->get_binding_keys('customer',$item);
				$this->queue_model->open_queue($q_name,$binding_keys); 
				$qcustq++;
			}
			$this->queue_model->close_conn();

			//
			$data = array();
			$data['result'] = array('customer_queues'=>$qcustq);
			$this->load->view('api/json',$data);

		} catch(Exception $e) {
			echo $e->getMessage();
			log_message('error', 'Notification/create_basic_queues: exception: message='.$e->getMessage());
			$this->failed();	
		}	
	}

	//
	public function queue($queue_name = "") { //Only customers
		try {
			$isauthorized = false; 

			//Validate
			if( strpos($queue_name,'cust-') === 0  ) {
				$isauthorized = true;
			}

			//Validate
			if($isauthorized == true) {
				$jsonmessage = file_get_contents("php://input");
				$inmessages = json_decode($jsonmessage,true);
				//
				$data = array();
				$data['result'] = true;

				$q_data = $this->get_queue_data_by_routingkey($queue_name);
				$msgs = $inmessages['messages'];
				if(isset($inmessages) && array_key_exists('messages', $inmessages)){
					$this->queue_model->open_conn();
					foreach ($msgs as $msg) {
						if(array_key_exists('msg', $msg)){
							$binding_key = $this->get_binding_keys($q_data['group'],$q_data['queue_name']);
							$this->queue_model->publish_queue($q_data['queue_name'],$msg,$binding_key);
						}
					}
					$this->queue_model->close_conn();
				} else {
					$data['result'] = false;
				}
				//
				$this->load->view('api/json',$data);
			} else {
				$this->forbidden();
				log_message('info', 'Notification/queue: forbidden'.$_SERVER['REMOTE_ADDR']);
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			log_message('error', 'Notification/queue: exception: message='.$e->getMessage());
			$this->failed();	
		}
	}

	//
	public function topic($routing_key = "") {
		try {
			$isauthorized = false;

			//Validate
			if( $routing_key == 'allcustomers'    || 
				strpos($routing_key, 'cust-') === 0 ) {
				$isauthorized = true;
			}


			if($isauthorized == true) {
				$jsonmessage = file_get_contents("php://input");
				$inmessages = json_decode($jsonmessage,true);
				//
				$data = array();
				$data['result'] = true;
				if(isset($inmessages) && array_key_exists('messages', $inmessages)){
					$msgs = $inmessages['messages'];
					$this->queue_model->open_conn();
					foreach ($msgs as $msg) {
						if(array_key_exists('msg', $msg)){
							$data_routing = $this->get_queue_data_by_routingkey($routing_key);
							//var_dump($data);
							$binding_keys = $this->get_binding_keys($data_routing['group'],$data_routing['item']);
							//Lazy queue initialization
							$this->queue_model->open_queue($data_routing['queue_name'],$binding_keys);
							$this->queue_model->publish_topic($routing_key,$msg);
						}
					}
					$this->queue_model->close_conn();
				} else {
					$data['result'] = false;
				}
				//
				$this->load->view('api/json',$data);
			} else {
				$this->forbidden();
				log_message('info', 'Notification/topic: forbidden'.$_SERVER['REMOTE_ADDR']);
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			log_message('error', 'Notification/topic: exception: message='.$e->getMessage());
			$this->failed();	
		}
	}


	public function inbox($customer_id = -1) {
		try {
			$isauthorized = false; 

			//Validate
			if(is_numeric($customer_id) && $customer_id > 0) {
				$isauthorized = true;
			}

			if($isauthorized == true) {
				//
				$data = array();
				$q_name = $this->get_queue_name('customer',$customer_id);

				$this->queue_model->open_conn();
				$data['result'] = $this->queue_model->get_all($q_name);
				$this->queue_model->close_conn();

				$data['customer_id'] = $customer_id;
				
				//
				$this->load->view('notification/view',$data);
			} else {
				$this->forbidden();
				log_message('info', 'Notification/inbox: forbidden'.$_SERVER['REMOTE_ADDR']);
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			log_message('error', 'Notification/inbox: exception: message='.$e->getMessage());
			$this->failed();	
		}
	}

	public function ack($customer_id, $tag) {
		try {
			$isauthorized = false;

			//Validate
			if(is_numeric($customer_id)) {
				$isauthorized = true;
			}

			//Authorized
			if($isauthorized == true) {
				//
				$data = array();
				$acks = 0;
				if(isset($tag)){
					$queue_name = $this->get_queue_name('customer',$customer_id);
					$this->queue_model->open_conn();
					$acks = $this->queue_model->ack($queue_name,$tag);
					$this->queue_model->close_conn();
				} else {
					//0
					throw new Exception("Error Processing Request", 1);
				}
				//
				$data['result'] = $acks;
				$data['customer_id'] = $customer_id;
				//
				$this->load->view('api/json',$data);
			} else {
				$this->forbidden();
				log_message('info', 'Notification/ack: forbidden'.$_SERVER['REMOTE_ADDR']);
			}
		} catch(Exception $e) {
			log_message('error', 'Notification/inbox: exception: message='.$e->getMessage());
			$this->failed();	
		}
	}

	public function size($customer_id) {
		try {
			$isauthorized = false; 

			//Validate
			if(is_numeric($customer_id)) {
				$isauthorized = true;
			}

			if($isauthorized == true) {
				//
				$data = array();
				$total = 0;
				
				$queue_name = $this->get_queue_name('customer',$customer_id);

				$this->queue_model->open_conn();
				$total = $this->queue_model->size($queue_name);
				$this->queue_model->close_conn();
				//
				$data['result'] = $total;
				//
				$this->load->view('api/json',$data);
			} else {
				$this->forbidden();
				log_message('info', 'Notification/inbox: forbidden'.$_SERVER['REMOTE_ADDR']);
			}
		} catch(Exception $e) {
			log_message('error', 'Notification/inbox: exception: message='.$e->getMessage());
			$this->failed();	
		}
	}

}
