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

//
//    VISUALIZZAZIONE DELLE ASSEMBLEE DI CLASSE PER I GENITORI
//	  E
//	  RICHIESTA DI ASSEMBLEE DI CLASSE PER GLI ALUNNI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Inoltro richiesta";
$script = "";
stampa_head_new($titolo, "", $script, "L");
$idclasse = stringa_html('idclasse');
stampa_testata_new("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - <a href='assricgen.php?idclasse=$idclasse'>Assemblee di classe</a> - <a href='ricgen.php?idclasse=$idclasse'>Richiesta assemblea di classe</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));
$orainizio = substr(stringa_html('oreass'), 0, 1);
$orafine = substr(stringa_html('oreass'), 2, 1);
$rap1 = stringa_html('rappresentante1');
//$rap2 = stringa_html('rappresentante2');
//$pres = stringa_html('alunnopresidente');
//$seg = stringa_html('alunnosegretario');
//$datarichiesta = stringa_html('datarichiesta');

$docenteconcedente1 = stringa_html('docenteconcedente1');
$docenteconcedente2 = stringa_html('docenteconcedente2');

$odg = stringa_html('odg');
$odgdef = stringa_html('odgdef');

if($odg == $odgdef){
    // Check se L'ordine del giorno non è stato modificato dalla textbox
    print("<div class='alert alert-danger mx-5' role='alert'>
        Impossibile proseguire! L'Ordine del Giorno non è stato impostato!
    </div>");
    die();
}

$dataassemblea = stringa_html('data');
$dataassemblea = data_to_db($dataassemblea);
$datarichiesta = date('Y-m-d');
//print $dataassemblea;
//$gg = substr($dataass,0,2);
//$mm = substr($dataass,3,2);
//$aaaa = substr($dataass,6,4);
//$dataassemblea = $aaaa."-".$mm."-".$gg;

$assq = "INSERT INTO tbl_assemblee(idclasse,datarichiesta,dataassemblea,orainizio,orafine,docenteconcedente1";
if ($docenteconcedente2 != "")
{
    $assq .= ",docenteconcedente2";
}


$assq .= ",rappresentante1,odg) VALUES ($idclasse,'$datarichiesta','$dataassemblea','$orainizio','$orafine',$docenteconcedente1";
if ($docenteconcedente2 != "")
{
    $assq .= ",$docenteconcedente2";
}

$assq .= ",$rap1,'$odg')";
if (!controlloAssemblee($idclasse, $dataassemblea, $con))
{
    print "<br/> <CENTER><b>Assemblea già effettuata per il mese indicato!</b></CENTER>";
} else
{
    $resq = eseguiQuery($con, $assq);
    if (!$resq)
    {
        print "<br/> <CENTER><b>Impossibile inserire richiesta nel database!</b></CENTER>";
        print "<br/> <CENTER>Controlla di aver compilato tutti i campi correttamente</CENTER>";
        print "<p align>" . $assq . "</p>";
    } else
    {

        print ("<form method='post' action='assricgen.php' id='formdisp'>
			
        </form> 
        <SCRIPT language='JavaScript'>
            {
                document.getElementById('formdisp').submit();
            }
        </SCRIPT>  
      
       ");
    }
}
mysqli_close($con);
stampa_piede_new("");

function controlloAssemblee($idclasse, $dataassemblea, $con)
{
    $meseassemblea = substr($dataassemblea, 5, 2);
    $query = "select count(*) as  numass from tbl_assemblee where idclasse=$idclasse and month(dataassemblea)=$meseassemblea and autorizzato<>2 and concesso1<>2";
    // print inspref($query);
    $ris = eseguiQuery($con, $query);
    $rec = mysqli_fetch_array($ris);
    if ($rec['numass'] == 0)
        return true;
    else
        return false;
}