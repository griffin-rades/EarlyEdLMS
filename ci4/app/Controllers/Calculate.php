<?php namespace App\Controllers;

class Calculate extends BaseController
{
	//get session data for the user when loaded/reloaded.
	public function getSession(){
		$currentTotal = $this->session->get("currentScreen");

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	// handle every number clicked, add info to user session.
	public function number($number){
		$currentScreen += $number;
		$this->session->set("currentScreen", $currentScreen);
	}
	//hande when the add button is clicked
	public function add($numToAdd, $currentTotal){
		$currentTotal += $numToAdd;

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	//hanle when the subtraction button is clicked
	public function subtract($numToSubtract, $currentTotal, $first = false){
		if($first){
			$currentTotal = $numToSubtract;
		}else{
			$currentTotal -= $numToSubtract;
		}
		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	//handle when the mutliply button is clicked
	public function multiply($number, $currentTotal){
		$currentTotal *= $number;

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	//hadle when the divide button is clicked
	public function divide($number, $currentTotal){
		if($currentTotal != "0"){
			$currentTotal = $currentTotal / $number;
		}else{
			$currentTotal = $number;
		}

		return $this->response->setJSON(array("answer"=>$currentTotal));
	}
	//handle when the equal button is clicked
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
		}elseif($prevOp == "none"){
			$total = $total;
		}

		return $this->response->setJSON(array("answer"=>$total));
	}
	//handle when the precent button is clicked
	public function percent($number){
		$number = $number * .01;

		return $this->response->setJSON(array("answer"=>$number));
	}
	//handle when the clear button is clicked
	public function clear(){
		$this->session->set('currentScreen', "");
	}
}
