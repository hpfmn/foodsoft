
<?php

error_reporting(E_ALL);
 $_SESSION['LEVEL_CURRENT'] = LEVEL_IMPORTANT;
     if( ! $angemeldet ) {
       exit( "<div class='warn'>Bitte erst <a href='index.php'>Anmelden...</a></div>");
     } 
     

     if($hat_dienst_V){

?>
 <div id=Zusatz>
       <h1>Dienste erstellen</h1>

   <!-- Zeige bisherige Dienste-->

   
   <form name="erstellen" action="index.php" method="post">
	   <input type="hidden" name="area" value="dienste">			
	   <? 
	     get_http_var("dienstfrequenz"); //ToDo check for integer
	     if (!isset($dienstfrequenz)){
	     	$dienstfrequenz = "7";
	     } else {
	          get_http_var("startdatum"); //ToDo check for date
	          get_http_var("enddatum"); //ToDo check for date
	          create_dienste($startdatum,$enddatum,$dienstfrequenz);
		  ?>echo <p><b> Dienste erstellt </b></p><?
	     }
	     $startdatum =  get_latest_dienst($dienstfrequenz);
	     $enddatum = get_latest_dienst(60+$dienstfrequenz);

	   ?>
	   Verteile Dienste mit 
	   <input type="text" size=3 name="dienstfrequenz" value=<?echo $dienstfrequenz?> />
	   tägigem Abstand <br> ab dem
	   <input type="text" size=10 name="startdatum" value=<?echo $startdatum?> />
	   bis
	   <input type="text" size=10 name="enddatum" value=<?echo $enddatum?> />
	   <br>
	   <input type="submit" action="create"  value="Dienste Erstellen" />

	   <p>
	   </p>

   </form>


       <h1>Rotationsplan</h1>

   <form name="rotationsplan" action="index.php" method="post">
	   <input type="hidden" name="area" value="dienste">			
	   <? 
	     get_http_var("plan_dienst");
	     if (!isset($plan_dienst)) $plan_dienst = "1/2";
             foreach (array_keys($_REQUEST) as $submitted){
	 	if(strstr($submitted, "up_")!==FALSE){
		    sql_change_rotationsplan(substr($submitted, 3), $plan_dienst, FALSE);
		} elseif(strstr($submitted, "down_")!==FALSE){
		    sql_change_rotationsplan(substr($submitted, 5), $plan_dienst, TRUE);
		}
	      }
	 	
	   ?>
	   Rotationsplan für
	   <select name="plan_dienst" onchange="document.rotationsplan.submit()">
	      <option value="1/2" <?if($plan_dienst=="1/2") echo "selected"?>> Dienst 1/2 </option>
	      <option value="3"<?if($plan_dienst=="3") echo "selected"?>> Dienst 3 </option>
	      <option value="4"<?if($plan_dienst=="4") echo "selected"?>> Dienst 4 </option>
	   </select>
	   bearbeiten:

	   <br>

	   <table>
           <?
	   $rotationen = sql_rotationsplan($plan_dienst);
	   while($gruppe = mysql_fetch_array($rotationen)){
	   	rotationsplanView($gruppe);
	   }
	   ?>
	   </table>



   </form>

   </div>
   <?
   }
?>
       <h1>Dienstliste</h1>

   <!-- Zeige bisherige Dienste-->

   
     <table><tr>
      <th> Datum </th>
      <th> Dienst 1/2 </th>
      <th> Dienst 3 </th>
      <th> Dienst 4 </th>
      </tr><tr>
	   <? 
	   /*
	       Abgeschickte Befehle auffangen und ausführen
		uebernehmen_
		wirdoffen_
		abtauschen_
		akzeptieren_
            */

             foreach ($_REQUEST as $submitted){
	        $command = explode("_", $submitted);
		switch($command[0]){
		case "uebernehmen":
                   $row = sql_get_dienst_by_id($command[1]);
		   if($row["Status"]=="Offen" || isset($_REQUEST["confirmed"])){
		   //Offenen Dienst gleich übernehmen
                       sql_dienst_uebernehmen($command[1]);
                   } else {
		   //Nicht bestätigten Dienst: Confirmation
		       ?>
		       <form action="index.php">
		       <input type="hidden" name="area" value="dienste">
		       <input type="hidden" name="aktion" value="uebernehmen_<?echo $command[1]?>">
		       <input type="hidden" name="confirmed" value="confirmed">
		       <b>Dies müsste mit der andern Gruppe abgesprochen sein oder die Gruppe ist nach mehreren Versuchen (Telefon und Email) nicht erreichbar </b>
		       <input  type="submit" value="Klar">  
		       </form>
		       <br>
		       <?
		   }
		   break;
		case "wirdoffen":
		   sql_dienst_wird_offen($command[1]);
		   break;
		case "abtauschen":
                   $row = sql_get_dienst_by_id($command[1]);
		   //Datumsvorschlag unterbreiten
		   get_http_var("abtauschdatum");
		   if(!isset($abtauschdatum)){
		       $dates = sql_get_dienst_date($row["Dienst"], "Vorgeschlagen");
		       if(mysql_num_rows($dates)<=1){
		           //Keine Möglichkeit zum Tauschen
			   //Das eigene Datum ist auch in der Liste
			   ?> <b> Keine Tauschmöglichkeit. Dienst ist jetzt offen </b> <?
		           sql_dienst_wird_offen($command[1]);
		       } else {
		           ?> 
			   Bitte Ausweichdatum auswählen:
			   <form name=tauschdatum" action="index.php" method="post">
		            <input type="hidden" name="aktion" value="abtauschen_<?echo $command[1]?>">
	                   <input type="hidden" name="area" value="dienste">	
			   <select name="abtauschdatum">
			   <?
		           while($date = mysql_fetch_array($dates)){
			       ?>
			       <option value=<?echo $date["datum"]?> ><?echo $date["datum"]?> </option>
			       <?

		           }
			   ?>
			   </select>
			   <input type="submit" value="Dieses Datum geht">  
			   </form>
			   <p>
			   <?
		       }

		   } else {
		   //erst bei gewähltem Datum ausführen
		       sql_dienst_abtauschen($command[1], $abtauschdatum);

		   }
		   break;
		case "akzeptieren":
		   sql_dienst_akzeptieren($command[1]);
		   break;
		
	        }
	     }

	  //Formular vorbereiten und anzeigen

	    $dienste =  sql_get_dienste();
	    $currentDienst = "initial";
	    $currentDate = "initial";
	    while($row = mysql_fetch_array($dienste)){
		//neue Zeile für Dienst 1/2
	        if($row["Lieferdatum"]!=$currentDate){ //Problem, wenn Dienst abgef. immer 1/2
		    $currentDate = $row["Lieferdatum"];
		    ?>
		     </tr><tr><td><?echo $currentDate?></td>
		    <?
		}
		if($currentDienst != $row["Dienst"]){
			echo "</td><td>";
			$currentDienst = $row["Dienst"];
		}
		if($row["Status"]!="Nicht geleistet"){
			dienst_view($row, $login_gruppen_id); 
			echo "<br>";
		}
	    }
	   ?>
     </tr>
     </table>

	   <p>
	   </p>

