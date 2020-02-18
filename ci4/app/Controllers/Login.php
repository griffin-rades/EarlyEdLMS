<?php namespace App\Controllers;

class Login extends BaseController{

  function loginUser(){
    helper('aauth');
    
    $email = $_POST["userEmail"];
    $password = $_POST["userPassword"];


    echo view("teacherHome");
  }
}
