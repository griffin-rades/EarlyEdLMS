<?php namespace App\Controllers;

class Login extends BaseController{

  function loginUser(){

    $data = array();

    $data['email'] = $this->request->getVar("userEmail");
    $data['password'] = $this->request->getVar("userPassword");

    if($this->aauth->login($this->request->getVar('userEmail'), $this->request->getVar('userPassword'), $this->request->getVar('rememberMe'))){
    	return view('teacherHome', $data);
	}else{
    	return view('loginTeacher');
	}
  }
}
