<?php namespace App\Controllers;

use App\Libraries\Aauth;

class Login extends BaseController{

  function loginUser(){

    $data = array();
    $this->aauth = new Aauth();
    $data['aauth'] = $this->aauth;
    $data['email'] = $this->request->getVar("userEmail");
    $data['password'] = $this->request->getVar("userPassword");

    if($this->aauth->login($this->request->getVar('userEmail'), $this->request->getVar('userPassword'), $this->request->getVar('rememberMe'))){
    	return view('teacherHome', $data);
	}else{
    	$data['error'] = true;
		$data['errors'] = $this->aauth->printErrors('<br />', true);
    	return view('loginTeacher', $data);
	}
  }
}
