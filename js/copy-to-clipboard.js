window.addEventListener('DOMContentLoaded', function () {



    function myFunction() {
        var copyText = document.getElementsByClassName("copy-to-clipboard");
        for (let i = 0; i < copyText.length; i++) {

          copyText[i].addEventListener('click', function(e){
            navigator.clipboard.writeText(e.target.innerText);
          });
            
        }
    
      }

      myFunction();
      
     
});