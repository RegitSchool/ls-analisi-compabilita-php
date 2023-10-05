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

/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco parametri";
$script = "";
stampa_head_new($titolo, "", $script, "PMSD");
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


//
//    Fine parte iniziale della pagina
//
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome);
if (!$con)
{
    print("<h1> Connessione al server fallita </h1>");
    exit;
}

//Connessione al database
$DB = true;
if (!$DB)
{
    print("<h1> Connessione al database fallita </h1>");
    exit;
}

//Esecuzione query
$query = "SELECT * FROM tbl_parametri
	        WHERE parametro NOT IN ('versioneprecedente','editorhtml','numeroperiodi','finesecondo','sola_lettura','passwordesame')
	        ORDER BY gruppo,parametro";
if (!($ris = eseguiQuery($con, $query)))
{
    print "\nQuery fallita";
} else
{
    alert("ATTENZIONE! Non modificare i parametri se non si è consapevoli delle conseguenze!", "", "danger", "radioactive");
    print "<CENTER><table class='table table-striped table-bordered'>";
    //print "<TR class='prima'><TD ALIGN='CENTER'><B>Parametro</B></TD><TD ALIGN='CENTER'><B>Significato</B></TD><TD ALIGN='CENTER'><B>Valore</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
    print "<TR class='prima'><TD ALIGN='CENTER'><B>Gruppo</B></TD><TD ALIGN='CENTER'><B>Parametro</B></TD><TD ALIGN='CENTER'><B>Descrizione</B></TD><TD ALIGN='CENTER'><B>Valore</B></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
    while ($dati = mysqli_fetch_array($ris))
    {
        if ($dati['parametro'] != "chiaveuniversale")
        {
            print "<TR class='oddeven'><TD>" . $dati['gruppo'] . "</TD><TD>" . $dati['parametro'] . "</TD><TD>" . $dati['descrizione'] . "</TD><TD>" . $dati['valore'] . "</TD><TD><A HREF='mod_par.php?idpar=" . $dati['idparametro'] . "'><img src='../immagini/edit.png'></A></TD>";
        } else
        {
            print "<TR class='oddeven'><TD>" . $dati['gruppo'] . "</TD><TD>" . $dati['parametro'] . "</TD><TD>" . $dati['descrizione'] . "</TD><TD>*****</TD><TD><A HREF='mod_par.php?idpar=" . $dati['idparametro'] . "'><img src='../immagini/edit.png'></A></TD>";
        }
    }
    print "</CENTER></TABLE>";
}


stampa_piede_new("");
mysqli_close($con);




