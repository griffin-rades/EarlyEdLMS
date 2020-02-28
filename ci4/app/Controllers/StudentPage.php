<?php namespace App\Controllers;

use App\Libraries\Aauth;

class StudentPage extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		return view('students', $data);
	}
}

