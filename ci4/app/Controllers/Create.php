<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Create extends BaseController{

	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		return view('createAccount', $data);
	}
	function newUser(){
		$data = array();
		$this->aauth = new Aauth();

		$data['aauth'] = $this->aauth;

		$userEmail = $this->request->getVar('userEmail');
		$userPassword = $this->request->getVar('userPassword');
		$userName = $this->request->getVar('userName');

		$this->aauth->createUser($userEmail, $userPassword, $userName);

		$this->aauth->login($userEmail,$userPassword, false);

		$data['user'] = $this->aauth->getUser();

		return view('teacherHome', $data);
	}
}
