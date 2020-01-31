var total;
var anotherNumber = true;
window.addEventListener("DOMContentLoaded", loaded);

function loaded(){
  const keys = document.querySelector('.calcButtons');
  var display = document.getElementById("calcDisplay");

  keys.addEventListener("click", function(e){

    if(e.target.classList.contains("operator")){
      anotherNumber = false;
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "calculation.php?num=" + display.innerHTML + "op=" + e.target.value, true);
      xhr.send();
    }
    if(e.target.classList.contains("number")){
      doNumber(e.target.value);
    }
    if(e.target.value == "clear"){
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
