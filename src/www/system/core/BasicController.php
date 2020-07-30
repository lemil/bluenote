<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_BasicController extends CI_Controller {
			
	/**
	 * Constructor
	 */
	public function __construct()
	{
        parent::__construct();
		$this->load->helper('strings');
	}

	
	public function index() {	
		$data = array();
		$data['r'] = '0';
		$data['d'] = '0';
		$this->load->view('json',$data);	
	}
	
	
}
 