function creaTabellaSMS() {
    var mese = document.getElementById("mese").value;
    
    var data = new FormData();
    data.append("mese", mese);
    
    
    fetch("../cruscottoSMS/tabella/creaTabellaSMS.php", {
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





