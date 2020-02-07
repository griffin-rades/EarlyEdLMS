<?php namespace App\Controllers;

class Calculate extends BaseController
{
	// public function index()
	// {
	// 	return view('calculator');
	// }
	public function number($number){
		$currentScreen += $number;
		$this->session->set("currentScreen", $currentScreen);
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

	public function divide($number, $currentTotal){
		if($currentTotal != "0"){
			$currentTotal = $currentTotal / $number;
		}else{
			$currentTotal = $number;
		}

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}

	public function equal($total, $prevOp, $number){
		if($prevOp == "add"){
			$total += $number;
		}elseif($prevOp == "subtract"){
			$total -= $number;
		}elseif($prevOp == "multiply"){
			$total *= $number;
		}elseif($prevOp == "divide"){
			if($total != 0){
					$total = $total / $number;
			}
		}

		return $this->response->setJSON(array("answer"=>$total));
	}
	
	public function percent($number){
		$number = $number * .01;

		return $this->response->setJSON(array("answer"=>$number));
	}
	public function clear(){
		//$this->session->set('current', $currentScreen);
	}
	//--------------------------------------------------------------------
}
