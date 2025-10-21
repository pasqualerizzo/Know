<html>
<head>
    <meta charset="UTF-8">
    <title>Autorizzazione RID/Bollettino</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

<h1 style="text-align: center">AUTORIZZAZIONE RID/BOLLETTINO</h1>
<br>
<br>

<div style="display: flex; justify-content: center; margin-top: 8px; font-size: 10vh; width: 100%;" id="contenitore">
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td id="colonna1" style="border: 4px solid purple; margin-top: 40px; font-size: 10vw;"></td>
            <td id="colonna2" style="border: 4px solid orange; margin-top: 40px; font-size: 10vw;"></td>
        </tr>
        <tr>
            <td id="colonna3" style="border: 4px solid purple; margin-top: 40px; font-size: 10vw;"></td>
            <td id="colonna4" style="border: 4px solid orange; margin-top: 40px; font-size: 10vw;"></td>
        </tr>
        <tr>
            <td id="colonna5" style="border: 4px solid purple; margin-top: 40px; font-size: 10vw;"></td>
            <td id="colonna6" style="border: 4px solid orange; margin-top: 40px; font-size: 10vw;"></td>
        </tr>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    function iniziaLav() {
        $("#colonna1").html('');
        $("#colonna2").html('');

        // eni plenitude
        $.ajax({
            url: "creaTabellaRid913.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna1").append(valore_risposta);
            }
        });

        // eni plenitude
        $.ajax({
            url: "creaTabellaBoll1216.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna2").append(valore_risposta);
            }
        });

        $("#colonna3").html('');
        $("#colonna4").html('');

        // eni VIVIGAS
        $.ajax({
            url: "creaTabellaRid1316.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna3").append(valore_risposta);
            }
        });

        // eni VIVIGAS
        $.ajax({
            url: "creaTabellaBoll1619.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna4").append(valore_risposta);
            }
        });

        $("#colonna5").html('');
        $("#colonna6").html('');

        // VODAFONE
        $.ajax({
            url: "creaTabellaRid1619.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna5").append(valore_risposta);
            }
        });

        // VODAFONE
        $.ajax({
            url: "creaTabellaBoll1920.php",
            method: "GET",
            success: function(valore_risposta) {
                console.log(valore_risposta);
                $("#colonna6").append(valore_risposta);
            }
        });
    }

    // Esegui all'avvio
    iniziaLav();

    // Aggiorna ogni 4 secondi
    setInterval(function() {
        iniziaLav();
        console.log('Aggiorno');
    }, 4000);

    // Ricarica la pagina ogni 10 secondi
    setInterval(function() {
        location.reload();
    }, 4000);
</script>

</body>
</html>