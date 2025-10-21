<html>
<header style=" display: flex;align-content: center;justify-content: center;" >



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">


</header>

<body    font-family: Arial, sans-serif; >


<h1 style="text-align: center">Gestione Code Backoffice Giornaliero</h1>
<br>
<br>

<div style="display: flex; 
        justify-content: center; 
        margin-top: 8px; font-size: 10vh;width: 100%" ; id="contenitore">

<table border-collapse: collapse;
        width: 100%;>
<tr><td id="colonna1" style="border: 4px solid purple; margin-top: 40px;  font-size: 10vw;"></td><td id="colonna2"  style="border: 4px solid purple; margin-top: 40px; font-size: 10vw;"></td><td id="colonna9"  style="border: 4px solid purple; margin-top: 40px; font-size: 10vw;"></td></tr>
<tr><td id="colonna3"  style="border: 4px solid black; margin-top: 40px; font-size: 10vw;"></td><td id="colonna4"  style="border: 4px solid black; margin-top: 40px; font-size: 10vw;"></td><td id="colonna10"  style="border: 4px solid black; margin-top: 40px; font-size: 10vw;"></td></tr>
<tr><td id="colonna5"  style="border: 4px solid red; font-size: 10vw;"></td><td id="colonna6"  style="border: 4px solid red; margin-top: 40px; font-size: 10vw;"></td><td id="colonna11"  style="border: 4px solid red; margin-top: 40px; font-size: 10vw;"></td></tr>
<tr><td id="colonna7"  style="border: 4px solid blue; font-size: 10vw;"></td><td id="colonna8"  style="border: 4px solid blue; margin-top: 40px; font-size: 10vw;"></td><td id="colonna12"  style="border: 4px solid blue; margin-top: 40px; font-size: 10vw;"></td></tr>

</table>

</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<script>

iniziaLav();


    // set timeout per aggiornare la pagina
    setInterval(function () {

        iniziaLav();
        /*

        $("#colonna1").html('');
        $("#colonna2").html('');

        // eni plenitude
    
        $.ajax({
            url: "plenitude_som.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna1").append(valore_risposta);

            }
        });


        // enel
        $.ajax({
            url: "plenitude_det.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna2").append(valore_risposta);
        
            }
        });

        */

        console.log('Aggiorno');

    }, 4000);


    setInterval(function () {
        location.reload();
    },10000);




    function iniziaLav(){

        $("#colonna1").html('');
        $("#colonna2").html('');
        $("#colonna9").html('');

        // eni plenitude
    
        $.ajax({
            url: "plenitude_bkl.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna1").append(valore_risposta);

            }
        });


        // eni plenitude
        $.ajax({
            url: "plenitude_ok.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna2").append(valore_risposta);
        
            }
        });

              // eni plenitude
              $.ajax({
            url: "plenitude_prc.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna9").append(valore_risposta);
        
            }
        });



        $("#colonna3").html('');
        $("#colonna4").html('');
        $("#colonna10").html('');

        // eni VIVIGAS
    
        $.ajax({
            url: "vivigas_bkl.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna3").append(valore_risposta);

            }
        });


         // eni VIVIGAS
        $.ajax({
            url: "vivigas_ok.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna4").append(valore_risposta);
        
            }
        });
             // eni VIVIGAS

            $.ajax({
            url: "vivigas_prc.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna10").append(valore_risposta);
        
            }
        });


        
        $("#colonna5").html('');
        $("#colonna6").html('');
        $("#colonna11").html('');

        // VODAFONE
    
        $.ajax({
            url: "vodafone_bkl.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna5").append(valore_risposta);

            }

            // VODAFONE
        });
        $.ajax({
            url: "vodafone_ok.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna6").append(valore_risposta);

            }

            // VODAFONE
        });

        $.ajax({
            url: "vodafone_prc.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna11").append(valore_risposta);

            }
        });

        $("#colonna7").html('');
        $("#colonna8").html('');
        $("#colonna12").html('');

        // IREN
   
        $.ajax({
            url: "iren_bkl.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
                
                $("#colonna7").append(valore_risposta);

            }
        });


        // IREN
        $.ajax({
            url: "iren_ok.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna8").append(valore_risposta);
        
            }
        });


        // IREN

        $.ajax({
            url: "iren_prc.php",
            //url:"https://www.hydrasolutions.it/risorse/nova/Json/crea_json.php",
            method: "GET",
            success: function (valore_risposta) {
                console.log(valore_risposta);
            
                $("#colonna12").append(valore_risposta);
        
            }
        });


    }
    
</script>

</html>


