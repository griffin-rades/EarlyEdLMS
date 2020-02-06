<?php namespace App\Controllers;

class Calculate extends BaseController
{
	// public function index()
	// {
	// 	return view('calculator');
	// }
	public function number($number){
		//$currentScreen += $number;
		//$this->session->set('current', $currentScreen);
	}

	public function add($numToAdd, $currentTotal = 0){
		$currentTotal += $numToAdd;

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	public function subtract(){

	}
	public function multiply(){

	}
	public function divide(){

	}
	public function equal($total, $prevOp, $number){
		if($prevOp == "+"){
			$total += $number;
		}

		return $this->response->setJSON(array("answer"=>$total));
	}
	public function clear(){
		//$this->session->set('current', $currentScreen);
	}
	//--------------------------------------------------------------------
}
