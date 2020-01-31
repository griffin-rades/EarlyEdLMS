<? php

if($_REQUEST["op"] != "="){
  $firstNum = $_REQUEST["num"];
  $operation = $_REQUEST["op"];
}else{
  $secondNum = $_REQUEST["num"];
}


echo $firstNum;
?>
