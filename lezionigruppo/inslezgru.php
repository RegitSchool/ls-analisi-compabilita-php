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


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Inserimento lezione di gruppo";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


$ins = false;
$gio = stringa_html('gio');
$mese = stringa_html('mese');
$anno = stringa_html('anno');
$idlezionegruppo = stringa_html('idlezionegruppo');
$idgruppo = stringa_html('idgruppo');

$data = $anno . "-" . $mese . "-" . $gio;

$argomenti = elimina_apici(stringa_html('argomenti'));

$attivita = elimina_apici(stringa_html('attivita'));
$numeroore = stringa_html('orelezione');
$orainizio = stringa_html('orainizio');
$provenienza = stringa_html('provenienza');


$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


// INSERIMENTO, CANCELLAZIONE O UPDATE DATI LEZIONE   DA RIVEDERE PER INSERIMENTO PRESENZA
$ope = '';
$querylett = "select iddocente,idmateria from tbl_gruppi where idgruppo=$idgruppo";
$ris = eseguiQuery($con, $querylett);
$rec = mysqli_fetch_array($ris);
$iddocente = $rec['iddocente'];
$idmateria = $rec['idmateria'];
if ($idlezionegruppo != '')
{
    if ((($argomenti != "") | ($attivita != "")) | ($numeroore != ""))
    {
        $ope = 'U';
        $query1 = "update tbl_lezioni set numeroore='$numeroore',orainizio='$orainizio',argomenti='$argomenti',attivita='$attivita' where idlezionegruppo=$idlezionegruppo";
        $query2 = "update tbl_lezionigruppi set numeroore='$numeroore',orainizio='$orainizio',argomenti='$argomenti',attivita='$attivita' where idlezionegruppo=$idlezionegruppo";
    } else
    {
        $ope = 'D';
        $query1 = "delete from tbl_lezioni where idlezionegruppo=$idlezionegruppo";
        $query2 = "delete from tbl_lezionigruppi where idlezionegruppo=$idlezionegruppo";
    }
} else
{
    $ope = 'I';
    $query1 = "insert into tbl_lezionigruppi(datalezione,idgruppo,numeroore,orainizio,argomenti,attivita) values ('$data','$idgruppo','$numeroore','$orainizio','" . elimina_apici($argomenti) . "','" . elimina_apici($attivita) . "')";
}

//
//  INSERIMENTO, AGGIORNAMENTO O CANCELLAZIONE FIRMA
//


if ($ope == 'I')
{
    // INSERISCO LA LEZIONE DEL GRUPPO
    $ris = eseguiQuery($con, $query1);
    $idlezionegruppo = mysqli_insert_id($con);  // RICAVO L'ID DELLA LEZIONE DI GRUPPO
    // RICAVO LE CLASSI DEL GRUPPO
    $query = "select distinct idclasse from tbl_gruppialunni,tbl_alunni
	         where tbl_gruppialunni.idalunno=tbl_alunni.idalunno
	         and idgruppo=$idgruppo";
    //print inspref($query);
    $ris = eseguiQuery($con, $query);
    //print mysqli_num_rows($ris);
    while ($rec = mysqli_fetch_array($ris))
    {
        $idclasse = $rec['idclasse'];
        // Inserimento lezione
        $queryverlez = "select * from tbl_lezioni where idclasse=$idclasse and datalezione='$data' and numeroore=$numeroore and orainizio=$orainizio and idmateria=$idmateria";
        $risverlez = eseguiQuery($con, $queryverlez);
        if ($recverlez = mysqli_fetch_array($risverlez))
        {
            $idlezione = $recverlez['idlezione'];
            $queryupdlez = "update tbl_lezioni set idlezionegruppo=$idlezionegruppo,argomenti='$argomenti',attivita='$attivita' where idlezione=$idlezione";
            $ris2 = eseguiQuery($con, $queryupdlez);
        } else
        {
            $queryinslez = "insert into tbl_lezioni(idclasse,datalezione,iddocente,idmateria,idlezionegruppo,numeroore,orainizio,argomenti,attivita) values ('$idclasse','$data','$iddocente','$idmateria','$idlezionegruppo','$numeroore','$orainizio','$argomenti','$attivita')";
            $ris2 = eseguiQuery($con, $queryinslez);
            $idlezione = mysqli_insert_id($con);
        }
        // Inserimento firma
        $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$idlezione','$iddocente')";
        $ris3 = eseguiQuery($con, $queryinsfirma);
    }
    print "<center><b>Inserimento effettuato!</b></center>";
}
if ($ope == 'U')
{

    $ris3 = eseguiQuery($con, $query1);
    $ris4 = eseguiQuery($con, $query2);

    print "<center><b>Aggiornamento effettuato!</b></center>";
}
if ($ope == 'D')
{
    $ris3 = eseguiQuery($con, $query1);
    $ris4 = eseguiQuery($con, $query2);
    print "<center><b>Cancellazione effettuata!</b></center>";
}



//
// AGGIORNAMENTO DATI ALUNNI (ASSENZE E LEZIONI)


$query = "SELECT idalunno AS al FROM tbl_gruppialunni WHERE idgruppo=" . $idgruppo;
$risalu = eseguiQuery($con, $query);

while ($id = mysqli_fetch_array($risalu))
{

    $va = "oreass" . $id['al'];
    $assal = stringa_html($va);
    $idclasse = estrai_classe_alunno($id['al'], $con);
    // ricavo id lezione       // FARE IN MODO CHE TENGA CONTO DI EVENTUALI LEZIONI CANCELLATE TTTTTT
    $query = "select idlezione from tbl_lezioni where idlezionegruppo=$idlezionegruppo and idclasse=$idclasse";
    $rislez = eseguiQuery($con, $query);
    if ($rec = mysqli_fetch_array($rislez))  // SE LA LEZIONE PER LA CLASSE NON C'E' Si REINSERISCE
    {
        $idlezione = $rec['idlezione'];
    } else
    {
        $queryinslez = "insert into tbl_lezioni(idclasse,datalezione,iddocente,idmateria,idlezionegruppo,numeroore,orainizio,argomenti,attivita) values ('$idclasse','$data','$iddocente','$idmateria','$idlezionegruppo','$numeroore','$orainizio','$argomenti','$attivita')";
        $risins = eseguiQuery($con, $queryinslez);
        $idlezione = mysqli_insert_id($con);
        $queryinsfirma = "insert into tbl_firme(idlezione,iddocente) values ('$idlezione','$iddocente')";
        $risinsfirma = eseguiQuery($con, $queryinsfirma);
    }





    require '../lib/req_salva_voti.php';
    if (gestione_manuale_assenze($idclasse, $data, $con))
    {
        require '../lib/req_salva_assenze.php';
    }
}
if (!gestione_manuale_assenze($idclasse, $data, $con))
    ricalcola_assenze_lezioni_classe($con, $idclasse, $data);

echo "<p align='center'>";

print ('
			<form method="post" action="lezgru.php">
			<p align="center">');

// Se la lezione non è stata cancellata si passa il codice
if ($ope != 'D')
{
    print ('<p align="center"><input type=hidden value=' . $idlezionegruppo . ' name=idlezionegruppo>');
}

print('<input type="submit" value="OK" name="b"></p></form>');

mysqli_close($con);
stampa_piede("");

