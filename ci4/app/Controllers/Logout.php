<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Logout extends BaseController{

	/**
	 * index
	 *
	 * log the user out
	 *
	 *
	 * @return string, array
	 */
	function index()
	{
		$this->aauth->logout();

		$data = array();
		$data['aauth'] = $this->aauth;

		return view('loginTeacher', $data);
	}
}
