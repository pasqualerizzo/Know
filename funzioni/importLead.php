
<html>

    <head>

        <meta charset="UTF-8">

        <title>Pannello</title>

        <link  href="css/stile.css" rel="stylesheet">

    </head>

    <body>
        <h1 >Pannello Aggiornamento</h1>
      
    <form class="accesso" name="importUpdate" action="importUpdate.php" method="POST" enctype="multipart/form-data">

        <fieldset>

            <legend>Import</legend>
           

            <input type="file" name="import" accept=".csv" ><br/><br/>

            <input type="submit" value="import" name="import">

        </fieldset>

    </form>
</body>
</html>