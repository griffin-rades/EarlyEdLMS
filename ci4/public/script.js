var anotherNumber = true;
window.addEventListener("DOMContentLoaded", loaded);
var previousOP = "";
var $currentTotal = 0;

function loaded(){
  const keys = document.querySelector('.calcButtons');
  var display = document.getElementById("calcDisplay");

  keys.addEventListener("click", function(e){
      
      if(e.target.classList.contains("number")){
        doNumber(e.target.value);
      }
      
      if(e.target.value = "+"){
          $.ajax({
            type: "POST",
            url: "calculate/add/" + display.innerHTML + "/" + $currentTotal,
            //data: {number: display.innerHTML, total: $currentTotal},
            dataType:'JSON',
            success: function(response){
                $currentTotal += response.answer;
                display.innerHTML = $currentTotal;
            }
          });
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

    if(e.target.value == "clear"){
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
}
