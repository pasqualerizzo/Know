/**
 * File di integrazione per aggiungere grafici alle funzioni esistenti
 * Questo file estende le funzionalità senza modificare il codice legacy
 */

// Salva le funzioni originali
const creaTabellaAvanzamentoWeekOriginal = window.creaTabellaAvanzamentoWeek;
const creaTabellaAvanzamentoMesiOriginal = window.creaTabellaAvanzamentoMesi;

/**
 * Override della funzione creaTabellaAvanzamentoWeek per aggiungere grafici
 */
window.creaTabellaAvanzamentoWeek = function() {
    // Chiama la funzione originale
    if (creaTabellaAvanzamentoWeekOriginal) {
        creaTabellaAvanzamentoWeekOriginal();
    }
    
    // Carica i dati per i grafici
    caricaGraficiWeek();
};

/**
 * Override della funzione creaTabellaAvanzamentoMesi per aggiungere grafici
 */
window.creaTabellaAvanzamentoMesi = function() {
    // Chiama la funzione originale
    if (creaTabellaAvanzamentoMesiOriginal) {
        creaTabellaAvanzamentoMesiOriginal();
    }
    
    // Carica i dati per i grafici
    caricaGraficiAnnuale();
};

/**
 * Carica e genera grafici settimanali
 */
function caricaGraficiWeek() {
    const mese = document.getElementById("mese").value;
    const mandatoSelect = document.getElementById("mandato");
    const sedeSelect = document.getElementById("sede");
    
    if (!mese || !mandatoSelect || !sedeSelect) {
        console.warn('Elementi form mancanti per grafici week');
        return;
    }
    
    const mandato = [];
    for (let option of mandatoSelect.selectedOptions) {
        mandato.push(option.value);
    }
    
    const sede = [];
    for (let option of sedeSelect.selectedOptions) {
        sede.push(option.value);
    }
    
    if (mandato.length === 0) {
        console.warn('Nessun mandato selezionato');
        return;
    }
    
    // Mostra loading
    const containerWeek = document.getElementById('grafici-week');
    if (containerWeek) {
        containerWeek.style.display = 'block';
        containerWeek.innerHTML = '<div class="loading-grafici">Caricamento grafici in corso</div>';
    }
    
    // Chiamata AJAX per ottenere i dati
    const formData = new FormData();
    formData.append('mese', mese);
    formData.append('mandato', JSON.stringify(mandato));
    formData.append('sede', JSON.stringify(sede));
    
    fetch('tabella/getDatiGraficiWeek.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Errore dal server:', data.error);
            return;
        }
        
        console.log('Dati ricevuti per grafici week:', data);
        
        // Ripristina il container
        if (containerWeek) {
            containerWeek.innerHTML = `
                <h2>Grafici Avanzamento Settimanale</h2>
                <div class="grafico-row">
                    <div class="grafico-box"><canvas id="chartWeekFatturato"></canvas></div>
                    <div class="grafico-box"><canvas id="chartWeekPezzi"></canvas></div>
                </div>
                <div class="grafico-row">
                    <div class="grafico-box"><canvas id="chartWeekOre"></canvas></div>
                    <div class="grafico-box"><canvas id="chartWeekResa"></canvas></div>
                </div>
                <div class="grafico-row full-width">
                    <div class="grafico-box"><canvas id="chartWeekComparison"></canvas></div>
                </div>
            `;
        }
        
        // Genera i grafici
        if (window.generaGraficiWeek) {
            window.generaGraficiWeek(data);
        }
    })
    .catch(error => {
        console.error('Errore nel caricamento dati grafici:', error);
        if (containerWeek) {
            containerWeek.innerHTML = '<div class="loading-grafici" style="color: red;">Errore nel caricamento dei grafici</div>';
        }
    });
}

/**
 * Carica e genera grafici annuali
 */
function caricaGraficiAnnuale() {
    const mese = document.getElementById("mese").value;
    const mandatoSelect = document.getElementById("mandato");
    const sedeSelect = document.getElementById("sede");
    
    if (!mese || !mandatoSelect || !sedeSelect) {
        console.warn('Elementi form mancanti per grafici annuale');
        return;
    }
    
    const mandato = [];
    for (let option of mandatoSelect.selectedOptions) {
        mandato.push(option.value);
    }
    
    const sede = [];
    for (let option of sedeSelect.selectedOptions) {
        sede.push(option.value);
    }
    
    if (mandato.length === 0) {
        console.warn('Nessun mandato selezionato');
        return;
    }
    
    // Estrae mese/anno dal campo (formato YYYY-MM)
    const meseAnno = mese.split('-');
    const meseAnnoFormattato = meseAnno[1] + '/' + meseAnno[0]; // MM/YYYY
    
    // Mostra loading
    const containerAnnuale = document.getElementById('grafici-annuale');
    if (containerAnnuale) {
        containerAnnuale.style.display = 'block';
        containerAnnuale.innerHTML = '<div class="loading-grafici">Caricamento grafici in corso</div>';
    }
    
    // Chiamata AJAX per ottenere i dati
    const formData = new FormData();
    formData.append('mese_anno', meseAnnoFormattato);
    formData.append('mandato', JSON.stringify(mandato));
    formData.append('sede', JSON.stringify(sede));
    
    fetch('tabella/getDatiGraficiAnnuale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Errore dal server:', data.error);
            return;
        }
        
        console.log('Dati ricevuti per grafici annuale:', data);
        
        // Ripristina il container
        if (containerAnnuale) {
            containerAnnuale.innerHTML = `
                <h2>Grafici Avanzamento Annuale</h2>
                <div class="grafico-row">
                    <div class="grafico-box"><canvas id="chartAnnualeFatturato"></canvas></div>
                    <div class="grafico-box"><canvas id="chartAnnualePezzi"></canvas></div>
                </div>
                <div class="grafico-row">
                    <div class="grafico-box"><canvas id="chartAnnualeOre"></canvas></div>
                    <div class="grafico-box"><canvas id="chartAnnualeResa"></canvas></div>
                </div>
                <div class="grafico-row full-width">
                    <div class="grafico-box"><canvas id="chartAnnualeTrend"></canvas></div>
                </div>
            `;
        }
        
        // Genera i grafici
        if (window.generaGraficiAnnuale) {
            window.generaGraficiAnnuale(data);
        }
    })
    .catch(error => {
        console.error('Errore nel caricamento dati grafici:', error);
        if (containerAnnuale) {
            containerAnnuale.innerHTML = '<div class="loading-grafici" style="color: red;">Errore nel caricamento dei grafici</div>';
        }
    });
}

// Esporta le funzioni per debug
window.caricaGraficiWeek = caricaGraficiWeek;
window.caricaGraficiAnnuale = caricaGraficiAnnuale;

console.log('✅ Integrazione grafici caricata correttamente');

