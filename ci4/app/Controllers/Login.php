<?php namespace App\Controllers;

class Login extends BaseController{

  function loginUser(){

    $data = array();

    $data['email'] = $this->request->getVar("userEmail");
    $data['password'] = $this->request->getVar("userPassword");



    return view("teacherHome", $data);
  }
}
