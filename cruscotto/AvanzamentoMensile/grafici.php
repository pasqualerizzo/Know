<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grafici Avanzamento</title>
    <link href="../../css/tabella.css" rel="stylesheet">
    <link href="grafici.css" rel="stylesheet">
</head>
<body>
    <!-- Container per grafici settimanali -->
    <div class="grafici-container" id="grafici-week" style="display:none;">
        <h2>Grafici Avanzamento Settimanale</h2>
        
        <div class="grafico-row">
            <div class="grafico-box">
                <canvas id="chartWeekFatturato"></canvas>
            </div>
            <div class="grafico-box">
                <canvas id="chartWeekPezzi"></canvas>
            </div>
        </div>
        
        <div class="grafico-row">
            <div class="grafico-box">
                <canvas id="chartWeekOre"></canvas>
            </div>
            <div class="grafico-box">
                <canvas id="chartWeekResa"></canvas>
            </div>
        </div>
        
       
    </div>

    <!-- Container per grafici annuali -->
    <div class="grafici-container" id="grafici-annuale" style="display:none;">
        <h2>Grafici Avanzamento Annuale</h2>
        
        <div class="grafico-row">
            <div class="grafico-box">
                <canvas id="chartAnnualeFatturato"></canvas>
            </div>
            <div class="grafico-box">
                <canvas id="chartAnnualePezzi"></canvas>
            </div>
        </div>
        
        <div class="grafico-row">
            <div class="grafico-box">
                <canvas id="chartAnnualeOre"></canvas>
            </div>
            <div class="grafico-box">
                <canvas id="chartAnnualeResa"></canvas>
            </div>
        </div>
        
        
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="grafici.js"></script>
</body>
</html>

