// main.js
function tarifleriGetir(tur) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("tarifler").innerHTML = this.responseText;
        }
    };
    xhttp.open("GET", "getTarifler.php?tur=" + tur, true);
    xhttp.send();
}
