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
		$firstName = $this->request->getVar('firstName');
		$lastName = $this->request->getVar('lastName');

		if($this->aauth->createUser($userEmail, $userPassword, $userName)){
			$data['success'] = "The account was successfuly created";
		}else{
			$data['success'] = "The account was not created";
		}

		$tableData = [
			'teacherID' => $this->aauth->getUserID(),
			'firstName'  => $firstName,
			'lastName'  => $lastName
		];

		$this->db->table('lms_teacher')->insert($tableData);



		return view('loginTeacher', $data);
	}
}
