window.addEventListener("DOMContentLoaded", loaded);
var currentTotal = 0;  //keep track of calculation total
var first = true; //keep track to if it is the first time subtract button is clicked
var previousOp = "none"; //keep track of the previous operation for when the equal button is clicked
var numNext = true; //keep track of when to append or start a new number input

function loaded(){
  //retrievs the current users data on load/reload.
  $.ajax({
    type: "POST",
    url: "calculate/getSession",
    dataType:'JSON',
    success: function(response){
      if(response.answer == ""){
        display.innerHTML = 0;
      }else{
        currentTotal = response.answer;
        display.innerHTML = currentTotal;
      }
    }
  });

  //connect the variables and constants to their calculator button or the display
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
  const negateButton = document.getElementById("negate");

  //loop through the numberButton and give them and event listener
  for(var i = 0; i < numberButtons.length; i++){
    numberButtons[i].addEventListener("click", function(e){
      if(numNext){
        if(display.innerHTML == "0"){
          display.innerHTML = e.target.value;
        }else{
          display.innerHTML += e.target.value;
        }
      }else{
        display.innerHTML = e.target.value;
        numNext = true;
      }
      //call server to update the value of the current display. Used for session data
      $.ajax({
        type: "POST",
        url: "calculate/number/" + display.innerHTML,
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = currentTotal;
        }
      });
    });
  }
  //add operation button clicked. Call to the server calculate/add/'numberOnScreen'/'currentTotal'
  //response: set currentTotal to new total, change screen to operation, set previousOp, and set numNext to false.
  addButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/add/" + display.innerHTML + "/" + currentTotal,
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = "+";
        previousOp = "add";
        numNext = false;
      }
    });
  });
  //subtract operation button clicked. Call to the server calculate/subtract/'numberOnScreen'/'currentTotal'/'ifFirstTimeSubtractClicked'
  //response: set currentTotal to new total, change screen to operation, set previousOp, and set numNext to false.
  subtractButton.addEventListener("click", function(){
    //if it is the first time that subtract was clicked send that information to the server.
    if(first){
      $.ajax({
        type: "POST",
        url: "calculate/subtract/" + display.innerHTML + "/" + currentTotal + "/" + first,
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = "-";
          previousOp = "subtract";
          numNext = false;
        }
      });
      first = false; //subtract has been clicked
    }else{
      $.ajax({
        type: "POST",
        url: "calculate/subtract/" + display.innerHTML + "/" + currentTotal,
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = "-";
          previousOp = "subtract";
          numNext = false;
        }
      });
    }
  });
  //multiply operation button clicked. Call to the server calculate/multiply/'numberOnScreen'/'currentTotal'
  //response: set currentTotal to new total, change screen to operation, set previousOp, and set numNext to false.
  mutlButton.addEventListener("click", function(){
    if(currentTotal == 0){
      $.ajax({
        type: "POST",
        url: "calculate/multiply/" + display.innerHTML + "/" + 1,
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = "*";
          previousOp = "multiply";
          numNext = false;
        }
      });
    }else{
      $.ajax({
        type: "POST",
        url: "calculate/multiply/" + display.innerHTML + "/" + currentTotal,
        dataType:'JSON',
        success: function(response){
          currentTotal = response.answer;
          display.innerHTML = "*";
          previousOp = "multiply";
          numNext = false;
        }
      });
    }
  });
  //divide operation button clicked. Call to the server calculate/divide/'numberOnScreen'/'currentTotal'
  //response: set currentTotal to new total, change screen to operation, set previousOp, and set numNext to false.
  divideButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/divide/" + display.innerHTML + "/" + currentTotal,
      dataType:'JSON',
      success: function(response){
        currentTotal = response.answer;
        display.innerHTML = "/";
        previousOp = "divide";
        numNext = false;
      }
    });
  });
  //percent operation button clicked. Call to the server calculate/percent/'numberOnScreen'
  //response: set currentTotal to 0, set display to response, and set numNext to false.
  percentButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/percent/" + display.innerHTML,
      dataType:'JSON',
      success: function(response){
        currentTotal = 0;
        display.innerHTML = response.answer;;
        numNext = false;
      }
    });
  });
  //decimal operation button clicked.
  decimalButton.addEventListener("click", function(){
    if(!display.innerHTML.includes(".")){ //if display does not have a decimal add one to the end of the display
      display.innerHTML += ".";
    }
  });
  //negate operation button clicked.
  negateButton.addEventListener("click", function(){
    if(!display.innerHTML.includes("-") && display.innerHTML != 0){ //if display is not 0 and it is not alread negative add negative sign to front
      display.innerHTML = "-" + display.innerHTML;
    }else if(display.innerHTML.includes("-")){ //if already is negative remove the negative
      display.innerHTML = display.innerHTML.substring(1);
    }
  });
  //equal operation button clicked. Call to the server calculate/equal/'numberOnScreen'/previousOperation'/'currentTotal'
  //response: set currentTotal to 0, change screen to response, set previousOp to none, and set numNext to false.
  equalButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/equal/" + currentTotal + "/" + previousOp + "/" + display.innerHTML,
      dataType:'JSON',
      success: function(response){
        display.innerHTML = response.answer;
        currentTotal = 0;
        previousOp = "";
        numNext = false;
      }
    });
  });
  //clear operation button clicked. Call to the server calculate/clear  used to remove session data.
  //set currentTotal to 0, change screen to 0, and set numNext to true.
  clearButton.addEventListener("click", function(){
    $.ajax({
      type: "POST",
      url: "calculate/clear",
      dataType:'JSON',
    });
    numNext = true;
    currentTotal = 0;
    display.innerHTML = 0;
    first = true;
  });
}
