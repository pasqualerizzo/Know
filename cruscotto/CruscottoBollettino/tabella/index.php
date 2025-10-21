<html>
<head>
    <meta charset="UTF-8">
    <title>Autorizzazione RID/Bollettino</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="icon" href="../../images/logo-metrics.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .table-cell {
            height: 200px;
            vertical-align: middle;
            text-align: center;
            font-size: 36px;
            font-weight: bold;
        }
        .purple-border {
            border: 4px solid purple;
        }
        .orange-border {
            border: 4px solid orange;
        }
        .loading {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>

<h1 style="text-align: center">AUTORIZZAZIONE RID/BOLLETTINO</h1>
<br>
<br>

<div style="display: flex; justify-content: center; margin-top: 8px; width: 100%;" id="contenitore">
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td id="colonna1" class="table-cell purple-border loading">Caricamento...</td>
            <td id="colonna2" class="table-cell orange-border loading">Caricamento...</td>
        </tr>
        <tr>
            <td id="colonna3" class="table-cell purple-border loading">Caricamento...</td>
            <td id="colonna4" class="table-cell orange-border loading">Caricamento...</td>
        </tr>
        <tr>
            <td id="colonna5" class="table-cell purple-border loading">Caricamento...</td>
            <td id="colonna6" class="table-cell orange-border loading">Caricamento...</td>
        </tr>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    function iniziaLav() {
        // Prima riga
        $.ajax({
            url: "creaTabellaRid913.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna1").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna1").html('Errore').removeClass('loading');
            }
        });

        $.ajax({
            url: "creaTabellaBoll1216.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna2").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna2").html('Errore').removeClass('loading');
            }
        });

        // Seconda riga
        $.ajax({
            url: "creaTabellaRid1316.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna3").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna3").html('Errore').removeClass('loading');
            }
        });

        $.ajax({
            url: "creaTabellaBoll1619.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna4").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna4").html('Errore').removeClass('loading');
            }
        });

        // Terza riga
        $.ajax({
            url: "creaTabellaRid1619.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna5").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna5").html('Errore').removeClass('loading');
            }
        });

        $.ajax({
            url: "creaTabellaBoll1920.php",
            method: "GET",
            success: function(valore_risposta) {
                $("#colonna6").html(valore_risposta).removeClass('loading');
            },
            error: function() {
                $("#colonna6").html('Errore').removeClass('loading');
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

    // Ricarica la pagina ogni 60 secondi (aumentato da 4 a 60 secondi)
    setInterval(function() {
        location.reload();
    }, 4000);
</script>

</body>
</html>