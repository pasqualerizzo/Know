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
                mandato.options[0].selected=true;
                aggiornaSede()
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
            fetch("/Know/cruscotto/funzioni/aggiornaAgenzia.php", {
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
                mandato.options[0].selected=true;
                
            }
            )
    .catch(err => console.log(err));
}



function aggiornaMandatoInvertito() {
    permessi();
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
                mandato.options[0].selected=true;
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
                sede.options[0].selected=true;
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
                sede.options[0].selected=true;
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
    fetch("../cruscottoProduzione/tabella/creaTabellaGiornaliero.php", {
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
    
    data.append("agenzia",JSON.stringify(valoreAgenzia));

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
    
    data.append("agenzia",JSON.stringify(valoreAgenzia));

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
    
    data.append("agenzia",JSON.stringify(valoreAgenzia));

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

function permessi() {
            var permesso = document.getElementById("permessi").value;
            console.log(permesso);
            var liHr = document.getElementById("liHR");
            var liCruscottoProduzione = document.getElementById("liCruscottoProduzione");
            var liCruscottoLead = document.getElementById("liCruscottoLead");
            var liCruscottoStore = document.getElementById("liCruscottoStore");
             var liCruscottoProduzioneInvertito = document.getElementById("liCruscottoProduzioneInvertito");
            switch (permesso) {
                case "CEO":
                    break;
                case "HR":
                    liCruscottoProduzione.parentNode.removeChild(liCruscottoProduzione);
                    liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                    liCruscottoLead.parentNode.removeChild(liCruscottoLead);
                    liCruscottoStore.parentNode.removeChild(liCruscottoStore);
                    break;
                case "TL":
                    liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                    liHr.parentNode.removeChild(liHr);
                    liCruscottoLead.parentNode.removeChild(liCruscottoLead);
                    liCruscottoStore.parentNode.removeChild(liCruscottoStore);
                    break;
                case "Supervisor":                    
                    liHr.parentNode.removeChild(liHr);
                    //liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                break;
            case "Store" :                    
                    liHr.parentNode.removeChild(liHr);
                    liCruscottoProduzione.parentNode.removeChild(liCruscottoProduzione);
                    liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                    liCruscottoLead.parentNode.removeChild(liCruscottoLead);
                   
                    break;
                    
                    case "Marketing" :                    
                    liHr.parentNode.removeChild(liHr);
                    liCruscottoProduzione.parentNode.removeChild(liCruscottoProduzione);
                    liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                    
                   liCruscottoStore.parentNode.removeChild(liCruscottoStore);
                    break;
                default :                    
                    liHr.parentNode.removeChild(liHr);
                    liCruscottoProduzione.parentNode.removeChild(liCruscottoProduzione);
                    liCruscottoProduzioneInvertito.parentNode.removeChild(liCruscottoProduzioneInvertito);
                    liCruscottoLead.parentNode.removeChild(liCruscottoLead);
                    liCruscottoStore.parentNode.removeChild(liCruscottoStore);
                    break;
            }
        }