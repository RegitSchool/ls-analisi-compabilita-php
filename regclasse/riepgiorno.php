<?php

// require_once '../lib/req_apertura_sessione.php';
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

require_once '../php-ini' . $_SESSION['suffisso'] . '.php';
require_once '../lib/funzioni.php';
require_once '../lib/funregi.php';

// istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione

if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Riepilogo registro di classe giornata";
$script = "<script type='text/javascript'>
         <!--
               var stile = 'top=10, left=10, width=1024, height=400, status=no, menubar=no, toolbar=no, scrollbars=yes';
               function Popup(apri)
               {
                  window.open(apri, '', stile);
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

$giorno = '';
$meseanno = '';
$anno = '';
$mese = '';
$idclasse = '';

// MODIFICA ANTE TOKEN
$iddocente = $_SESSION['idutente'];
// FINE MODIFICA ANTE TOKEN


$giorno = stringa_html('gio');
//$orainizio = stringa_html('orainizio');
$meseanno = stringa_html('meseanno');
$idclasse = stringa_html('idclasse');


// Le variabili di sessione servono agli altri programmmi a stabilire che si proviene
// dal registro per poter fare automaticamente ritorno qui.

$_SESSION['prove'] = 'riepgiorno.php';
$_SESSION['regcl'] = $idclasse;
$_SESSION['regma'] = $meseanno;
$_SESSION['reggi'] = $giorno;

$_SESSION['classeregistro'] = $idclasse;

$anno = substr($meseanno, 5, 4);
$mese = substr($meseanno, 0, 2);

$giornosettimana = "";

// Impostazione variabili per giorno attuale - pulsante OGGI
$gioattuale = date('d');
$meannoattuale = date('m - Y');

if ($giorno == '')
{
    $giorno = date('d');
}
if ($mese == '')
{
    $mese = date('m');
}
if ($anno == '')
{
    $anno = date('Y');
}

print ('
         <form method="post" action="riepgiorno.php" name="voti">
   
         <p align="center">
         <table align="center">');

print (' <tr>
         <td width="50%"><b>Data (gg/mm/aaaa)</b></td>');


//
//   Inizio visualizzazione della data
//


echo('   <td width="50%">');
echo('   <select name="gio" ONCHANGE="voti.submit()">');
require '../lib/req_aggiungi_giorni_a_select.php';

echo("</select>");

echo('   <select name="meseanno" ONCHANGE="voti.submit()">');
require '../lib/req_aggiungi_mesi_a_select.php';
echo("</select>");


//
//  Fine visualizzazione della data
//


echo("        
      </td></tr>");


//
//   Classi
//
print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%"> 
        <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="voti.submit()"><option value="">&nbsp;');

//
//  Riempimento combobox delle classi
//

require '../lib/req_aggiungi_classi_a_select.php';

echo('
      </SELECT>
      </td></tr>');

echo('</table>');
echo ('</form>');

$dataoggi = "$anno-$mese-$giorno";
$datadomani = aggiungi_giorni($dataoggi, 1);
$dataieri = aggiungi_giorni($dataoggi, -1);
if (giorno_settimana($dataieri) == "Dom")
{
    if ($_SESSION['giornilezsett'] == 6)
        $dataieri = aggiungi_giorni($dataieri, -1);
    else
        $dataieri = aggiungi_giorni($dataieri, -2);
}
if (giorno_settimana($datadomani) == "Dom" | (giorno_settimana($datadomani) == "Sab" & $_SESSION['giornilezsett'] == 5 ))
{

    if ($_SESSION['giornilezsett'] == 6)
        $datadomani = aggiungi_giorni($datadomani, +1);
    else
        $datadomani = aggiungi_giorni($datadomani, +2);
}
$gioieri = substr($dataieri, 8, 2);
$giodomani = substr($datadomani, 8, 2);
$maieri = substr($dataieri, 5, 2) . " - " . substr($dataieri, 0, 4);
$madomani = substr($datadomani, 5, 2) . " - " . substr($datadomani, 0, 4);

print "<br><center>";
if ($dataieri >= $_SESSION['datainiziolezioni'])
    print ("<a href='riepgiorno.php?gio=$gioieri&meseanno=$maieri&idclasse=$idclasse'><img src='../immagini/indietro.png'></a>");
print ("&nbsp;&nbsp;&nbsp;");
if ($datadomani <= $_SESSION['datafinelezioni'])
    print ("<a href='riepgiorno.php?gio=$gioattuale&meseanno=$meannoattuale&idclasse=$idclasse'><img src='../immagini/today.png'></a> &nbsp;
            <a href='riepgiorno.php?gio=$giodomani&meseanno=$madomani&idclasse=$idclasse'><img src='../immagini/avanti.png'></a>");
print "</center>";

//       <table align="center">
//       <td>');
//     //     <p align="center"><input type="submit" value="Visualizza voti" name="b"></p>
//echo('</td></table>');
//echo('</form><hr>');
echo('<hr>');

if ($mese == "")
{
    $m = 0;
} else
{
    $m = $mese;
}
if ($giorno == "")
{
    $g = 0;
} else
{
    $g = $giorno;
}

if ($anno == "")
{
    $a = 0;
} else
{
    $a = $anno;
}

$giornosettimana = giorno_settimana($anno . "-" . $mese . "-" . $giorno);



if (!checkdate($m, $g, $a))
{
    print ("<Center> <big><big>Data non corretta!</big></big> </center>");
} else
{
    if ($giornosettimana == "Dom")
    {
        print ("<Center> <big><big>Il giorno selezionato &egrave; una domenica!</big></big> </center>");
    }
   else
    {
        if (($idclasse != ""))
        {

            $newdate = $a . "-" . $m . "-" . $g;
            if ($newdate >= $_SESSION['datainiziolezioni'] & $newdate <= $_SESSION['datafinelezioni'] & (!giorno_festa($newdate, $con)))
                stampa_reg_classe($newdate, $idclasse, $iddocente, $_SESSION['numeromassimoore'], $con, true, $_SESSION['gestcentrassenze'], $_SESSION['giustificauscite']);
            else
            if (giorno_festa($newdate, $con))
                print "<b><br><center><font color='red'>" . data_italiana($newdate) . " - " . estrai_festa($newdate, $con) . "</font></center><br>";
        }
    }
}

mysqli_close($con);
stampa_piede("");

