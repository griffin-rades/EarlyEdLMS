<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Logout extends BaseController{

	function index()
	{
		$this->aauth->logout();

		$data = array();
		$data['aauth'] = $this->aauth;

		return view('loginTeacher', $data);
	}
}
