function NumberOnly(evt){
          var charCode = (evt.which) ? evt.which : event.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
       
    function showhideHostinput(){
        
        var numberofhost = document.getElementById("numberofhost").value;
        var i=1;
        while(i<=numberofhost){
            document.getElementById('host'+i).setAttribute('style','');
            i++;
        }
        while(i>numberofhost && i <=9){
            document.getElementById('host'+i).setAttribute('style','visibility:hidden;line-height:0;height:0px;');
            i++;
        }
    }
    