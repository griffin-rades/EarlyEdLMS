<?php namespace App\Controllers;

class Calculate extends BaseController
{	
	// public function index()
	// {
	// 	return view('calculator');
	// }
	
	public function add($numToAdd, $currentTotal = 0){
		// $numToAdd = $request->getPost("number");
		// $currentTotal = $request->getPost("total");
		
		$currentTotal += $numToAdd;
		
		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	public function subtract(){
		
	}
	public function multiply(){
		
	}
	public function divide(){
		
	}
	//--------------------------------------------------------------------
}
