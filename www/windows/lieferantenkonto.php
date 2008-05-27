<?PHP

 assert($angemeldet) or exit();
 
 //Vergleicht das Datum der beiden mysql-records
 //gibt +1 zur�ck, wenn Datum in $konto �lter ist
 //gibt 0 zur�ck, wenn Daten gleich sind
 //gibt -1 zur�ck, wen Datum in $veteil �lter ist
 function compare_date($konto, $verteil){
	//Kein weiterer Eintrag in Konto
 	if(!$konto) return 1;
	if(!$verteil) return -1;
 	$konto_date = $konto['valuta_kan'];
	$verteil_date = $verteil['valuta_kan'];
  if( $konto_date < $verteil_date )
    return 1;
  if( $konto_date > $verteil_date )
    return -1;
  return 0;
 }

$editable = ( ! $readonly and ( $dienst == 4 ) );
get_http_var( 'lieferanten_id', 'u', 0, true );

?>
 <h1>Lieferantenkonto</h1>
 <div id='option_menu'></div>
<?

option_menu_row( "<th colspan='2'>Anzeigeoptionen</th>" );

option_menu_row(
  " <td>Lieferant:</td>
    <td><select id='select_lieferant' onchange=\"select_lieferant('"
    . self_url( 'lieferanten_id' ) . "');\">
  " . optionen_lieferanten( $lieferanten_id ) . "
    </select></td>"
);
if( ! $lieferanten_id )
  return;

$lieferanten_name = lieferant_name( $lieferanten_id );

if( $editable ) {
  get_http_var( 'action', 'w', '' );
  switch( $action ) {
    case 'zahlung_lieferant':
      buchung_lieferant_bank();
      break;
   case 'zahlung_gruppe_lieferant':
     buchung_gruppe_lieferant();
     break;
  }

  ?>
    <div id='transactions_button' style='padding-bottom:1em;'>
    <span class='button'
      onclick="document.getElementById('transactions_menu').style.display='block';
               document.getElementById('transactions_button').style.display='none';"
      >Transaktionen...</span>
    </div>

    <fieldset class='small_form' id='transactions_menu' style='display:none;margin-bottom:2em;'>
      <legend>
        <img src='img/close_black_trans.gif' class='button' title='Schliessen' alt='Schliessen'
        onclick="document.getElementById('transactions_button').style.display='block';
                 document.getElementById('transactions_menu').style.display='none';">
        Transaktionen
      </legend>

      Art der Transaktion:
        <span style='padding-left:1em;' title='Überweisung oder Abbuchung von Bankkonto der Foodcoop'>
        <input type='radio' name='transaktionsart'
          onclick="document.getElementById('zahlungbank_form').style.display='block';
                   document.getElementById('zahlunggruppe_form').style.display='none';"
        ><b>Überweisung/Lastschrift</b>
        </span>

        <span style='padding-left:1em;' title='Direktzahlung von Gruppe an Lieferant'>
        <input type='radio' name='transaktionsart'
          onclick="document.getElementById('zahlungbank_form').style.display='none';
                   document.getElementById('zahlunggruppe_form').style.display='block';"
        ><b>Direktzahlung durch Gruppe</b>
        </span>

        <div id='zahlungbank_form' style='display:none;'>
          <? formular_buchung_lieferant_bank( $lieferanten_id ); ?>
        </div>

        <div id='zahlunggruppe_form' style='display:none;'>
          <? formular_buchung_gruppe_lieferant( 0, $lieferanten_id ); ?>
        </div>

     </fieldset>

  <?
}

  $kontostand = lieferantenkontostand($lieferanten_id);
  $pfandkontostand = lieferantenpfandkontostand($lieferanten_id);

  $cols = 10;
  ?>
  <table class="numbers">
    <tr>
      <th>Typ</th>
      <th>Valuta</th>
      <th>Buchung</th>
      <th>Informationen</th>
      <th>Pfand</th>
      <th>Pfandsumme</th>
      <th>Waren</th>
      <th>Summe</th>
      <th>Transaktionen</th>
      <th>Kontostand</th>
    </tr>
    <tr class='summe'>
      <td colspan='<? echo $cols-5; ?>' style='text-align:right;'>Kontostand:</td>
      <td class='number'>
        <? printf( "(%8.2lf)", $pfandkontostand ); ?>
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class='number'>
        <? printf( "%8.2lf", $kontostand ); ?>
      </td>
    </tr>
  <?

  $konto_result = sql_get_group_transactions( 0, $lieferanten_id );
  $vert_result = sql_bestellungen_soll_lieferant( $lieferanten_id );

  $summe = $kontostand;
  $pfandsumme = $pfandkontostand;
  $konto_row = mysql_fetch_array($konto_result);
  $vert_row = mysql_fetch_array($vert_result);
  while( $konto_row or $vert_row ) {
    if( compare_date($konto_row, $vert_row) == 1 ) {
      //Eintrag in Konto ist �lter -> Verteil ausgeben
      $details_url = "index.php?window=abrechnung"
          . "&bestell_id={$vert_row['gesamtbestellung_id']}";
//              . "&spalten=" . ( PR_COL_NAME | PR_COL_BESTELLMENGE | PR_COL_LPREIS
//                                | PR_COL_LIEFERMENGE | PR_COL_ENDSUMME );
      $pfand_leer_soll = $vert_row['pfand_leer_brutto_soll'];
      $pfand_voll_soll = $vert_row['pfand_voll_brutto_soll'];
      $pfand_soll = $pfand_leer_soll + $pfand_voll_soll;
      $waren_soll = $vert_row['waren_brutto_soll'];
      $soll = $pfand_soll + $waren_soll;
      ?>
      <tr>
        <td style='vertical-align:top;font-weight:bold;'>Bestellung</td>
        <td><? echo $vert_row['valuta_trad']; ?></td>
        <td><? echo $vert_row['lieferdatum_trad']; ?></td>
        <td>Bestellung: <a
            href="javascript:neuesfenster('<? echo $details_url; ?>','abrechnung');"
            ><? echo $vert_row['name']; ?></a>
        </td>
        <td class='number'>
          <div style='font-weight:bold;'><? printf("%.2lf", $pfand_soll ); ?></div>
        </td>
        <td class='number'>
          <div style='font-weight:bold;'><? printf("%.2lf", $pfand_summe ); ?></div>
        </td>
        <td class='number'>
          <div style='font-weight:bold;'><? printf("%.2lf", $waren_soll ); ?></div>
        </td>
        <td>&mbsp;</td>
        <td class='number'>
          <div style='font-weight:bold;'><? printf("%.2lf", $soll ); ?></div>
        <td>
        <td class='number'>
          <div style='font-weight:bold;'><? printf("%.2lf", $summe ); ?></div>
        <td>
      </tr>
      <?
      $summe -= $soll;
      $pfandsumme -= $pfand_soll;
      $vert_row = mysql_fetch_array($vert_result);
    } else {
      ?>
      <tr>
        <td valign='top' style='font-weight:bold;'>
          <?
          if( $konto_row['konterbuchung_id'] >= 0 ) {
            echo 'Zahlung';
          } else {
            echo 'Verrechnung';
          }
          ?>
        </td>
        <td><? echo $konto_row['valuta_trad']; ?></td>
        <td><? echo $konto_row['date']; ?></td>
        <td>
          <div><? echo $konto_row['notiz']; ?></div>
          <div>
            <?
            $k_id = $konto_row['konterbuchung_id'];
            $k_row = sql_get_transaction( $k_id );
            if( $k_id > 0 ) { // bankueberweisung oder lastschrift
              $konto_id = $k_row['konto_id'];
              $auszug_nr = $k_row['kontoauszug_nr'];
              $auszug_jahr = $k_row['kontoauszug_jahr'];
              echo "Auszug: <a href=\"javascript:neuesfenster(
                 'index.php?window=konto&konto_id=$konto_id&auszug_jahr=$auszug_jahr&auszug_nr=$auszug_nr'
                ,'konto'
                );\">$auszug_jahr / $auszug_nr ({$k_row['kontoname']})</a>
              ";
            } else {  // zahlung durch gruppe
              $gruppen_id = $k_row['gruppen_id'];
              $gruppen_name = sql_gruppenname($gruppen_id);
              echo "Zahlung durch Gruppe: <a href=\"javascript:neuesfenster(
              'index.php?window=showGroupTransaktions&gruppen_id=$gruppen_id'
              , 'kontoblatt'
              );\">$gruppen_name</a>
              ";
            }
            ?>
          </div>
          </td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class='mult'>
            <div style='font-weight:bold;'>
              <? printf("%.2lf" , $konto_row['summe']); ?>
            </div>
          </td>
          <td class='number'>
            <? printf( "%8.2lf", $summe ); ?>
          </td>
        </tr>
      <?
      $summe -= $konto_row['summe'];
      $konto_row = mysql_fetch_array($konto_result);
    }
  }

  ?>
    <tr class='summe'>
      <td colspan='<? echo $cols-5; ?>' style='text-align:right;'>Startsaldo:</td>
      <td class='number'>
        <div><? printf( "%8.2lf", $pfandsumme ); ?></div>
      </td>
            <div style='font-size:smaller;'><? printf( "(%8.2lf)", $pfandsumme ); ?></div>
          </td>
        </tr>
   </table>

