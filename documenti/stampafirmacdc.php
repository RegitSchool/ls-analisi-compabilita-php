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
require_once("../lib/fpdf/fpdf.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

$iddocente = stringa_html('iddocente');

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Firme presenza cdc";
$script = "";

$schede = new FPDF();

// scelta classe se il docnete è coordinatore
$docente = stringa_html("docente");

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$condclassi = $condclassi = " true ";
if ($docente != "")
{
    $strclassi = "";
    $query = "select idclasse from tbl_classi where idcoordinatore=$docente";
    $ris = eseguiQuery($con, $query);
    while ($rec = mysqli_fetch_array($ris))
    {
        $strclassi .= $rec['idclasse'] . ",";
    }
    if ($strclassi != "")
        $condclassi = " idclasse in (" . substr($strclassi, 0, strlen($strclassi) - 1) . ")";
}


$queryclasse = "SELECT idclasse,idcoordinatore FROM tbl_classi WHERE $condclassi ORDER BY anno, specializzazione, sezione";
$riscla = eseguiQuery($con,$queryclasse);

while ($reccla = mysqli_fetch_array($riscla))
{
    $schede->AddPage();

    $idclasse = $reccla['idclasse'];
    $daticoordinatore = estrai_dati_docente($reccla['idcoordinatore'], $con);
    if ($_SESSION['suffisso'] == "")
        $suff = "";
    else
        $suff = $_SESSION['suffisso'] . "/";
    $schede->Image("../abc/$suff" . "testata.jpg", 20, 10, 172, 25);


    $denclasse = converti_utf8("Classe: " . decodifica_classe($idclasse, $con));
    // print $denclasse;
    $schede->SetFont('arial', '', 11);
    $schede->setXY(20, 40);
    $schede->Cell(172, 8, $denclasse, 0, 0, "C");
    $schede->setXY(20, 50);
    $intest = converti_utf8("Firme per presenza al consiglio di classe del _________________ Ora inizio ______  Ora fine ______ ");
    $schede->Cell(172, 8, $intest, NULL, 1, "C");
    $schede->setXY(20, 60);
    $schede->SetFont('arial', '', 10);
    $intest = converti_utf8("DOCENTE");
    $schede->Cell(76, 8, $intest, "TBLR", 1, "C");
    
    $schede->setXY(96, 60);
    $intest = converti_utf8("INGR.");
    $schede->Cell(20, 8, $intest, "TBLR", 1, "C");
    
    $schede->setXY(116, 60);
    $intest = converti_utf8("USC. ");
    $schede->Cell(20, 8, $intest, "TBLR", 1, "C");
    
    $schede->setXY(136, 60);
    $intest = converti_utf8("FIRMA");
    $schede->Cell(56, 8, $intest, "TBLR", 1, "C");


   

    $query = "select distinct cognome, nome, tbl_cattnosupp.iddocente,tbl_cattnosupp.idalunno from tbl_cattnosupp,tbl_docenti
               where tbl_cattnosupp.iddocente=tbl_docenti.iddocente
               and tbl_cattnosupp.idclasse=$idclasse
               and tbl_cattnosupp.iddocente!=1000000000
               
               order by cognome, nome";
    
    $ris = eseguiQuery($con, $query);
    $posY = 68;
    while ($nom = mysqli_fetch_array($ris))
    {


        $schede->setXY(20, $posY);
        $schede->SetFont('arial', 'B', 10);

        $intest = converti_utf8($nom['cognome']." ".$nom['nome']);
        $schede->Cell(76, 6, $intest, "TLR", 1, "");
        $docenti = "";
        if ($idclasse != "")
        {
            //print "<br>";
            if ($nom['idalunno']==0)
            {
                $query = "select tbl_cattnosupp.idmateria from tbl_cattnosupp,tbl_materie
                            where tbl_cattnosupp.idclasse=$idclasse and tbl_cattnosupp.iddocente=" . $nom['iddocente'] .
                        " and idalunno=0 and tbl_cattnosupp.iddocente<>1000000000 and tbl_cattnosupp.idmateria=tbl_materie.idmateria order by denominazione";
                // print inspref($query);
                $rismat = eseguiQuery($con, $query);
                $docenti = "";
                while ($recmat = mysqli_fetch_array($rismat))
                {
                    $docenti .= converti_utf8(decodifica_sigla_materia($recmat['idmateria'], $con) . "   ");
                }
            }
            else
                $docenti="Sostegno";
            
        } 
        $posY += 6;
        $schede->SetFont('arial', '', 8);
        $schede->setXY(20, $posY);
        $schede->Cell(76, 6, $docenti, "BLR", 1, "");
        $posY -= 6;

        $schede->setXY(96, $posY);
        $schede->Cell(20, 12, "", "TBLR", 1, "");
        
        $schede->setXY(116, $posY);
        $schede->Cell(20, 12, "", "TBLR", 1, "");
        
        $schede->setXY(136, $posY);
        $schede->Cell(56, 12, "", "TBLR", 1, "");
        $posY += 12;
        
        
    }

    $posY += 4;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, "Il coordinatore di classe", "", 1, "C");
    $posY += 4;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, converti_utf8("(" . $daticoordinatore . ")"), "", 1, "C");
    $posY += 7;
    $schede->setXY(96, $posY);
    $schede->Cell(96, 7, "____________________________________", "", 1, "C");
}
$schede->Output("firme.pdf", "I");
mysqli_close($con);
//stampa_piede("",false); 


