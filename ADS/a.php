<?php

?>

<html>

    <head>

        <meta charset="UTF-8">

        <title>Pannello</title>

        <link  href="css/stile.css" rel="stylesheet">

    </head>

    <body>
        <h1 style="text-align: center"><img src="images/NH1.png" height="40" alt="">Pannello Aggiornamento CRM2<img src="images/NH1.png" height="40" alt=""></h1>
      

        <h2>Enel Out</h2>
        <form class="accesso" name="import" action="import.php" method="POST" enctype="multipart/form-data">

            <fieldset>

                <legend>Import Enel Out</legend>

                <input type="file" name="import" accept=".csv" ><br/><br/>

                <input type="submit" value="import" name="import">

            </fieldset>

        </form>



        
    </body>

</html>

