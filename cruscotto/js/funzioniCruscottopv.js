function aggiornaMandato() {

    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("/Know/cruscotto/funzioni/aggiornaMandato.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
                aggiornaSede();
            }
            )
            .catch(err => console.log(err));
}

function aggiornaMandatoMese() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("mese", mese);
    fetch("/Know/cruscotto/funzioni/aggiornaMandatoMese.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
                aggiornaSedeMese();
            }
            )
            .catch(err => console.log(err));
}


function aggiornaAgenzia() {

    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("agenzia");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("/Know/cruscotto/funzioni/aggiornaAgenziaGt.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
            }
            )
            .catch(err => console.log(err));
}



function aggiornaMandatoInvertito() {
//permessi();
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("/Know/cruscotto/funzioni/aggiornaMandato.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
                aggiornaSedeInvertito()
            }
            )
            .catch(err => console.log(err));
}


function aggiornaSede() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/funzioni/aggiornaSede.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}


function aggiornaSedePlenitude() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;

    var valoreMandato = ["Plenitude"];

    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/funzioni/aggiornaSede.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}


function aggiornaSedeKpi() {
    var table = document.getElementById("tabellaKPI");
    var sede = document.getElementById("sede");
    var uniqueValues = new Set();
    for (var i = 1; i < table.rows.length; i++) {
// Ottieni il valore della prima colonna (indice 0)
        var value = table.rows[i].cells[1].innerText;
        console.log(value);
        uniqueValues.add(value);
    }
    var uniqueArray = Array.from(uniqueValues);
    rimuoviOption(sede);
    for (let i = 0; i < uniqueArray.length; i++) {
        var opzione = document.createElement("option");
        opzione.text = uniqueArray[i];
        opzione.value = uniqueArray[i];
        sede.add(opzione);
    }
    var primo = sede.options[0];
    primo.selected = true;

}


function aggiornaSedeInvertito() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/funzioni/aggiornaSedeInvertito.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}


function aggiornaSedeInvertito() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/funzioni/aggiornaSedeInvertito.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}


function aggiornaSedeMese() {
    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/funzioni/aggiornaSedeMese.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}

function creaTabella() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabella.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaAvanzamento() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../AvanzamentoMensile/tabella/creaTabellaAvanzamento.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaInvertito() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaInvertito.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}



function creaTabellaInvertitoEsteso() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaInvertitoEsteso.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdp() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpInterno.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdpEsteso() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpInterno.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaPdpEsterno() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaPdpEsterno.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdpEsternoEsteso() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpEsterno.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function rimuoviOption(elemento) {
    var lunghezza = elemento.options.length;
    for (let i = lunghezza; i >= 0; i--) {
        elemento.remove(i);
    }
}

function selezionaTutteSedi() {
    var x = document.getElementById("sede");
    for (i = 0; i < x.length; i++) {
        x.options[i].selected = true;
    }
}

function creaTabellaOperatore() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoOperatore/tabella/creaTabellaOperatore.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}


function creaTabellaOperatoreMese() {

    var data = new FormData();
    fetch("../cruscottoOperatoreMese/tabella/creaTabellaOperatoreMese.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}

function creaTabellaGiornaliero() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaCruscottoInvertitoLead.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdpGiornaliero() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpInternoGiornaliero.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaLead() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var agenzia = document.getElementById("agenzia");
    var valoreAgenzia = [];
    for (const option of agenzia.options) {
        if (option.selected) {
            valoreAgenzia.push(option.value);
        }
    }

    data.append("agenzia", JSON.stringify(valoreAgenzia));
    fetch("../cruscottoLead/tabella/creaTabellaLead.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaLeadCampagna() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var agenzia = document.getElementById("agenzia");
    var valoreAgenzia = [];
    for (const option of agenzia.options) {
        if (option.selected) {
            valoreAgenzia.push(option.value);
        }
    }

    var categoria = document.getElementById("categoria");
    var valoreCategoria = [];
    for (const option of categoria.options) {
        if (option.selected) {
            valoreCategoria.push(option.value);
        }
    }


    data.append("agenzia", JSON.stringify(valoreAgenzia));
    data.append("categoria", JSON.stringify(valoreCategoria));
    fetch("../cruscottoLead/tabella/creaTabellaLeadCampagna.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaLeadProduzione() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var agenzia = document.getElementById("agenzia");
    var valoreAgenzia = [];
    for (const option of agenzia.options) {
        if (option.selected) {
            valoreAgenzia.push(option.value);
        }
    }

    data.append("agenzia", JSON.stringify(valoreAgenzia));
    fetch("../cruscottoLead/tabella/creaTabellaLeadProduzione.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaStore() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("../cruscottoStore/tabella/creaTabellaStore.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function pdpInternoVuoto() {

    tabella = document.getElementById("PdpInterno");
    tabella.innerHTML = "";
}

function pdpEsternoVuoto() {

    tabella = document.getElementById("PdpEsterno");
    tabella.innerHTML = "";
}

function creaTabellaStoreMessaggi() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("../cruscottoStore/tabella/creaTabellaStoreMessaggi.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}



function creaTabellaLeadKLpi() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
//    var agenzia = document.getElementById("agenzia");
//    var valoreAgenzia = [];
//    for (const option of agenzia.options) {
//        if (option.selected) {
//            valoreAgenzia.push(option.value);
//        }
//    }
//
//    data.append("agenzia", JSON.stringify(valoreAgenzia));


    fetch("../CruscottoKpiLead/tabella/creaTabellakpilead.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                aggiornaSedeKpi();
                calcolaTotali();
            })
            .catch(err => console.log(err));
}

function creaTabellaLeadKpiNuovo() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);



    fetch("../CruscottoKpiLeadNuovo/tabella/creaTabellakpilead.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                aggiornaSedeKpi();
                calcolaTotaliNuovo();
            })
            .catch(err => console.log(err));
}


function sortTableKPI(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tabellaKPI");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
// Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 2); i++) {
// Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
// If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
// If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
function sortTableNumeroKPI(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tabellaKPI");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
// Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 2); i++) {
// Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (Number(x.innerHTML) > Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (Number(x.innerHTML) < Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}

function filterTable() {
    var input, filters, table, tr, td, i, j, txtValue;
    input = document.getElementById("sede");
    filters = Array.from(input.selectedOptions).map(option => option.value.toUpperCase());
    console.log(filters);
    table = document.getElementById("tabellaKPI");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                for (var k = 0; k < filters.length; k++) {
                    if (txtValue.toUpperCase().indexOf(filters[k].trim()) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
                if (tr[i].style.display === "") {
                    break;
                }
            }
        }
    }
    calcolaTotali();
}






function calcolaTotali() {
// Ottieni la tabella
    var tabella = document.getElementById('tabellaKPI');
    var righe = tabella.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    // Inizializza un array per i totali
    var totali = Array(27).fill(0); // 19 colonne numeriche

    // Itera attraverso le righe della tabella
    for (var i = 0; i < righe.length; i++) {
// Controlla se la riga è visibile
        if (righe[i].style.display !== 'none') {
            var celle = righe[i].getElementsByTagName('td');
            for (var j = 2; j < celle.length; j++) { // Inizia da 2 per saltare le prime due colonne non numeriche
                var valore = parseFloat(celle[j].innerText) || 0;
                totali[j] += valore;
            }
        }
    }
    //console.log(totali);
    // Crea una nuova riga per i totali
    var rigaTotali = tabella.insertRow(-1);
    rigaTotali.style.backgroundColor = "orange";
    rigaTotali.style.border = "2px solid #AAAAAA";
    var cellaOperatore = rigaTotali.insertCell(0);
    cellaOperatore.innerText = 'Totali';
    var cellaSede = rigaTotali.insertCell(1);
    cellaSede.innerText = '-';
    for (var x = 2; x < 27; x++) {
        var cellaGenerica = rigaTotali.insertCell(x);
        switch (x) {
            case 16:
            case 17:
                cellaGenerica.innerText = '-';
                break;
            case 10:
            case 12:
            case 14:
            case 19:
                cellaGenerica.innerText = (((totali[x - 1] / totali[4])) * 100).toFixed(2);
                break;
            case 8:
                cellaGenerica.innerText = (((totali[x - 1] / totali[6]))).toFixed(2);
                break;
            case 24:
                cellaGenerica.innerText = (totali[4] / totali[2]).toFixed(2);
                break;
            default:
                cellaGenerica.innerText = totali[x].toFixed(2);
        }
    }
}

function calcolaTotaliNuovo() {
// Ottieni la tabella
    var tabella = document.getElementById('tabellaKPI');
    var righe = tabella.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    // Inizializza un array per i totali
    var totali = Array(26).fill(0); // 19 colonne numeriche
    var lunghezza = 0;
    // Itera attraverso le righe della tabella
    for (var i = 0; i < righe.length; i++) {
// Controlla se la riga è visibile
        lunghezza = lunghezza + 1;
        if (righe[i].style.display !== 'none') {
            var celle = righe[i].getElementsByTagName('td');
            for (var j = 2; j < celle.length; j++) { // Inizia da 2 per saltare le prime due colonne non numeriche
                var valore = parseFloat(celle[j].innerText) || 0;
                totali[j] += valore;

            }
        }
    }
    console.log(lunghezza);
    // Crea una nuova riga per i totali
    var rigaTotali = tabella.insertRow(-1);
    rigaTotali.style.backgroundColor = "orange";
    rigaTotali.style.border = "2px solid #AAAAAA";
    var cellaOperatore = rigaTotali.insertCell(0);
    cellaOperatore.innerText = 'Totali';
    var cellaSede = rigaTotali.insertCell(1);
    cellaSede.innerText = '-';
    for (var x = 2; x < 26; x++) {
        var cellaGenerica = rigaTotali.insertCell(x);
        switch (x) {
            case 12:
            case 13:
                cellaGenerica.innerText = '-';
                break;
            case 3:
            case 23:
            case 24:
            case 25:
                cellaGenerica.innerText = (totali[x] / lunghezza).toFixed(2);
                break;
            case 20:
                cellaGenerica.innerText = (((totali[4] / totali[2]))).toFixed(2);
                break;
            case 10:
            case 15:
            case 22:
                cellaGenerica.innerText = (((totali[x - 1] / totali[4])) * 100).toFixed(2);
                break;
            case 8:
                cellaGenerica.innerText = (((totali[x - 1] / totali[6]))).toFixed(2);
                break;

            default:
                cellaGenerica.innerText = totali[x].toFixed(2);
        }
    }
}


function calcolaTotaliSub() {
// Ottieni la tabella
    var tabella = document.getElementById('tabellaKPI');
    var righe = tabella.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    // Inizializza un array per i totali
    var totali = Array(22).fill(0); // 19 colonne numeriche

    // Itera attraverso le righe della tabella
    for (var i = 0; i < righe.length; i++) {
// Controlla se la riga è visibile
        if (righe[i].style.display !== 'none') {
            var celle = righe[i].getElementsByTagName('td');
            for (var j = 2; j < celle.length; j++) { // Inizia da 2 per saltare le prime due colonne non numeriche
                var valore = parseFloat(celle[j].innerText) || 0;
                totali[j] += valore;
            }
        }
    }
    //console.log(totali);
    // Crea una nuova riga per i totali
    var rigaTotali = tabella.insertRow(-1);
    rigaTotali.style.backgroundColor = "orange";
    rigaTotali.style.border = "2px solid #AAAAAA";
    var cellaOperatore = rigaTotali.insertCell(0);
    cellaOperatore.innerText = 'Totali';
    var cellaSede = rigaTotali.insertCell(1);
    cellaSede.innerText = '-';
    for (var x = 2; x < 22; x++) {
        var cellaGenerica = rigaTotali.insertCell(x);
        switch (x) {

            case 7:
            case 9:
            case 11:
            case 16:

            case 21:
                cellaGenerica.innerText = (((totali[x - 1] / totali[4])) * 100).toFixed(2);
                break;
            case 19:
                cellaGenerica.innerText = ((totali[4] / totali[2])).toFixed(2);
                ;
                break;
            case 13:
            case 14:
                cellaGenerica.innerText = "-";
                break;

            default:
                cellaGenerica.innerText = totali[x].toFixed(2);
        }
    }
}

function creaTabellaAvanzamentoMandati() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../AvanzamentoMensile/tabella/creatabellamandati.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function aggiornaSedeData() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var sede = document.getElementById("sede");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("/Know/cruscotto/funzioni/aggiornaSedeData.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.json())
            .then((response) => {
                console.log(response);
                rimuoviOption(sede);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    sede.add(opzione);
                }
                sede.options[0].selected = true;
            })
            .catch(err => console.log(err));
}


function creaTabellaChiusuraSerale() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }

    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/cruscottoChiusuraSerale/tabella/creaTabellaChiusuraSeralaNUOVA.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaChiusuraSeraleInbound() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }

    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/cruscottoChiusuraSerale/tabella/creaTabellaChiusuraSeralaInbound.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function sortTableOperatore(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tableOperatore");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
// Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
// Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
// If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
// If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
function sortTableNumeroOperatore(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("tableOperatore");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
     no switching has been done: */
    while (switching) {
// Start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /* Loop through all table rows (except the
         first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
// Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
             one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
             based on the direction, asc or desc: */
            if (dir == "asc") {
                if (Number(x.innerHTML) > Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (Number(x.innerHTML) < Number(y.innerHTML)) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
             and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /* If no switching has been done AND the direction is "asc",
             set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}
function creaTabellaMatricola() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaCodiceMatricola.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("CodMatricola");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function aggiornaCategoria() {

    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("categoria");
    var agenzia = document.getElementById("agenzia");
    var valoreAgenzia = [];
    for (const option of agenzia.options) {
        if (option.selected) {
            valoreAgenzia.push(option.value);
        }
    }
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("agenzia", JSON.stringify(valoreAgenzia));
    fetch("/Know/cruscotto/funzioni/aggiornaCategoria.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
            }
            )
            .catch(err => console.log(err));
}

function creaTabellaChiusuraSeraleTotale() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }

    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/cruscottoChiusuraSerale/tabella/creaTabellaChiusuraSeralaTotale.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}




function filtroEnergyKpi() {
    var input, filters, table, tr, td, i, j, txtValue;
    //Valore del checkbox energy

    //Valore Sede
    sede = document.getElementById("sede");
    valoreSede = Array.from(sede.selectedOptions).map(option => option.value.toUpperCase());
//    console.log($valoreRicerca);
    table = document.getElementById("tabellaKPI");
    tr = table.getElementsByTagName("tr");
    if (tr.length > 1) {
        table.deleteRow(-1);
    }
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        var sedeTrovata = false;

        txtValue = td[1].textContent || td[1].innerText;
        for (var k = 0; k < valoreSede.length; k++) {
            if (txtValue.toUpperCase().indexOf(valoreSede[k].trim()) > -1) {
                sedeTrovata = true;
            }
        }


        if (sedeTrovata) {
            tr[i].style.display = "";
        }

    }
    calcolaTotali();
}

function filtroEnergyKpiNuova() {
    var input, filters, table, tr, td, i, j, txtValue;
    
    // Valore Sede
    sede = document.getElementById("sede");
    valoreSede = Array.from(sede.selectedOptions).map(option => option.value.toUpperCase());
    
    // Se nessuna sede è selezionata, mostra tutte le righe
    var mostraTutteLeSedi = valoreSede.length === 0;
    
    table = document.getElementById("tabellaKPI");
    tr = table.getElementsByTagName("tr");
    
    if (tr.length > 1) {
        table.deleteRow(-1);
    }
    
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        var sedeTrovata = mostraTutteLeSedi; // Se true, mostra tutte le sedi
        
        if (!mostraTutteLeSedi) {
            txtValue = td[1].textContent || td[1].innerText;
            for (var k = 0; k < valoreSede.length; k++) {
                if (txtValue.toUpperCase().indexOf(valoreSede[k].trim()) > -1) {
                    sedeTrovata = true;
                    break;
                }
            }
        }

        if (sedeTrovata) {
            tr[i].style.display = "";
        }
    }
    calcolaTotaliNuovo();
}


function resetKpi() {
    ceckEnergy = document.getElementById("ceckEnergy");
    ceckEnergy.checked = false;
    //Valore del checkbox telco
    ceckTelco = document.getElementById("ceckTelco");
    ceckTelco.checked = false;
}


function creaTabellaCruscottoBo() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);

    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }

    data.append("mandato", JSON.stringify(valoreMandato));
    fetch("/Know/cruscotto/CruscottoBo/tabella/creaTabellaCruscottoBo.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}
function creaTabellaAssenze() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
//    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/CruscottoAssenze/tabella/creaCruscottoAssenze.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}

function creaTabellaGiornalieroSwVol() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaInvertitoSwVol.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaPdpSwVol() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpInternoSwVol.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdpEsternoSwVol() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzione/tabella/creaTabellaPdpEsternoSwVol.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaMatricolaSwVol() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaCodiceMatricolaSwVol.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("CodMatricola");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}



function creaTabellaTl() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
//    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneInvertito/tabella/creaTabellaTl.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabellaTL");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaVistaMandato() {
    var meseRiferimento = document.getElementById("meseRiferimento").value;
    var data = new FormData();
    data.append("meseRiferimento", meseRiferimento);

    fetch("../vistaSettimanale/tabella/creaVistaMandato.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


//
function creaTabellaAvanzamentoWeek() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../AvanzamentoMensile/tabella/creatabellaAvanzamentoWeek.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("week");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

//
function creaTabellaAvanzamentoWeekSede() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../AvanzamentoMensile/tabella/creatabellaAvanzamentoWeekSede.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("weeksede");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function creaTabellaAvanzamentoMesi() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../AvanzamentoMensile/tabella/creatabellaAvanzamentoMesi.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("weekgroup");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

//
function creaTabellaCruscottoMese() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("mese", mese);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../CruscottoMensile/tabella/creaTabellaMensile.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("week");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}
function creaTabellaContattiUtili() {
    const mese = document.getElementById("mese").value;
    if (!mese) {
        alert('Seleziona un mese');
        return;
    }

    const data = new FormData();
    data.append("mese", mese);

    fetch("../ContattiUtili/tabella/creaTabellaContattiUtili.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(response => {
                document.getElementById("ContattiUtili").innerHTML = response;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById("ContattiUtili").innerHTML = '<p>Errore nel caricamento dei dati</p>';
            });
}

function creaTabellaCU() {
    const mese = document.getElementById("mese").value;
    if (!mese) {
        alert('Seleziona un mese');
        return;
    }

    const data = new FormData();
    data.append("mese", mese);

    fetch("../ContattiUtili/tabella/creaTabellaCu.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(response => {
                document.getElementById("Cu").innerHTML = response;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById("Cu").innerHTML = '<p>Errore nel caricamento dei dati</p>';
            });
}


function creaTabellaLeadKpiPlenitude() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;

    var sede = document.getElementById("sede").value;

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("sede", sede);



    fetch("../CruscottoKpiLeadPlenitude/tabella/creaTabellakpileadplenitude.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("kpi");
                tabella.innerHTML = response;

                calcolaTotaliNuovo();
            })
            .catch(err => console.log(err));
}


function creaTabellaQuartili() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;

    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("sede", JSON.stringify(valoreSede));




    fetch("../CruscottoKpiLeadPlenitude/tabella/creaTabellaQuartili.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("quartili");
                tabella.innerHTML = response;

                //calcolaTotaliNuovo();
            })
            .catch(err => console.log(err));
}

function creaTabellaOreDichiarate() {
    const mese = document.getElementById("mese").value;
    if (!mese) {
        alert('Seleziona un mese');
        return;
    }

    const data = new FormData();
    data.append("mese", mese);

    fetch("../CruscottoOreDichiarate/tabella/oreDichiarate.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(response => {
                document.getElementById("oreDichiarate").innerHTML = response;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById("oreDichiarate").innerHTML = '<p>Errore nel caricamento dei dati</p>';
            });
}


function creaTabellaOreDichiarateMensile() {
    const mese = document.getElementById("mese").value;
    if (!mese) {
        alert('Seleziona un mese');
        return;
    }

    const data = new FormData();
    data.append("mese", mese);

    fetch("../CruscottoOreDichiarate/tabella/oreDichiarate.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(response => {
                document.getElementById("oreDichiarate").innerHTML = response;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById("oreDichiarate").innerHTML = '<p>Errore nel caricamento dei dati</p>';
            });
}


function aggiornaMandatoMeseOre() {

    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("mese", mese);
    fetch("/Know/cruscotto/funzioni/aggiornaMandatoMeseOre.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
                aggiornaSedeMese();
            }
            )
            .catch(err => console.log(err));
}

function creaTabellaAssenzeMese() {
    var mese = document.getElementById("mese").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

       var data = new FormData();
        data.append("mese", mese);
//    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/CruscottoAssenze/tabella/creaCruscottoAssenze.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}


function creaTabellaOperatoreAssenze() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/CruscottoAssenze/tabella/creaCruscottoAssenze.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}


function creaTabellaOperatoreAssenzeSede() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/CruscottoAssenze/tabella/creaCruscottoAssenzeSede.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("assenza");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}


function creaTabellaOperatoreAssenzeSedeSettimana() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("/Know/cruscotto/CruscottoAssenze/tabella/creaCruscottoAssenzeSedeSettimane.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("settimana");
                tabella.innerHTML = response;
                tabella2 = document.getElementById("PdpEsterno");
                tabella2.innerHTML = "";
                tabella3 = document.getElementById("PdpInterno");
                tabella3.innerHTML = "";
            })
            .catch(err => console.log(err));
}


function creaTabellaTelco() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var testMode = document.getElementById("testMode").checked;
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("testMode", testMode);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneTelco/tabella/creaTabellaTelco.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("tabella");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}

function aggiornaMandatoTelco() {
//permessi();
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    fetch("/Know/cruscotto/funzioni/aggiornaMandatoTelco.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }
    ).then(response => response.json())
            .then((response) => {
                //console.log(response);
                rimuoviOption(mandato);
                for (let i = 0; i < response.length; i++) {
                    var opzione = document.createElement("option");
                    opzione.text = response[i];
                    opzione.value = response[i];
                    mandato.add(opzione);
                }
                mandato.options[0].selected = true;
                aggiornaSedeInvertito()
            }
            )
            .catch(err => console.log(err));
}



function creaTabellaPdpTelco() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneTelco/tabella/creaTabellaPdpInternoTelco.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpInterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}


function creaTabellaPdpEsternoTelco() {
    var dataMinore = document.getElementById("dataInizio").value;
    var dataMaggiore = document.getElementById("dataFine").value;
    var mandato = document.getElementById("mandato");
    var valoreMandato = [];
    for (const option of mandato.options) {
        if (option.selected) {
            valoreMandato.push(option.value);
        }
    }
    var sede = document.getElementById("sede");
    var valoreSede = [];
    for (const option of sede.options) {
        if (option.selected) {
            valoreSede.push(option.value);
        }
    }

    var data = new FormData();
    data.append("dataMinore", dataMinore);
    data.append("dataMaggiore", dataMaggiore);
    data.append("mandato", JSON.stringify(valoreMandato));
    data.append("sede", JSON.stringify(valoreSede));
    fetch("../cruscottoProduzioneTelco/tabella/creaTabellaPdpEsternoTelco.php", {
        method: "POST",
        body: data,
        redirect: 'manual'
    }).then(response => response.text())
            .then((response) => {
                console.log(response);
                tabella = document.getElementById("PdpEsterno");
                tabella.innerHTML = response;
            })
            .catch(err => console.log(err));
}
