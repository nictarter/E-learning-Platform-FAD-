/**
 * This file is part of Area FAD
 * @author      Nicolò Tarter <nicolo.tarter@gmail.com>
 * @copyright   (C) 2024 Nicolò Tarter
 * @license     GPL-3.0+ <https://www.gnu.org/licenses/gpl-3.0.html>
 */

//************
//Dettagli FAD (dettagliFAD.php)
//************

function aperturaFAD(IDFAD, tipoVisualizzazione) {
    document.getElementById("tipoVisualizzazione" + IDFAD).value = tipoVisualizzazione;
    document.getElementById("formVisualizzazioneFAD" + IDFAD).submit();
}

//************
//Visualizza FAD (visualizzaFAD.php)
//************

function iniziaConteggioFAD(finestraEsclusiva) {
    //Salva l'ora di inizio:
    var orarioInizio = Date.now();
    //Imposta la NavBar di colore verde e mostra il pulsante per stoppare il conteggio delle ore
    document.getElementById("divNavBarFAD").style = "background-color: green";
    document.getElementById("iniziaConteggio").style = "display:none";
    document.getElementById("terminaConteggio").style = "display:inline-block";
    if (finestraEsclusiva === "1") {
        //Metti a schermo intero:
        let fad = document.body;
        fad.requestFullscreen();
    } else {
        //Prepara il pulsante per fermare il timer in modo che possa gestire la rendicontazione della FAD quando essa è impostata in modalità non esclusiva:
        function salvaConteggioFinestraNonEsclusiva() {
            terminaConteggioFAD(orarioInizio);
        }
        document.getElementById("terminaConteggio").onclick = salvaConteggioFinestraNonEsclusiva;
    }
    //Mostra lo scorrimento del tempo (timer)
    setInterval(function() {
        var tempoTrascorso = Date.now() - orarioInizio;
        var secondiTrascorsi = Math.round(tempoTrascorso/1000);
        var secondi = secondiTrascorsi % 60;
        var minuti = (secondiTrascorsi - secondi) / 60;
        document.getElementById("ore").innerText = String(minuti) + ":" + String(secondi);
        if (finestraEsclusiva === "1") {
            //Se esci dallo schermo intero, termina il conteggio delle ore:
            if (!document.fullscreenElement) {
                terminaConteggioFAD(orarioInizio);
            }
            //Se esci dalla finestra, termina il conteggio delle ore:
            window.onblur = function () {
                terminaConteggioFAD(orarioInizio);
            }
        }
    }, 100)
}

function chiudiSchermoIntero() {
    document.exitFullscreen();
}

function terminaConteggioFAD(orarioInizio) {
    //Salva l'orario della fine:
    var orarioFine = Date.now();
    //Ferma lo scorrimento che viene mostrato a schermo:
    document.getElementById("ore").id = "";
    //Imposta la NavBar di colore arancione e mostra la scritta di salvataggio del conteggio:
    document.getElementById("divNavBarFAD").style = "background-color: orange";
    document.getElementById("terminaConteggio").style = "display:none";
    document.getElementById("salvataggioConteggio").style = "display:inline-block";
    document.getElementById("conteggioDatabase").value = Math.round(Math.round((orarioFine - orarioInizio) / 1000) / 60);
    document.getElementById("formConteggioDatabase").submit();
}

//************
//Le tue FAD (leTueFAD.php)
//************

function aggiungiCartella(tipoCartella, cartellaPrincipale, materia, classe) {
    var nomeCartella = prompt("Inserisci il nome della cartella:");
    if (nomeCartella.length >= 1) {
        document.getElementById("nomeCartella").value = nomeCartella;
        document.getElementById("tipoCartella").value = tipoCartella;
        document.getElementById("cartellaPrincipale").value = cartellaPrincipale;
        document.getElementById("materia").value = materia;
        document.getElementById("classe").value = classe;
        document.getElementById("creaCartella").submit();
    } else {
        alert("L'operazione è stata interrotta.");
    }
}

function cancellaCartella(IDCartella) {
    var confermaCancellazioneCartella = prompt("Sei sicuro di voler cancellare questa cartella?\nAssieme a questa cartella verranno cancellati anche tutti i contenuti (dunque eventuali file e/o sottocartelle).\nQuesta azione è irreversibile.\nDigita \"confermo\" per confermare.");
    if (confermaCancellazioneCartella === "confermo") {
        document.getElementById("IDCartella").value = IDCartella;
        document.getElementById("cancellaCartella").submit();
    } else {
        alert("L'operazione è stata interrotta.");
    }
}

function cancellaFAD(IDFAD) {
    var confermaCancellazioneFAD = prompt("Sei sicuro di voler cancellare questa FAD?\nQuesta azione è irreversibile e verranno cancellate anche le ore rendicontate dagli studenti.\nDigita \"confermo\" per confermare.");
    if (confermaCancellazioneFAD === "confermo") {
        document.getElementById("IDFAD").value = IDFAD;
        document.getElementById("cancellaFAD").submit();
    } else {
        alert("L'operazione è stata annullata.");
    }
}

function visualizzaAnteprimaDocentiFAD(annoScolastico, classe, materia, IDFAD, estensioneFAD) {
    location.replace("FAD/" + annoScolastico + "/" + classe + "/" + materia + "/" + IDFAD + "." + estensioneFAD);
}

function apriDettagliCartellaFAD(IDFAD) {
    document.getElementById("IDCartella").value = IDFAD;
    document.getElementById("apriDettagliCartellaStudente").submit();
}

function apriCartella(idDiv) {
    document.getElementById(idDiv).style = "display: none";
    document.getElementById(idDiv).onclick = function() {chiudiCartella(idDiv);};
}

function chiudiCartella(idDiv) {
    document.getElementById(idDiv).style = "display: inline-block";
    document.getElementById(idDiv).onclick = function () {apriCartella(idDiv);};
}

//************
//Apertura/chiusura div con mainDiv (dettagliFAD.php, Amministrazione/gestisciClassi.php, Amministrazione/gestisciStudenti.php)
//************

function mostraDiv(idMainDiv, idDiv) {
    document.getElementById(idDiv).style = "display: block";
    document.getElementById(idMainDiv).onclick = function () {chiudiDiv(idMainDiv, idDiv);};
}

function chiudiDiv(idMainDiv, idDiv) {
    document.getElementById(idDiv).style = "display: none";
    document.getElementById(idMainDiv).onclick = function () {mostraDiv(idMainDiv, idDiv);};
}

//************
//Amministrazione - Gestisci classi (Amministrazione/gestisciStudenti.php)
//************

function assegnaDocente(IDMateria) {
    var emailDocente = prompt("Inserisci l'email del docente da assegnare alla materia.\nAttenzione!\nVerificare di inserire l'email correttamente!");
    if (emailDocente.length >= 1) {
        document.getElementById("email" + IDMateria).value = emailDocente;
        document.getElementById("formAssegnazioneDocente" + IDMateria).submit();
    } else {
        alert("L'operazione è stata interrotta.");
    }
}

function assegnaMonteOreAnnuale(IDMateria) {
    var monteOreAnnuale = prompt("Inserisci il monteore annuale per questa materia in minuti:");
    if (monteOreAnnuale >= 1) {
        document.getElementById("monteOreAnnuale" + IDMateria).value = monteOreAnnuale;
        document.getElementById("formAssegnazioneMonteOre" + IDMateria).submit();
    } else {
        alert("L'operazione è stata interrotta.");
    }
}

function aggiungiClasse() {
    location.replace("aggiungiClasse.php");
}

//************
//Amministrazione - Gestisci studenti (Amministrazione/gestisciStudenti.php)
//************

function aggiungiStudenti(classe) {
    var emailStudente = prompt("Inserisci l'email dello studente.\nAttenzione!\nVerificare di inserire l'email correttamente!");
    if (emailStudente.length >= 1) {
        document.getElementById("email" + classe).value = emailStudente;
        document.getElementById("form" + classe).submit();
    } else {
        alert("L'aggiunta degli studenti è stata interrotta.");
    }
}

function rimuoviStudente(IDStudente) {
    var confermaRimozioneStudente = prompt("Sei sicuro di voler rimuovere lo studente?\nDigita \"CoNfErMo\" per confermare l'operazione.");
    if (confermaRimozioneStudente === "CoNfErMo") {
        document.getElementById("rimuovi" + IDStudente).submit();
    } else {
        alert("L'operazione è stata interrotta.");
    }
}

//************
//Amministrazione - Anno Scolastico (Amministrazione/annoScolastico.php)
//************

function nuovoAnnoScolastico() {
    var nuovoAnno = prompt("Sei sicuro di voler creare un nuovo anno scolastico?\nCreando un nuovo anno scolastico, quello precedente verrà impostato in sola visualizzazione!\nInserisci il nuovo anno scolastico nel formato XXXX-YYYY.");
    if (nuovoAnno.length === 9) {
        document.getElementById("nuovoAnnoScolastico").value = nuovoAnno;
        document.getElementById("formCambioAnnoScolastico").submit();
    } else {
        alert("La creazione del nuovo anno scolastico è stata interrotta.");
    }
}