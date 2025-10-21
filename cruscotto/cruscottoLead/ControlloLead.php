<?php
$localIP = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['SERVER_NAME'];

?>

<html>
    <head>
        <title>Controllo Lead Siscall2</title>
        <meta http-equiv="refresh" content="10">
        <style>
            h2{
                margin: 0.5px;
            }
            .riquadro { 
                
                text-align: center;
                border-radius: 45px;
                margin: 5px;
                box-shadow:  0 5px 15px rgba(0,0,0,0.3),
                    inset 0 0 10px #000000;
                 text-shadow: rgb(122, 122, 122) 2px 2px 2px;
            }
            .titolo{
                
                color: #000000;
                text-shadow: 2px 2px 0 #bcbcbc, 4px 4px 0 #9c9c9c;
            }
            .testa{
                display: flex;
                align-content: center;
                justify-content: center;
                background: #edde9c;
                height: 10%;
            }
            .corpo{
                display:flex;
                 flex-direction: column;
                
                justify-content: flex-start;
                
                height:80%;
                background: #edde9c;                
            }
            </style>
    </head>
    <body onload="ricercaLead();">
        <header  class="testa"  >
            <h1 class="titolo" >Controllo Stato Lead</h1>
        </header>
        <div class="corpo" >
            <div class="riquadro"   id=genericoDiv>
                <h2 style="font-size: 200%">Lista Generico</h2>
                <label style="font-size: 150%;text-align: center" id="titoloGenerico">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="genericoNew"></a>
            </div>
            <br>
            <div class="riquadro"   id=amazonDiv>
                <h2 style="font-size: 200%">Form</h2>
                <label id="titoloAmazon" style="font-size: 150%;text-align: center">Lead in NEW: </label>
                <a id="amazonNew" style="font-size: 150%;text-align: center"></a>
            </div>
            <br>
            <div class="riquadro"  id=energy2000Div>
                <h2 style="font-size: 200%">Liste 2000 Energy</h2>
                <label id="titoloenergy2000" style="font-size: 150%;text-align: center">Lead in NEW: </label>
                <a id="energy2000" style="font-size: 150%;text-align: center"></a>
            </div>
            <br>
            <div class="riquadro"  id=energy2099Div>
                <h2 style="font-size: 200%">Code Abbandonate</h2>
                <label id="titoloEnergy2099" style="font-size: 150%;text-align: center">Lead in NEW: </label>
                <a id="energy2099" style="font-size: 150%;text-align: center"></a>
            </div>
            <br>
            <!--
            <div class="riquadro"  id=telefonicoDiv>
                <h2 style="font-size: 200%" >Lista Telefonico</h2>
                <label style="font-size: 150%;text-align: center;" id="titoTelefonico">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="telefonicoNew"></a>
            </div>
            
            <br>
            <div class="riquadro"  id=telefonico2000Div>
                <h2 style="font-size: 200%" >Lista 2000 TELCO</h2>
                <label style="font-size: 150%;text-align: center" id="titoloTelefonico2000">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="telefonicoNew2000"></a>
            </div>
              <br>
            <div class="riquadro"  id=telefonico2098Div>
                <h2 style="font-size: 200%" >Lista 2098 TELCO</h2>
                <label style="font-size: 150%;text-align: center" id="titoloTelefonico2098">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="telefonicoNew2098"></a>
            </div>
              <br>
            -->
            <div class="riquadro"  id=metaformDiv>
                <h2 style="font-size: 200%" >Yesterday</h2>
                <label style="font-size: 150%;text-align: center" id="titolometaform">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="metaform"></a>
            </div>
            
            <div class="riquadro"  id=telco2599Div>
                <h2 style="font-size: 200%" >Telco 2599</h2>
                <label style="font-size: 150%;text-align: center" id="titoloTelco2599">Lead in NEW:</label>
                <a style="font-size: 150%;text-align: center;margin: 10 px" id="telco2599"></a>
            </div>
              
        </div>

    </body>
    <script type="text/javascript">
        function ricercaLead() {
            var amazonNew = document.getElementById("amazonNew");
            var titoloAmazon = document.getElementById("titoloAmazon");
            var amazonDiv = document.getElementById("amazonDiv");
            var genericoNew = document.getElementById("genericoNew");
            var titoloGenerico = document.getElementById("titoloGenerico");
            var genericoDiv = document.getElementById("genericoDiv");
            //var telefonicoNew = document.getElementById("telefonicoNew");
            //var titoloTelefonico = document.getElementById("titoTelefonico");
            //var telefonicoDiv = document.getElementById("telefonicoDiv");
            var energy2000 = document.getElementById("energy2000");
            var titoloenergy2000 = document.getElementById("titoloenergy2000");
            var energy2000Div = document.getElementById("energy2000Div");
            //var telefonicoNew2000 = document.getElementById("telefonicoNew2000");
            //var titoloTelefonico2000 = document.getElementById("titoloTelefonico2000");
            //var telefonico2000Div = document.getElementById("telefonico2000Div");
            var energy2099 = document.getElementById("energy2099");
            var titoloenergy2099 = document.getElementById("titoloenergy2099");
            var energy2099Div = document.getElementById("energy2099Div");
            
            var metaform = document.getElementById("metaform");
            var titolometaform = document.getElementById("titolometaform");
            var metaformdiv = document.getElementById("metaformDiv");
            
            var telco2599 = document.getElementById("telco2599");
            var titoloTelco2599 = document.getElementById("titoloTelco2599");
            var telco2599div = document.getElementById("telco2599Div");
            
            var data = new FormData();
            data.append("lista", '11113');
            fetch("infoLead.php", {
                method: "POST",
                body: data,
                redirect: 'manual'
            }).then(response => response.json())
                    .then((response) => {
                        console.log(response);
                       var conteggioNewAmazon = response[0].NEW;
                        if (conteggioNewAmazon > 0) {
                            amazonNew.innerHTML = (conteggioNewAmazon);
                            amazonDiv.style.backgroundColor = "green";
                            amazonDiv.style.color = "white";
                        } else {
                            amazonNew.innerHTML = (0);
                            amazonDiv.style.backgroundColor = "crimson";
                            amazonDiv.style.color = "white";
                        }
                        var conteggioNewGenerico = response[1].NEW;
                        if (conteggioNewGenerico > 0) {
                            genericoNew.innerHTML = (conteggioNewGenerico);
                            genericoDiv.style.backgroundColor = "green";
                            genericoDiv.style.color = "white";
                        } else {
                            genericoNew.innerHTML = (0);
                            genericoDiv.style.backgroundColor = "crimson";
                            genericoDiv.style.color = "white";
                        }
//                        var conteggioNewTelefonico = response[2].NEW;
//                        if (conteggioNewTelefonico > 0) {
//                            telefonicoNew.innerHTML = (conteggioNewTelefonico);
//                            telefonicoDiv.style.backgroundColor = "green";
//                            telefonicoDiv.style.color = "white";
//                        } else {
//                            telefonicoNew.innerHTML = (0);
//                            telefonicoDiv.style.backgroundColor = "crimson";
//                            telefonicoDiv.style.color = "white";
//                        }
                        var conteggioNewenergy2000 = response[3].NEW;
                        if (conteggioNewenergy2000 > 0) {
                            energy2000.innerHTML = (conteggioNewenergy2000);
                            energy2000Div.style.backgroundColor = "green";
                            energy2000Div.style.color = "white";
                        } else {
                            energy2000.innerHTML = (0);
                            energy2000Div.style.backgroundColor = "crimson";
                            energy2000Div.style.color = "white";
                        }
//                        var conteggioNew2098Telco=response[4].NEW;
//                        if (conteggioNew2098Telco > 0) {
//                            telefonicoNew2098.innerHTML = (conteggioNew2098Telco);
//                            telefonico2098Div.style.backgroundColor = "green";
//                            telefonico2098Div.style.color = "white";
//                        } else {
//                            telefonicoNew2098.innerHTML = (0);
//                            telefonico2098Div.style.backgroundColor = "crimson";
//                            telefonico2098Div.style.color = "white";
//                        }
//                        var conteggioNew2000Telco=response[5].NEW;
//                        if (conteggioNew2000Telco > 0) {
//                            telefonicoNew2000.innerHTML = (conteggioNew2000Telco);
//                            telefonico2000Div.style.backgroundColor = "green";
//                            telefonico2000Div.style.color = "white";
//                        } else {
//                            telefonicoNew2000.innerHTML = (0);
//                            telefonico2000Div.style.backgroundColor = "crimson";
//                            telefonico2000Div.style.color = "white";
//                        }
                        
                        var conteggioNewEnergy2099=response[6].NEW;
                        if (conteggioNewEnergy2099 > 0) {
                            energy2099.innerHTML = (conteggioNewEnergy2099);
                            energy2099Div.style.backgroundColor = "green";
                            energy2099Div.style.color = "white";
                        } else {
                            energy2099.innerHTML = (0);
                            energy2099Div.style.backgroundColor = "crimson";
                            energy2099Div.style.color = "white";
                        }
                        
                        
                        var conteggioNewmetaform=response[7].NEW;
                        if (conteggioNewmetaform > 0) {
                            metaform.innerHTML = (conteggioNewmetaform);
                            metaformDiv.style.backgroundColor = "green";
                            metaformDiv.style.color = "white";
                        } else {
                            metaform.innerHTML = (0);
                            metaformDiv.style.backgroundColor = "crimson";
                            metaformDiv.style.color = "white";
                        }
                        
                        var conteggioNewTelco2599=response[8].NEW;
                        if (conteggioNewTelco2599 > 0) {
                            telco2599.innerHTML = (conteggioNewTelco2599);
                            telco2599Div.style.backgroundColor = "green";
                            telco2599Div.style.color = "white";
                        } else {
                            telco2599.innerHTML = (0);
                            telco2599Div.style.backgroundColor = "crimson";
                            telco2599Div.style.color = "white";
                        }
                    }
                    )
            
                    .catch(err => console.log(err));
        }



    </script>
</html>