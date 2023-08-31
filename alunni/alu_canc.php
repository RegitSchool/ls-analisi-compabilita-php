<?php

require_once '../lib/req_apertura_sessione.php';

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */


//sdsada

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login 

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

/* cancellazione di un alunno	
  parametri di ingresso: codice dell'alunno, codice della classe
  parametri di uscita:codice della classe */

$titolo = "Cancellazione alunno";
$script = "";
stampa_head($titolo, "", $script, "MASP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - Cancellazione alunno", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
}

//connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
}
$c = stringa_html('idal');
$idcla = stringa_html('idcla');
//$sql="DELETE FROM tbl_tutori WHERE idtutore='$c'";
//esecuzione query
//$res=eseguiQuery($con,$sql);
$sql = "DELETE FROM tbl_utenti WHERE idutente='$c'";
//esecuzione query
$res = eseguiQuery($con, $sql);
$utal=$c+2100000000;
$sql = "DELETE FROM tbl_utenti WHERE idutente='$utal'";
//esecuzione query
$res = eseguiQuery($con, $sql);
$sql = "DELETE FROM tbl_alunni WHERE idalunno='$c'";
//esecuzione query
$res = eseguiQuery($con, $sql);

//ad 
$utentealunno = "al" . $_SESSION['suffisso'] . $c;

if($_SESSION['adautosync_disabled'] == "no" && $_SESSION['ad_module_enabled'] == "yes" && $_SESSION['adgroup_alunni'] != "" && $_SESSION['adgroup_alunni'] != null && $_SESSION['gestioneutentialunni'] == 'yes') {
    $queue = array();
    queueDeleteOperation($queue, $utentealunno);
    sendQueueToBroker($queue, $_SESSION['broker_host'], $_SESSION['broker_port'], $_SESSION['broker_user'], $_SESSION['broker_pass'], $_SESSION['broker_topic']);
}

if (!$res)
{
    print ("<br/> <br/> <br/> <h2> Impossibile cancellare i dati </h2>");
    print(" <form action='vis_alu.php' method='POST'>");
    print ("<input type ='hidden' name='idcla' value='$idcla'>");
    print (" <input type='submit' value=' << Indietro'> </form>  ");
} else
{
    print ("<center>");
    print (" Dati cancellati ");
    print ("</center>");
    print (" \n <form action='vis_alu.php' method='POST'>");
    print ("<center>");
    print ("\n <input type='hidden' value='$idcla' name='idcla'>");
    print ("\n <input type='submit' value=' << Indietro'>");
    print ("</center>");
    print ("\n </form>");
}
mysqli_close($con);
stampa_piede("");


