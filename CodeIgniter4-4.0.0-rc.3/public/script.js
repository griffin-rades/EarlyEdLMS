var anotherNumber = true;
window.addEventListener("DOMContentLoaded", loaded);
var currentTotal = "";
var previousOP = "";

function loaded(){
  const keys = document.querySelector('.calcButtons');
  var display = document.getElementById("calcDisplay");

  keys.addEventListener("click", function(e){

    if(e.target.classList.contains("operator")){
      anotherNumber = false;

      $.ajax({
        type: "POST",
        url: "calculation.php",
        data: {operation: e.target.value, number: display.innerHTML, current: currentTotal, previous: previousOP},
        dataType:'JSON',
        success: function(response){
            previousOP = response.oper;
            currentTotal= response.answer;
            display.innerHTML = currentTotal;
        }
      });
      
      if(previousOP == "="){
        currentTotal = "";
      }

      doOperation(e.target.value);
    }

    if(e.target.classList.contains("negate")){
      if(display.innerHTML.includes("-")){
        display.innerHTML.replace("-", "");
      }else{
        if(display.innerHTML != "0"){
            display.innerHTML = "-" + display.innerHTML;
        }
      }
    }

    if(e.target.value == "."){
      if(display.innerHTML.includes(".")){

      }else{
        display.innerHTML += ".";
      }
    }

    if(e.target.classList.contains("number")){
      doNumber(e.target.value);
    }

    if(e.target.value == "clear"){
      currentTotal = "";
      previousOP = "";

      display.innerHTML = "0";
    }
  });

  function doNumber(digit){
    if(anotherNumber){
      if(display.innerHTML == "0"){
        display.innerHTML = digit;
      }else{
        display.innerHTML += digit;
      }
    }else{
      display.innerHTML = digit;
      anotherNumber = true;
    }
  }
  
  function doOperation(operator){
    display.innerHTML = operator;
  }
}
