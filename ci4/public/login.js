window.addEventListener("DOMContentLoaded", loaded);


function loaded(){
    const form = document.getElementById("loginForm");
    var userEmail = document.getElementById("email");
    var userPassword = document.getElementById("password");
    
    form.addEventListener("submit", submitLogin);
    
    function submitLogin(event){
        $.ajax({
          type: "POST",
          url: "login/loginUser",
          data: userEmail, userPassword,
          dataType:'JSON',
          success: function(response){
              console.log(response.userEmail);
          }
        });
    }
}