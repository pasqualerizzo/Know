// Configurazione globale Chart.js
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.color = '#666';

// Palette colori per i mandati
const mandatiColors = {
    'Plenitude': '#FF6384',
    'Vivigas Energia': '#36A2EB',
    'Vivigas': '#36A2EB',
    'Enel': '#FFCE56',
    'Iren': '#4BC0C0',
    'EnelIn': '#9966FF',
    'Heracom': '#FF9F40',
    'TIM': '#C9CBCF',
    'TOTALE': '#2c3e50'
};

// Storage per i chart instances
let chartInstances = {};

/**
 * Distrugge tutti i grafici esistenti
 */
function distruggiGrafici() {
    Object.keys(chartInstances).forEach(key => {
        if (chartInstances[key]) {
            chartInstances[key].destroy();
        }
    });
    chartInstances = {};
}

/**
 * Genera grafici settimanali
 */
function generaGraficiWeek(datiTabella) {
    console.log('Generazione grafici settimanali:', datiTabella);
    
    distruggiGrafici();
    
    const containerWeek = document.getElementById('grafici-week');
    const containerAnnuale = document.getElementById('grafici-annuale');
    
    if (!containerWeek) return;
    
    containerWeek.style.display = 'block';
    containerAnnuale.style.display = 'none';
    
    const labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];
    
    // Prepara i dataset
    const datasets = {
        fatturato: [],
        pezzi: [],
        ore: [],
        resa: []
    };
    
    Object.keys(datiTabella).forEach(mandato => {
        const data = datiTabella[mandato];
        const color = mandatiColors[mandato] || '#999';
        
        datasets.fatturato.push({
            label: mandato,
            data: [data.w1?.fatturato || 0, data.w2?.fatturato || 0, data.w3?.fatturato || 0, 
                   data.w4?.fatturato || 0, data.w5?.fatturato || 0, data.w6?.fatturato || 0],
            borderColor: color,
            backgroundColor: color + '33',
            tension: 0.4,
            fill: true
        });
        
        datasets.pezzi.push({
            label: mandato,
            data: [data.w1?.pezzi || 0, data.w2?.pezzi || 0, data.w3?.pezzi || 0, 
                   data.w4?.pezzi || 0, data.w5?.pezzi || 0, data.w6?.pezzi || 0],
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        });
        
        datasets.ore.push({
            label: mandato,
            data: [data.w1?.ore || 0, data.w2?.ore || 0, data.w3?.ore || 0, 
                   data.w4?.ore || 0, data.w5?.ore || 0, data.w6?.ore || 0],
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        });
        
        datasets.resa.push({
            label: mandato,
            data: [data.w1?.resa || 0, data.w2?.resa || 0, data.w3?.resa || 0, 
                   data.w4?.resa || 0, data.w5?.resa || 0, data.w6?.resa || 0],
            borderColor: color,
            backgroundColor: color + '33',
            tension: 0.4
        });
    });
    
    // Grafico Fatturato
    const ctxFatturato = document.getElementById('chartWeekFatturato');
    if (ctxFatturato) {
        chartInstances.weekFatturato = new Chart(ctxFatturato, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.fatturato
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Fatturato Settimanale (€)',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Pezzi
    const ctxPezzi = document.getElementById('chartWeekPezzi');
    if (ctxPezzi) {
        chartInstances.weekPezzi = new Chart(ctxPezzi, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets.pezzi
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pezzi Settimanali',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Grafico Ore
    const ctxOre = document.getElementById('chartWeekOre');
    if (ctxOre) {
        chartInstances.weekOre = new Chart(ctxOre, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets.ore
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ore Lavorate Settimanali',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' h';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Resa
    const ctxResa = document.getElementById('chartWeekResa');
    if (ctxResa) {
        chartInstances.weekResa = new Chart(ctxResa, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.resa
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Resa Oraria Settimanale (€/h)',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' €/h';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' €/h';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Comparazione (solo TOTALE)
    if (datiTabella['TOTALE']) {
        const totale = datiTabella['TOTALE'];
        const ctxComparison = document.getElementById('chartWeekComparison');
        
        if (ctxComparison) {
            chartInstances.weekComparison = new Chart(ctxComparison, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Fatturato',
                            data: [totale.w1?.fatturato || 0, totale.w2?.fatturato || 0, totale.w3?.fatturato || 0, 
                                   totale.w4?.fatturato || 0, totale.w5?.fatturato || 0, totale.w6?.fatturato || 0],
                            borderColor: '#FF6384',
                            backgroundColor: '#FF638433',
                            yAxisID: 'y',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Pezzi',
                            data: [totale.w1?.pezzi || 0, totale.w2?.pezzi || 0, totale.w3?.pezzi || 0, 
                                   totale.w4?.pezzi || 0, totale.w5?.pezzi || 0, totale.w6?.pezzi || 0],
                            borderColor: '#36A2EB',
                            backgroundColor: '#36A2EB33',
                            yAxisID: 'y1',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Generale - Fatturato vs Pezzi',
                            font: { size: 18, weight: 'bold' }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Fatturato (€)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pezzi'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
    }
}

/**
 * Genera grafici annuali
 */
function generaGraficiAnnuale(datiTabella) {
    console.log('Generazione grafici annuali:', datiTabella);
    
    distruggiGrafici();
    
    const containerWeek = document.getElementById('grafici-week');
    const containerAnnuale = document.getElementById('grafici-annuale');
    
    if (!containerAnnuale) return;
    
    containerWeek.style.display = 'none';
    containerAnnuale.style.display = 'block';
    
    const labels = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
    
    // Prepara i dataset
    const datasets = {
        fatturato: [],
        pezzi: [],
        ore: [],
        resa: []
    };
    
    Object.keys(datiTabella).forEach(mandato => {
        const data = datiTabella[mandato];
        const color = mandatiColors[mandato] || '#999';
        
        const fatturatoData = [];
        const pezziData = [];
        const oreData = [];
        const resaData = [];
        
        for (let m = 1; m <= 12; m++) {
            const mese = data['m' + m];
            fatturatoData.push(mese?.fatturato || 0);
            pezziData.push(mese?.pezzi || 0);
            oreData.push(mese?.ore || 0);
            resaData.push(mese?.resa || 0);
        }
        
        datasets.fatturato.push({
            label: mandato,
            data: fatturatoData,
            borderColor: color,
            backgroundColor: color + '33',
            tension: 0.4,
            fill: true
        });
        
        datasets.pezzi.push({
            label: mandato,
            data: pezziData,
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        });
        
        datasets.ore.push({
            label: mandato,
            data: oreData,
            backgroundColor: color,
            borderColor: color,
            borderWidth: 1
        });
        
        datasets.resa.push({
            label: mandato,
            data: resaData,
            borderColor: color,
            backgroundColor: color + '33',
            tension: 0.4
        });
    });
    
    // Grafico Fatturato Annuale
    const ctxFatturato = document.getElementById('chartAnnualeFatturato');
    if (ctxFatturato) {
        chartInstances.annualeFatturato = new Chart(ctxFatturato, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.fatturato
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Fatturato Mensile (€)',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Pezzi Annuale
    const ctxPezzi = document.getElementById('chartAnnualePezzi');
    if (ctxPezzi) {
        chartInstances.annualePezzi = new Chart(ctxPezzi, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets.pezzi
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pezzi Mensili',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        stacked: false
                    }
                }
            }
        });
    }
    
    // Grafico Ore Annuale
    const ctxOre = document.getElementById('chartAnnualeOre');
    if (ctxOre) {
        chartInstances.annualeOre = new Chart(ctxOre, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets.ore
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ore Lavorate Mensili',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' h';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Resa Annuale
    const ctxResa = document.getElementById('chartAnnualeResa');
    if (ctxResa) {
        chartInstances.annualeResa = new Chart(ctxResa, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets.resa
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Resa Oraria Mensile (€/h)',
                        font: { size: 16, weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' €/h';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' €/h';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Grafico Trend Annuale
    if (datiTabella['TOTALE']) {
        const totale = datiTabella['TOTALE'];
        const fatturatoData = [];
        const pezziData = [];
        
        for (let m = 1; m <= 12; m++) {
            const mese = totale['m' + m];
            fatturatoData.push(mese?.fatturato || 0);
            pezziData.push(mese?.pezzi || 0);
        }
        
        const ctxTrend = document.getElementById('chartAnnualeTrend');
        if (ctxTrend) {
            chartInstances.annualeTrend = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Fatturato',
                            data: fatturatoData,
                            borderColor: '#FF6384',
                            backgroundColor: '#FF638433',
                            yAxisID: 'y',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Pezzi',
                            data: pezziData,
                            borderColor: '#36A2EB',
                            backgroundColor: '#36A2EB33',
                            yAxisID: 'y1',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Annuale - Fatturato vs Pezzi',
                            font: { size: 18, weight: 'bold' }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Fatturato (€)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(value);
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pezzi'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
    }
}

// Esporta le funzioni
window.generaGraficiWeek = generaGraficiWeek;
window.generaGraficiAnnuale = generaGraficiAnnuale;
window.distruggiGrafici = distruggiGrafici;

