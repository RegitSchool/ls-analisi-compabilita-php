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
//    VISUALIZZAZIONE DELLE VALUTAZIONI 
//    PER I GENITORI 
//


@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

//  istruzioni per tornare alla pagina di login se non c'è una sessione valida

$tipoutente = $_SESSION["tipoutente"];
$idutente = $_SESSION['idutente']; //prende la variabile presente nella sessione
if ($tipoutente == "") {
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}


$titolo = "Visualizzazione situazione alunno";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);

$idalunno = stringa_html('idalunno');
$periodo = stringa_html('periodo');
$idclasse = stringa_html('idclasse');
$cambiamentoclasse = false;

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));

// LA QUERY SEGUENTE E' FATTA IN MODO TALE CHE VENGANO VISUALIZZATI
// ANCHE ALUNNI SENZA CLASSE PER VEDERE LA SITUAZIONE DEGLI ALUNNI
// TRASFERITI

$queryclasse = "SELECT * FROM tbl_classi";
if ($tipoutente == 'D')
    $queryclasse = "SELECT * FROM tbl_classi
             WHERE idcoordinatore=$idutente";




/*

  $query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
  ON tbl_alunni.idclasse=tbl_classi.idclasse
  ORDER BY cognome,nome,anno, sezione, specializzazione";
  if ($tipoutente=='D')
  $query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
  ON tbl_alunni.idclasse=tbl_classi.idclasse
  WHERE tbl_alunni.idclasse IN (SELECT idclasse from tbl_classi WHERE idcoordinatore=$idutente)
  ORDER BY cognome,nome,anno, sezione, specializzazione
  ";
 * 
 */
//$ris = eseguiQuery($con, $queryclasse);
//print "tttt ".inspref($query);
print "<form name='selealu' action='visvalpre.php' method='post'>";
print "<table align='center'>";
// SELEZIONE IN BASE A CLASSE DEGLI ALUNNI DA MOSTRARE

PRINT "
   <tr>
      <td width='20%'>Classe</td>
      <td width='50%'>
      <SELECT ID='cl' NAME='idclasse' ONCHANGE='selealu.submit()'>
      <option value=''>&nbsp;  ";

// Riempimento combo box tbl_classi
/*
  if ($tipoutente == 'S' | $tipoutente == 'P')
  {
  $query = 'SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi ORDER BY specializzazione, sezione, anno';
  } else
  {
  $query = 'SELECT idclasse,anno,sezione,specializzazione FROM tbl_classi
  WHERE idclasse in (select distinct idclasse from tbl_cattnosupp where iddocente=$idutente)
  ORDER BY specializzazione, sezione, anno';
  }
 * 
 */

$ris = eseguiQuery($con, $queryclasse);
while ($nom = mysqli_fetch_array($ris)) {
    print "<option value='";
    print ($nom['idclasse']);
    print "'";
    if ($idclasse == $nom['idclasse']) {
        print ' selected';
    }
    print '>';
    print ($nom['anno']);
    print '&nbsp;';
    print($nom['sezione']);
    print '&nbsp;';
    print($nom['specializzazione']);
}

if ($tipoutente != 'D') {
    print "<option value='999999'";
    print ($nom['idclasse']);
    print "'";
    if ($idclasse == 999999) {
        print ' selected';
    }
    print '>SENZA CLASSE';
}


echo('
      </SELECT>
      </td></tr>');

if ($idclasse != "0" & $idclasse != "") {

    if ($idclasse == 999999)
        $idclasse = 0;
    $query = "SELECT * FROM tbl_alunni WHERE idclasse=$idclasse";
    $ris = eseguiQuery($con, $query);
    print "<tr><td>Alunno</td>";
    print "<td>";
    print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>&nbsp;</option>";
    while ($rec = mysqli_fetch_array($ris)) {
        if ($idalunno == $rec['idalunno']) {
            $sele = " selected";
        } else {
            $sele = "";
        }
        print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ")"); // . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</option>");
    }
    print "
 </select> 
 </td>
 
 </tr>";
    if ($periodo == 'tutti' | $periodo == '')
        $seltutti = ' selected';
    if ($periodo == 'primo')
        $selprimo = ' selected';
    if ($periodo == 'secondo')
        $selsecondo = ' selected';
    print "<tr><td>Periodo</td><td><select name='periodo' ONCHANGE='selealu.submit();'>";
    print "<option value='tutti'$seltutti>Tutti</option>";
    print "<option value='primo'$selprimo>Primo</option>";
    print "<option value='secondo'$selsecondo>Secondo</option>";
    print "</select></tr>";

// IMPOSTO LA SELEZIONE SULLA DATA
// 
// 

    $seledata = '';
    if ($periodo == 'primo')
        $seledata = "and data<='" . $_SESSION['fineprimo'] . "'";
    if ($periodo == 'secondo')
        $seledata = "and data>'" . $_SESSION['fineprimo'] . "'";
} else
    $idalunno = "";

print "</table></form>";
// prelevamento dati alunno

if ($idalunno != '') {
    $query = "select * from tbl_alunni where idalunno=$idalunno";
    $ris = eseguiQuery($con, $query);

    if ($val = mysqli_fetch_array($ris)) {
        echo '<center><b><br>Situazione dell\'Alunno: ' . $val["cognome"] . ' ' . $val["nome"] . '</b></center><br/>';
        $idclasse = $val['idclasse'];
    }

    if ($tipoutente != "A") {
        // prelevamento voti
        $query = "select * from tbl_valutazioniintermedie, tbl_materie where tbl_valutazioniintermedie.idmateria=tbl_materie.idmateria and idalunno=$idalunno $seledata order by denominazione, data desc";
        $ris = eseguiQuery($con, $query);
        // print $query;
        if (mysqli_num_rows($ris) > 0) {
            print ("<table border=1 align=center><tr><td>Data</td><td align=center>Tipo<br/>valutazione</td><td align=center>Voto</td><td>Giudizio</td></tr>");
            $mat = "";
            while ($val = mysqli_fetch_array($ris)) {
                $materia = $val['denominazione'];
                $idmateria = $val['idmateria'];
                $data = data_italiana($val['data']);
                $tipo = $val['tipo'];
                if ($tipo == 'O')
                    $tipo = 'Orale';
                if ($tipo == 'S')
                    $tipo = 'Scritta';
                if ($tipo == 'P')
                    $tipo = 'Pratica';
                $voto = dec_to_mod($val['voto']);
                $giudizio = $val['giudizio'];
                if ($giudizio == "" | substr($giudizio, 0, 1) == "(")
                    $giudizio = "&nbsp;";


                if ($materia != $mat) {
                    $mat = $materia;
                    $querycontaore = "select sum(oreassenza) as totassmateria from tbl_asslezione where idalunno=$idalunno and idmateria=$idmateria $seledata";
                    //print inspref($querycontaore);
                    $risasslez = eseguiQuery($con, $querycontaore);
                    $recassmat = mysqli_fetch_array($risasslez);
                    $oreassenzamateria = $recassmat['totassmateria'] != 0 ? $recassmat['totassmateria'] : 0;

                    print("<tr class=prima><td colspan=4 align=center>$materia (Assenze $oreassenzamateria h.)</td></tr>");
                }

                if ($voto != "&nbsp;&nbsp;" | $giudizio != "&nbsp;") {
                    $colore = 'white';
                    if ($val['idclasse'] != $idclasse) {
                        $colore = 'grey';
                        $cambiamentoclasse = true;
                    }
                    print("<tr bgcolor=$colore>");
                    print("<td>$data</td>");
                    if ($voto >= 6)
                        $colore = 'green';
                    else
                        $colore = 'red';
                    print("<td align=center>$tipo</td>");
                    print("<td align=center><font color='$colore'><b>$voto</b></font></td>");
                    print("<td>$giudizio</td>");
                    print("</tr>");
                }
            }
            print ("</table><br/>");
            if ($cambiamentoclasse) {
                print ("<center><font color='grey'>Le valutazioni con sfondo grigio sono state attribuite in una classe diversa da quella di attuale appartenenza.</font></center>");
            }
        } else {
            print("<br/><big><big><center>Non ci sono voti registrati!</center><small><small><br/>");
        }

        // SITUAZIONE ASSENZE
    }

    print "<br>";

    $rs1 = eseguiQuery($con, "select * from tbl_alunni where idalunno=$idalunno");
    $rs2 = eseguiQuery($con, "select count(*) as numerotblassenze from tbl_assenze where idalunno=$idalunno $seledata");
    $rs3 = eseguiQuery($con, "select count(*) as numerotblritardi from tbl_ritardi where idalunno=$idalunno $seledata");
    $rs4 = eseguiQuery($con, "select count(*) as numerouscite from tbl_usciteanticipate where idalunno=$idalunno $seledata");
    $rs5 = eseguiQuery($con, "select * from tbl_assenze where idalunno=$idalunno $seledata order by data desc");
    $rs6 = eseguiQuery($con, "select * from tbl_ritardi where idalunno=$idalunno $seledata order by data desc");
    $rs7 = eseguiQuery($con, "select * from tbl_usciteanticipate where idalunno=$idalunno $seledata order by data desc");
    $query = "select sum(oreassenza) as numerooreassdad from tbl_asslezione where idalunno=$idalunno and data not in (select data from tbl_assenze where idalunno=$idalunno) and data in (select datadad from tbl_dad where idclasse=$idclasse)";
//print inspref($query);
    $rs8 = eseguiQuery($con, $query);
    $rs9 = eseguiQuery($con, "select * from tbl_asslezione where idalunno=$idalunno $seledata"
            . " and data not in (select data from tbl_assenze where idalunno=$idalunno)"
            . " and data in (select datadad from tbl_dad where idclasse=$idclasse)"
            . " order by data ");

    // print "<center><i>Dati aggiornati al ".data_italiana($ultimoaggiornamento).".</i></center>
    print "<table border='1' align='center'>";

    // prelevamento dati alunno

    /* 	if ($rs1) {

      if ($val1 = mysqli_fetch_array($rs1))
      echo '
      <tr class="prima">
      <td colspan="3"><b>Alunno: '. $val1["cognome"]. ' '. $val1["nome"]. '</b></td>
      </tr>';
      }
     */
    // conteggio tbl_assenze

    if ($val2 = mysqli_fetch_array($rs2)) {
        echo '
	 <tr class="prima">
	  <td colspan="3"><b>Assenze: ' . $val2["numerotblassenze"] . '</b></td>
	 </tr>';
    }

    // conteggio tbl_ritardi

    if ($rs3) {

        if ($val3 = mysqli_fetch_array($rs3)) {
            echo '
	 <tr class="prima">
	  <td colspan="3"><b>Ritardi: ' . $val3["numerotblritardi"] . '</b></td>
	 </tr>';
        }
    }

    // conteggio uscite anticipate

    if ($val4 = mysqli_fetch_array($rs4)) {
        echo '
	 <tr class="prima">
	  <td colspan="3"><b>Uscite anticipate: ' . $val4["numerouscite"] . '</b></td>
	 </tr>';
    }

    print "
	 <tr class='prima'><td width='33%'>Assenze</td><td width='33%'>Ritardi</td><td width='33%'>Uscite</td></tr>";

    // elenco tbl_assenze
    echo "
	 <tr><td valign='top'>";

    if ($rs5) {

        while ($val5 = mysqli_fetch_array($rs5)) {
            $data = $val5["data"];
            echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . '<br/>';
        }
    }
    echo "</td>";

    // elenco tbl_ritardi
    echo "<td valign='top'>";

    if ($rs6) {

        while ($val6 = mysqli_fetch_array($rs6)) {
            $data = $val6["data"];
            echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . '<br/>';
        }
    }
    echo "</td>";

    // elenco uscite
    echo "<td valign='top'>";

    if ($rs7) {

        while ($val7 = mysqli_fetch_array($rs7)) {
            $data = $val7["data"];
            echo ' ' . data_italiana($data) . ' ' . giorno_settimana($data) . '<br/> ';
            $query = "select * from tbl_autorizzazioniuscite where idalunno=$idalunno and data='$data'";
            $ris = eseguiQuery($con, $query);
            if ($rec = mysqli_fetch_array($ris))
                print "<small>" . $rec['testoautorizzazione'] . "</small><br>";
        }
    }
    echo '
		  </td>
		 </tr>
		';

    if ($rs9) {
        print "<tr><td colspan=3>";
        print "<center><b>Assenze a lezioni in didattica a distanza</b></center><br>";
        while ($val9 = mysqli_fetch_array($rs9)) {

            $data = $val9["data"];
            echo ' ' . giorno_settimana($data) . ' ' . data_italiana($data) . ' ';
            print "Ore assenza: " . $val9['oreassenza'];
            print " Materia: " . decodifica_materia(estrai_materia_lezione($val9['idlezione'], $con), $con);
            $giust = ($val9['giustifica'] == 1) ? "Giust." : "Non giust.";
            print " ($giust)<br>";
            $query = "select * from tbl_autorizzazioniuscite where idalunno=$idalunno and data='$data'";
            $ris = eseguiQuery($con, $query);
            if ($rec = mysqli_fetch_array($ris))
                print "<small>" . $rec['testoautorizzazione'] . "</small><br>";
        }
        print "</td></tr>";
    }


    $rasstot = eseguiQuery($con, "select sum(oreassenza) as assenzetotali from tbl_asslezione where idalunno=$idalunno $seledata");
    $rec = mysqli_fetch_array($rasstot);
    $numerooreasstotali = $rec['assenzetotali'];

    print "<tr><td colspan=3 align=center><b>Ore totali di assenza: $numerooreasstotali</b></td><tr></table>";

    if ($tipoutente != "A") {
    //
    // SITUAZIONE NOTE
    //
        $query = "select idclasse from tbl_alunni where idalunno=$idalunno";
        $ris = eseguiQuery($con, $query);
        $rec = mysqli_fetch_array($ris);
        $codclasse = $rec['idclasse'];

        // prelevamento dati alunno

        $query = "select * from tbl_alunni,tbl_classi where tbl_alunni.idclasse=tbl_classi.idclasse and idalunno='$idalunno'";
        $ris = eseguiQuery($con, $query);

        echo '<table border=1 align="center" width="800"  >';

        /* 	if($val=mysqli_fetch_array($ris))
          {
          echo '
          <tr>
          <td align=center><b> Alunno: '.$val["cognome"].' '.$val["nome"].' Classe '.$val["anno"].' '.$val["sezione"].' '.$val["specializzazione"].'</b></td>
          </tr> </table><br>
          ';
          }
         */


        $query = "select tbl_notealunno.idnotaalunno, data, tbl_alunni.cognome as cognalunno, tbl_alunni.nome as nomealunno, tbl_alunni.datanascita as dataalunno, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, tbl_alunni.datanascita, testo, provvedimenti
					from tbl_noteindalu, tbl_notealunno,tbl_classi, tbl_alunni, tbl_docenti 
					where 
					tbl_noteindalu.idnotaalunno=tbl_notealunno.idnotaalunno
					and tbl_noteindalu.idalunno=tbl_alunni.idalunno
					and tbl_notealunno.idclasse=tbl_classi.idclasse and  tbl_notealunno.iddocente=tbl_docenti.iddocente 
					and tbl_noteindalu.idalunno=$idalunno $seledata
					order by tbl_notealunno.data desc";
        // print inspref($query);
        $ris = eseguiQuery($con, $query);

        $c = mysqli_num_rows($ris);

        print "<br><br>";
        print "<table border=1 align=center>";
        print "<tr class=prima><td colspan=4 align=center>Note e provvedimenti disciplinari individuali</td></tr>";
        if ($c == 0) {
            echo "<tr><td colspan=4 align=center>Nessuna nota da visualizzare!</td></tr>";
        } else {
            print "<tr class=prima><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td></tr>";
            while ($rec = mysqli_fetch_array($ris)) {
                print("<tr>");

                print("<td>");
                print($rec['cogndocente'] . " " . $rec['nomedocente']);
                print("</td>");
                print("<td>");
                print(data_italiana($rec['data']));
                print("</td>");

                print("<td>");
                print("" . $rec['testo'] . "");
                print("</td>");
                print("<td>");
                print("" . $rec['provvedimenti'] . "");
                print("</td>");

                print("</tr>");
            }
        }
        print "</table><br>";

        $query = "select idnotaclasse, data, tbl_docenti.cognome as cogndocente, tbl_docenti.nome as nomedocente, testo, provvedimenti
					from tbl_noteclasse, tbl_classi, tbl_docenti 
					where tbl_noteclasse.idclasse=tbl_classi.idclasse and  tbl_noteclasse.iddocente=tbl_docenti.iddocente  
					and tbl_classi.idclasse=$codclasse 
					and data not in (select data from tbl_assenze where idalunno = $idalunno)
                                        $seledata
					order by tbl_noteclasse.data desc";
        // print $query."<br/>";
        $ris = eseguiQuery($con, $query);

        $c = mysqli_num_rows($ris);

        print "<table border=1 align=center>";
        print "<tr class=prima><td colspan=4 align=center>Note di classe</td></tr>";
        if ($c == 0) {
            echo "<tr><td colspan=4 align=center>Nessuna nota da visualizzare!</td></tr>";
        } else {
            print "<tr class=prima><td>Docente</td><td>Data</td><td>Nota</td><td>Provv.</td></tr>";
            while ($rec = mysqli_fetch_array($ris)) {
                print("<tr>");

                print("<td>");
                print($rec['cogndocente'] . " " . $rec['nomedocente']);
                print("</td>");
                print("<td>");
                print(data_italiana($rec['data']));
                print("</td>");
                print("<td>");
                print("" . $rec['testo'] . "");
                print("</td>");
                print("<td>");
                print("" . $rec['provvedimenti'] . "");
                print("</td>");

                print("</tr>");
            }
        }
        print "</table> <br/> <br/>";
    }
}
mysqli_close($con);
stampa_piede("");

