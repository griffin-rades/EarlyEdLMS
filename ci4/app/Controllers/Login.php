<?php namespace App\Controllers;

class Login extends BaseController{
    
    function loginUser(){
        helper('aauth');
        
        return view("home");
    }
}

