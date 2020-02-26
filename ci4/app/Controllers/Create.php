<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Create extends BaseController{

	/**
	 * index
	 *
	 * loads the create account page
	 *
	 *
	 * @return string, array
	 */
	function index(){
		$data = array();
		$this->aauth = new Aauth();
		$data['aauth'] = $this->aauth;

		return view('createAccount', $data);
	}

	/**
	 * newUser
	 *
	 * Gets the form data when the create account form is submitted
	 *
	 *
	 * @return string, array
	 */
	function newUser(){
		$data = array();
		$this->aauth = new Aauth();

		$data['aauth'] = $this->aauth;

		$userEmail = $this->request->getVar('userEmail');
		$userPassword = $this->request->getVar('userPassword');
		$userName = $this->request->getVar('userName');
		$firstName = $this->request->getVar('firstName');
		$lastName = $this->request->getVar('lastName');

		if($this->aauth->createUser($userEmail, $userPassword, $userName)){
			$data['success'] = "The account was successfuly created";
		}else{
			$data['success'] = "The account was not created";
		}

		$this->aauth->login($userEmail,$userPassword);

		$tableData = [
			'aauthID' => $this->aauth->getUserId(),
			'firstName'  => $firstName,
			'lastName'  => $lastName
		];

		$this->aauth->logout();

		try {
			$this->userModel->insert($tableData);
		}
		catch (\ReflectionException $e) {

		}

		//$this->db->table('lms_teacher')->insert($tableData);



		return view('loginTeacher', $data);
	}
}
