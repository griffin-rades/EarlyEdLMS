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

	public function add($numToAdd, $currentTotal){
		$currentTotal += $numToAdd;

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	
	public function subtract($numToSubtract, $currentTotal, $first = false){
		if($first){
			$currentTotal = $numToSubtract;
		}else{
			$currentTotal -= $numToSubtract;
		}
		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	
	public function multiply($number, $currentTotal){
		$currentTotal *= $number;
		
		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	
	public function divide(){

	}
	
	public function equal($total, $prevOp, $number){
		if($prevOp == "add"){
			$total += $number;
		}elseif($prevOp == "subtract"){
			$total -= $number;
		}elseif($prevOp == "multiply"){
			$total *= $number;
		}

		return $this->response->setJSON(array("answer"=>$total));
	}
	public function clear(){
		//$this->session->set('current', $currentScreen);
	}
	//--------------------------------------------------------------------
}
