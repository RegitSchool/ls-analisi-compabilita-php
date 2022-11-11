<?php
require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma é distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$note = stringa_html('note');
$risposta = stringa_html('risposta');
if ($risposta==2)
    $note=str_replace("(ONLINE)", "", $note);
$idprenotazione = stringa_html('idprenotazione');
$titolo = "Variazione appuntamento";
$script = "";
stampa_head($titolo, "", $script, "SDP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='orario.php'>Orario</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore: " . mysqli_error($con));


$query = "update tbl_prenotazioni
                  set conferma=$risposta,note='$note'
                  where idprenotazione=$idprenotazione";

$ris = eseguiQuery($con, $query);


print ("<form method='post' action='../colloqui/visrichieste_doc.php' id='formdisp'>
   
        </form> 
      
        <SCRIPT language='JavaScript'>
            {
                document.getElementById('formdisp').submit();
            }
        </SCRIPT>  
      
       ");
mysqli_close($con);
stampa_piede("");

