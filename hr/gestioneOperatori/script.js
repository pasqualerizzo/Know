function filterTable(parola, bottone) {
    // Seleziona tutti i pulsanti all'interno del div con la classe "pulsantiera"
    var div = document.querySelectorAll("#pulsantiera button");

    // Cambia il colore di tutti i pulsanti a grigio con testo nero
    div.forEach(btn => {
        btn.style.backgroundColor = "#f1f1f1";
        btn.style.color = "black";
    });

    // Cambia il colore del pulsante premuto a verde con testo bianco
    bottone.style.backgroundColor = "green";
    bottone.style.color = "white";

    // Filtra le righe della tabella in base alla parola
    var table = document.getElementById("tabellaOperatore");
    var tr = table.getElementsByTagName("tr");
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[4];
        if (td) {
            if (td.textContent.toLowerCase().indexOf(parola.toLowerCase()) > -1) {
                tr[i].style.display = ""; // Mostra la riga
            } else {
                tr[i].style.display = "none"; // Nascondi la riga
            }
        }
    }
}


function searchByName() {
    var input = document.getElementById("searchInput").value.toLowerCase();
    var table = document.querySelector(".tabellaOperatore");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[1]; // Nome Completo è nella seconda colonna
        if (td) {
            if (td.textContent.toLowerCase().indexOf(input) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function editRow(button) {
    // Trova la riga della tabella che contiene il pulsante cliccato
    var row = button.parentNode.parentNode;
    // Seleziona il primo elemento della riga
    var id = row.cells[0].innerText; // Utilizzando l'indice
    window.location.href = 'modificaOperatori/modificaOperatori.php?id=' + id;

}

function exportCSV() {
    var table = document.getElementById("tabellaOperatore");
    var rows = table.querySelectorAll("tr");
    var csvContent = "data:text/csv;charset=utf-8,";

    rows.forEach(row => {
        if (row.style.display !== "none") { // Controlla solo le righe visibili
            var cols = row.querySelectorAll("td:nth-child(2), th:nth-child(2)"); // Solo la seconda colonna

            var rowData = [];
            cols.forEach(col => rowData.push(col.textContent.replace(/,/g, ''))); // Rimuove eventuali virgole dai dati
            csvContent += rowData.join(",") + "\n";
        }
    });

    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "tabella_operatore.csv");
    document.body.appendChild(link); // Necessario per Firefox
    link.click();
}