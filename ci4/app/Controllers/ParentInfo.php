<?php namespace App\Controllers;

use App\Libraries\Aauth;

class ParentInfo extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		return view('underConstruction', $data);
	}
}

