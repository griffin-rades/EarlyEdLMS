<?php

$currentValue = $_POST["current"];
$num = $_POST["number"];
$op = $_POST["operation"];
$prevOP = $_POST["previous"];

if($currentValue == ""){
  $first = true;
}else{
  $first = false;
}

if($op == "="){
  $currentValue = doMath($prevOP, $currentValue, $num, $first);
}else{
  $currentValue = doMath($op, $currentValue, $num, $first);
}

function doMath($operator, $cval, $number, $f){
  if($operator == "+"){
    if($f){
      $cval = $number;
      echo json_encode(array("answer"=>$cval, "oper"=>$operator));
    }else{
      $cval = $cval + $number;
      echo json_encode(array("answer"=>$cval, "oper"=>$operator));
    }
  }elseif($operator == "-"){
    if($f){
      $cval = $number;
      echo json_encode(array("answer"=>$cval));
    }else{
      $cval = $cval - $number;
      echo json_encode(array("answer"=>$cval));
    }
  }elseif($operator == "*"){
    if($f){
      $cval = $number;
      echo json_encode(array("answer"=>$cval, "oper"=>$operator));
    }else{
      $cval = $cval * $number;
      echo json_encode(array("answer"=>$cval, "oper"=>$operator));
    }
  }elseif($operator == "/"){
    if($f){
      $cval = $number;
      echo json_encode(array("answer"=>$cval));
    }else{
      $cval = $cval / $number;
      echo json_encode(array("answer"=>$cval));
    }
  }elseif($operator == "%"){
    if($f){
      $cval = $number / 100;
      echo json_encode(array("answer"=>$cval));
    }else{
      $cval = $cval / 100;
      echo json_encode(array("answer"=>$cval));
    }
  }
  return $cval;
}

?>
