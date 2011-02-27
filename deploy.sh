#!/bin/sh
#
# this file is generated - do not modify!
#

export LANG=C
BRANCH=`git branch | sed -e '/^[^*]/d' -e 's/^\* \(.*\)/\1/'`
COMMIT=`git rev-parse --short HEAD`
COMMIT_FULL=`git rev-parse HEAD`
DIRTY=""
git status | grep -qF 'working directory clean' || DIRTY='-dirty'
echo "<a href='http://github.com/foodsoft/foodsoft/commits/$BRANCH/$COMMIT'>$BRANCH-$COMMIT$DIRTY</a>" >version.txt

chmod 755 .
chmod 755 ./css
chmod 644 ./css/readonly.gif
chmod 644 ./css/print.css
chmod 644 ./css/modified.gif
chmod 644 ./css/foodsoft.css
chmod 600 ./ToDo.txt
chmod 755 ./antixls.modif
chmod 644 ./leitvariable.php
chmod 644 ./structure.php
chmod 644 ./links_und_parameter
chmod 644 ./README.md
chmod 644 ./version.txt
chmod 755 ./wiki
chmod 644 ./wiki/start.txt
chmod 755 ./wiki/wiki
chmod 755 ./wiki/wiki/syntax.txt
chmod 755 ./wiki/wiki/dokuwiki.txt
chmod 755 ./wiki/privates
chmod 644 ./wiki/privates/privates.txt
chmod 644 ./wiki/privates/.gitignore
chmod 644 ./wiki/nostalgisches.txt
chmod 755 ./wiki/foodsoft
chmod 644 ./wiki/foodsoft/doku.txt
chmod 644 ./wiki/foodsoft/datenbankabgleich.txt
chmod 644 ./wiki/foodsoft/abrechnung.txt
chmod 644 ./wiki/foodsoft/verteilung.txt
chmod 644 ./wiki/foodsoft/die_papiernen_kontoblaetter_pflegen.txt
chmod 644 ./wiki/foodsoft/basarbewegungen_eintragen.txt
chmod 644 ./wiki/foodsoft/bestellung_der_naechsten_lieferung.txt
chmod 644 ./wiki/foodsoft/bestellvorlagen_erstellen.txt
chmod 644 ./wiki/foodsoft/daten_vom_server_runterladen.txt
chmod 644 ./wiki/foodsoft/lieferschein.txt
chmod 644 ./wiki/foodsoft/dienstplan.txt
chmod 644 ./wiki/foodsoft/bestellen.txt
chmod 644 ./wiki/foodsoft/basar.txt
chmod 644 ./wiki/foodsoft/bestellformular.txt
chmod 644 ./wiki/foodsoft/daten_auf_den_server_hochladen.txt
chmod 755 ./wiki/nahrungskette
chmod 644 ./wiki/nahrungskette/dienstv.txt
chmod 644 ./wiki/nahrungskette/protokollarchiv.txt
chmod 644 ./wiki/nahrungskette/abrechnung.txt
chmod 644 ./wiki/nahrungskette/notplandiensti.txt
chmod 644 ./wiki/nahrungskette/kellerdienst.txt
chmod 644 ./wiki/nahrungskette/faktenunddaten.txt
chmod 644 ./wiki/nahrungskette/dienste.txt
chmod 644 ./wiki/nahrungskette/bioladen_der_muslimischen_gemeinschaft_potsdam.txt
chmod 644 ./wiki/nahrungskette/allgemeines.txt
chmod 644 ./wiki/nahrungskette/warenannahmeverteilung.txt
chmod 644 ./wiki/nahrungskette/papierkontoblatt.txt
chmod 644 ./wiki/nahrungskette/leitfaden.txt
chmod 644 ./index.php
chmod 644 ./GITHOOKS
chmod 755 ./setup.php
chmod 644 ./INSTALL
chmod 755 ./code
chmod 644 ./code/inlinks.php
chmod 644 ./code/config.php
chmod 644 ./code/err_functions.php
chmod 644 ./code/zuordnen.php
chmod 644 ./code/login.php
chmod 644 ./code/common.php
chmod 644 ./code/katalogsuche.php
chmod 644 ./code/views.php
chmod 644 ./code/forms.php
chmod 644 ./code/html.php
chmod 644 ./foodsoft.class.php
chmod 644 ./fcck.php
chmod 755 ./js
chmod 644 ./js/tooltip.js
chmod 644 ./js/foodsoft.js
chmod 755 ./js/lib
chmod 644 ./js/lib/prototype.js
chmod 644 ./.gitignore
chmod 644 ./dump.php
chmod 644 ./apache.sample.conf
chmod 700 ./pre-commit
chmod 644 ./head.php
chmod 755 ./img
chmod 644 ./img/chart.png
chmod 644 ./img/reload_black.gif
chmod 644 ./img/open_black_trans.gif
chmod 644 ./img/chalk_trans.gif
chmod 644 ./img/print_black.gif
chmod 644 ./img/people.png
chmod 644 ./img/birne_rot.png
chmod 644 ./img/b_drop.png
chmod 644 ./img/arrow.up.blue.png
chmod 644 ./img/close_black_trans.gif
chmod 644 ./img/gluehbirne_15x16.png
chmod 644 ./img/b_browse.png
chmod 644 ./img/magic_wand.png
chmod 644 ./img/minus.png
chmod 644 ./img/close_black_hover.gif
chmod 644 ./img/green.png
chmod 644 ./img/question_small.png
chmod 644 ./img/arrow.down.blue.png
chmod 644 ./img/plus.png
chmod 644 ./img/b_edit.png
chmod 644 ./img/question.png
chmod 644 ./img/close_black.gif
chmod 644 ./img/euro.png
chmod 644 ./img/card.png
chmod 644 ./img/fant.gif
chmod 755 ./windows
chmod 644 ./windows/gruppen.php
chmod 644 ./windows/bestellschein.php
chmod 644 ./windows/editProduktgruppe.php
chmod 644 ./windows/editBestellung.php
chmod 755 ./windows/artikelsuche.php
chmod 644 ./windows/produktverteilung.php
chmod 644 ./windows/konto.php
chmod 644 ./windows/editVerpackung.php
chmod 644 ./windows/menu.php
chmod 644 ./windows/abschluss.php
chmod 644 ./windows/bestellungen.php
chmod 644 ./windows/gruppenkonto.php
chmod 644 ./windows/basar.php
chmod 755 ./windows/updownload.php
chmod 644 ./windows/lieferantenkonto.php
chmod 644 ./windows/produkte.php
chmod 644 ./windows/editProdukt.php
chmod 644 ./windows/lieferanten.php
chmod 644 ./windows/pfandverpackungen.php
chmod 644 ./windows/gruppenmitglieder.php
chmod 644 ./windows/gesamtlieferschein.php
chmod 644 ./windows/dienstplan.php
chmod 755 ./windows/produktpreise.php
chmod 644 ./windows/verluste.php
chmod 644 ./windows/gruppenpfand.php
chmod 644 ./windows/bestellen.php
chmod 644 ./windows/svn-commit.tmp
chmod 644 ./windows/editBuchung.php
chmod 644 ./windows/abrechnung.php
chmod 644 ./windows/head.php
chmod 644 ./windows/editLieferant.php
chmod 755 ./windows/katalog_upload.php
chmod 644 ./windows/bestellen.php.neu
chmod 644 ./windows/dienstkontrollblatt.php
chmod 644 ./windows/editKonto.php
chmod 644 ./windows/bilanz.php
chmod 644 ./files_und_skripte
chmod 644 ./.gitattributes
chmod 700 ./deploy.sh
chmod 700 .git
