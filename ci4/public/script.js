window.addEventListener("DOMContentLoaded", loaded);
var currentTotal = 0;
previousOp = "";
numNext = true;

function loaded(){
  var display = document.getElementById("calcDisplay");
  var numberButtons = document.getElementsByClassName("number");
  const addButton = document.getElementById("addition");
  const subtractButton = document.getElementById("subtract");
  const mutlButton = document.getElementById("multiply");
  const divideButton = document.getElementById("divide");
  const equalButton = document.getElementById("equal");
  const percentButton = document.getElementById("percent");
  const decimalButton = document.getElementById("decimal");
  const clearButton = document.getElementById("clear");

  for(var i = 0; i < numberButtons.length; i++){
    numberButtons[i].addEventListener("click", function(e){
      $.ajax({
        type: "POST",
        url: "calculate/number/" + e.target.value,
        //data: {number: display.innerHTML, total: $currentTotal},
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = currentTotal;
          console.log(currentTotal);
        }
      });

      if(numNext){
        if(display.innerHTML == "0"){
          display.innerHTML = e.target.value;
        }else{
          display.innerHTML += e.target.value;
        }
      }else{
        display.innerHTML = e.target.value;
      }
    });
  }

  addButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      //data: {number: display.innerHTML, total: $currentTotal},
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = "+";
        previousOp = "+";
        numNext = false;
      }
    });
  });

  mutlButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      //data: {number: display.innerHTML, total: $currentTotal},
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
        console.log(currentTotal);
      }
    });
  });

  divideButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      //data: {number: display.innerHTML, total: $currentTotal},
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
        console.log(currentTotal);
      }
    });
  });

  percentButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      //data: {number: display.innerHTML, total: $currentTotal},
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
        console.log(currentTotal);
      }
    });
  });

  decimalButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      //data: {number: display.innerHTML, total: $currentTotal},
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
        console.log(currentTotal);
      }
    });
  });

  equalButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/equal/" + currentTotal + "/" + previousOp + "/" + display.innerHTML,
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
        numNext = false;
        currentTotal = 0;
        previousOp = "";
      }
    });
  });

  clearButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/clear",
      dataType:'JSON',
      success: function(response){
        console.log("Cleared");
      }
    });
    numNext = true;
    currentTotal = 0;
    display.innerHTML = 0;
  });
}
