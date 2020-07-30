<?php
class CI_BasicModel extends CI_Model {


	public function __construct() 
	{
		parent::__construct();
		$this->load->helper('strings');
	}
		
	public function query($sql,$params = null)
	{
		$query = null;
		if(is_null($params)) {
			$query = $this->db->query($sql);
		} else {
			$aparams = is_array($params)?$params:array($params);
			$sparams = array();
			foreach($aparams as $p) {
				$sp = sanitize_xss_sqli($p);
				array_push($sparams,$sp);
			}
			$query = $this->db->query($sql,$sparams);
		}
		return $query;
	}

	public function first($sql,$params = null)
	{
		$query = $this->query($sql,$params);
		//
		if(!is_null($query)) {
			foreach ($query->result_array() as $row)
			{
				return $row;
			}
		}
		return null;
	}

	public function getGenericAll($table = '')
	{
	    $sql = $this->queries->getGenericAll($table);
	    $query = $this->query($sql);
	    return $query->result_array();
	}

	public function getGenericById($table,$id)
	{
	    $sql = $this->queries->getGenericById($table);
	    $query = $this->query($sql,array($id));
	    return $query->result_array();
	}

	
}