<?php
//
// bestellschein.php:
// - wenn bestell_id (oder bestellungs_id...) uebergeben:
//   detailanzeige, abhaengig vom status der bestellung
// - wenn keine bestell_id uebergeben:
//   auswahlliste aller bestellungen zeigen
//   (ggf. mit filter "status")
//

error_reporting(E_ALL);

if( ! $angemeldet ) {
  exit( "<div class='warn'>Bitte erst <a href='index.php'>Anmelden...</a></div>");
} 

if( get_http_var( 'bestellungs_id', 'u' ) ) {
  $bestell_id = $bestellungs_id;
  $self_fields['bestell_id'] = $bestell_id;
} else {
  get_http_var( 'bestell_id', 'u', false, true );
}

switch( $action ) {
  case 'changeState':
    fail_if_readonly();
    nur_fuer_dienst(1,3,4);
    need_http_var( 'change_id', 'u' );
    need_http_var( 'change_to', 'w' );
    changeState( $change_id, $change_to );
    // echo "<div class='warn'>change_id: $change_id, change_to: $change_to</div>";
    break;
  case 'changeEndDate':
    fail_if_readonly();
    nur_fuer_dienst(4);
    // yet to be implemented...
  default:
    break;
}

if( ! $bestell_id ) {
  // auswahl praesentieren, abhaengig von $state oder $area:
  if( ! get_http_var( 'state', 'w', false, true ) ) {
    switch( $area ) {
      case 'lieferschein':
        $state = STATUS_VERTEILT;
        break;
      case 'bestellschein':
        $state = array( STATUS_BESTELLEN, STATUS_LIEFERANT );
        break;
      default:
        $state = false;
    }
  }
  $result = sql_bestellungen( $state );
  select_bestellung_view($result, 'Liste der Bestellungen', $hat_dienst_IV, $dienst > 0 );
  echo "$print_on_exit";
  exit();
}

get_http_var( 'gruppen_id', 'u', 0, true );
if( $gruppen_id )
  $gruppen_name = sql_gruppenname($gruppen_id);

$state = getState($bestell_id);

$default_spalten = PR_COL_NAME | PR_COL_LPREIS | PR_COL_VPREIS | PR_COL_MWST | PR_COL_PFAND;
switch($state){    // anzeigedetails abhaengig vom Status auswaehlen
  case STATUS_BESTELLEN:
    $editable = FALSE;
    if( $gruppen_id ) {
      $default_spalten |= ( PR_COL_BESTELLMENGE | PR_COL_ENDSUMME );
    } else {
      $default_spalten
        |= ( PR_COL_BESTELLMENGE | PR_COL_BESTELLGEBINDE | PR_COL_NETTOSUMME | PR_COL_BRUTTOSUMME );
    }
    $title="Bestellschein (vorläufig)";
    break;
  case STATUS_LIEFERANT:
    $editable= FALSE;
    if( $gruppen_id ) {
      $default_spalten |= ( PR_COL_BESTELLMENGE | PR_COL_LIEFERMENGE | PR_COL_ENDSUMME );
    } else {
      $default_spalten
        |= ( PR_COL_BESTELLMENGE | PR_COL_LIEFERMENGE | PR_COL_LIEFERGEBINDE
             | PR_COL_NETTOSUMME | PR_COL_BRUTTOSUMME );
    }
    $title="Bestellschein";
    // $selectButtons = array("zeigen" => "bestellschein", "pdf" => "bestellt_faxansicht" );
    break;
  case STATUS_VERTEILT:
    if( $gruppen_id ) {
      $editable= FALSE;
      $default_spalten |= ( PR_COL_BESTELLMENGE | PR_COL_LIEFERMENGE | PR_COL_ENDSUMME );
    } else {
      // ggf. liefermengen aendern lassen:
      $editable = (!$readonly) and ( $hat_dienst_I or $hat_dienst_III or $hat_dienst_IV );
      $default_spalten
        |= ( PR_COL_BESTELLMENGE | PR_COL_LIEFERMENGE | PR_COL_LIEFERGEBINDE
             | PR_COL_NETTOSUMME | PR_COL_BRUTTOSUMME );
    }
    $title="Lieferschein";
    break;
  default: 
    ?> <div class='warn'>Keine Detailanzeige verfügbar</div> <?
    echo "$print_on_exit";
    exit();
}

get_http_var( 'spalten', 'w', $default_spalten, true );

										
	 if($state==STATUS_LIEFERANT){
	 	verteilmengenZuweisen($bestell_id);
	 }


  // liefermengen aktualisieren:
  //
  if( $editable and $state == STATUS_VERTEILT ) {
    $produkte = sql_bestellprodukte($bestell_id);
    while  ($produkte_row = mysql_fetch_array($produkte)) {
      $produkt_id =$produkte_row['produkt_id'];
      if( get_http_var( 'liefermenge'.$produkt_id ) ) {
        preisdatenSetzen( & $produkte_row );
        $mengenfaktor = $produkte_row['mengenfaktor'];
        $liefermenge = $produkte_row['liefermenge'] / $mengenfaktor;
        if( abs( ${"liefermenge$produkt_id"} - $liefermenge ) > 0.001 ) {
          $liefermenge = ${"liefermenge$produkt_id"};
          changeLiefermengen_sql( $liefermenge * $mengenfaktor, $produkt_id, $bestell_id );
        }
      }
    }
  }

         //infos zur gesamtbestellung auslesen 
	 $result = sql_bestellungen(FALSE,FALSE,$bestell_id);
	
       //Formular ausgeben

	echo "<h1>".$title."</h1>";

  ?>
    <table width='100%' class='layout'>
      <tr>
        <td style='text-align:left;padding-bottom:1em;'>
         <?
         bestellung_overview(mysql_fetch_array($result),$gruppen_id,$gruppen_id);
         ?>
        </td>
        <td style='text-align:right;padding-bottom:1em;' id='option_menu'>
        </td>
      </tr>
    </table>
  <?

  option_menu_row( "<th colspan='2'>Anzeigeoptionen</th>" );

  option_menu_row(
    " <td>Gruppenansicht:</td>
      <td><select id='select_group' onchange=\"select_group('"
      . self_url( 'gruppen_id' ) . "');\">
    " . optionen_gruppen($bestell_id,false,$gruppen_id, "Alle (Gesamtbestellung)" ) . "
      </select></td>"
  );

  products_overview(
    $bestell_id,
    $editable,   // Liefermengen edieren zulassen?
    $editable,   // Preise edieren zulassen?
    $spalten,    // welche Tabellenspalten anzeigen
    $gruppen_id, // Gruppenansichte (0: alle)
    true,        // angezeigte Spalten auswaehlen lassen
    true,        // Gruppenansicht auswaehlen lassen
    true         // Option: Anzeige nichtgelieferte zulassen
  );

?>

   <!-- nicht mehr sinnvoll, wenn in eigenem Fenster angezeigt:
   <form action="index.php" method="get">
	   <input type="hidden" name="area" value="<?echo($area)?>">			
	   <input type="submit" value="Zur�ck zur Auswahl ">
   </form>
   -->

