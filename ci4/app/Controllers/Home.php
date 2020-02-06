<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		//$this->session->set('someval', 'someval');
		
		return view('calculator');
	}

	//--------------------------------------------------------------------

}
