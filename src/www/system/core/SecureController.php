<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_SecureController extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
        parent::__construct();
		
        $this->load->library('auth_ldap');
		$this->load->helper('url');
		
		if(!$this->auth_ldap->is_authenticated()) {
			$this->session->keep_flashdata('tried_to');
			redirect('/auth/login');
		} 
	}

	
	
}