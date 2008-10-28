<?

global $open_tags, $print_on_exit, $html_id;
$open_tags = array();
$print_on_exit = array();
$html_id = 0;
global $input_event_handlers, $form_id;
$input_event_handlers = '';

global $td_title, $tr_title;
$td_title = '';
$tr_title = '';

// new_html_id(): liefert bei jedem Aufruf eine neue Nummer, zur Generierung eindeutiger id-Attribute:
//
function new_html_id() {
  global $html_id;
  ++$html_id;
  return $html_id;
}

function open_tag( $tag, $class = '', $attr = '' ) {
  global $open_tags;
  if( $class )
    $class = "class='$class'";
  echo "<$tag $class $attr>\n";
  $n = count( $open_tags );
  $open_tags[$n+1] = $tag;
}

function close_tag( $tag ) {
  global $open_tags, $hidden_input;
  $n = count( $open_tags );
  switch( $tag ) {
    case 'form':
      echo $hidden_input;
      $hidden_input = '';
      break;
  }
  if( $open_tags[$n] == $tag ) {
    echo "</$tag>";
    unset( $open_tags[$n--] );
  } else {
    error( "unmatched close_tag($tag)" );
  }
}

function open_div( $class = '', $attr = '', $payload = false ) {
  open_tag( 'div', $class, $attr );
  if( $payload !== false ) {
    echo $payload;
    close_div();
  }
}

function close_div() {
  close_tag( 'div' );
}

function open_span( $class = '', $attr = '', $payload = false ) {
  open_tag( 'span', $class, $attr );
  if( $payload !== false ) {
    echo $payload;
    close_span();
  }
}

function close_span() {
  close_tag( 'span' );
}

function open_table( $class = '', $attr = '' ) {
  open_tag( 'table', $class, $attr );
}

function close_table() {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'td':
    case 'th':
      close_tag( $open_tags[$n] );
    case 'tr':
      close_tag( 'tr' );
    case 'table':
      close_tag( 'table' );
      break;
    default:
      error( 'unmatched close_table' );
  }
}

function open_tr( $class = '', $attr = '' ) {
  global $open_tags, $tr_title;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'td':
    case 'th':
      close_tag( $open_tags[$n] );
    case 'tr':
      close_tag( 'tr' );
    case 'table':
      open_tag( 'tr', $class, $attr . $tr_title );
      break;
    default:
      error( 'unexpected open_tr' );
  }
  $tr_title = '';
}

function close_tr() {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'td':
    case 'th':
      close_tag( $open_tags[$n] );
    case 'tr':
      close_tag( 'tr' );
      break;
    case 'table':
      break;  // already closed, never mind...
    default:
      error( 'unmatched close_tr' );
  }
}

function open_tdh( $tag, $class= '', $attr = '', $payload = false ) {
  global $open_tags, $td_title;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'td':
    case 'th':
      close_tag( $open_tags[$n] );
    case 'tr':
      open_tag( $tag, $class . $td_title, $attr );
      break;
    case 'table':
      open_tr();
      open_tag( $tag, $class, $attr . $td_title );
      break;
    default:
      error( 'unexpected open_td' );
  }
  $td_title = '';
  if( $payload !== false ) {
    echo $payload;
    close_td();
  }
}

function open_td( $class= '', $attr = '', $payload = false ) {
  open_tdh( 'td', $class, $attr, $payload );
}
function open_th( $class= '', $attr = '', $payload = false ) {
  open_tdh( 'th', $class, $attr, $payload );
}

function close_td() {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'td':
    case 'th':
      close_tag( $open_tags[$n] );
      break;
    case 'tr':
    case 'table':
      break; // already closed, never mind...
    default:
      error( 'unmatched close_td' );
  }
}

function close_th() {
  close_td();
}

function tr_title( $title ) {
  global $tr_title;
  $tr_title = " title='$title' ";
}
function td_title( $title ) {
  global $td_title;
  $td_title = " title='$title' ";
}

function open_ul( $class = '', $attr = '' ) {
  open_tag( 'ul', $class, $attr );
}

function close_ul() {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'li':
      close_tag( 'li' );
    case 'ul':
      close_tag( 'ul' );
      break;
    default:
      error( 'unmatched close_ul' );
  }
}

function open_li( $class = '', $attr = '', $payload = false ) {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'li':
      close_tag( 'li' );
    case 'ul':
      open_tag( 'li', $class, $attr );
      break;
    default:
      error( 'unexpected open_li' );
  }
  if( $payload !== false ) {
    echo $payload;
    close_li();
  }
}

function close_li() {
  global $open_tags;
  $n = count( $open_tags );
  switch( $open_tags[$n] ) {
    case 'li':
      close_tag( 'li' );
      break;
    case 'ul':
      break;  // already closed, never mind...
    default:
      error( 'unmatched close_li' );
  }
}

function open_form( $class = '', $get_parameters = array(), $post_parameters = array() ) {
  global $form_id, $input_event_handlers, $hidden_input, $self_fields;

  if( is_string( $get_parameters ) )
    $get_parameters = parameters_explode( $get_parameters );
  if( is_string( $post_parameters ) )
    $post_parameters = parameters_explode( $post_parameters );

  $form_id = new_html_id();
  if( isset( $get_parameters['name'] ) ) {
    $name = $get_parameters['name'];
    unset( $get_parameters['name'] );
  } else {
    $name = "form_$form_id";
  }
  $input_event_handlers = " onChange='on_change($form_id);' ";

  $attr = adefault( $get_parameters, 'attr', '' );

  $window = adefault( $get_parameters, 'window', 'self' );
  $get_parameters['context'] = 'form';
  $action = fc_link( $window, $get_parameters );

  echo
  open_tag( 'form', $class, "method='post' $action name='$name' id='form_$form_id' $attr" );
  $hidden_input = "<input type='hidden' name='postform_id' value='".postform_id()."'>";
  foreach( $post_parameters as $key => $val ) {
    $hidden_input .= "<input type='hidden' name='$key' value='$val'>";
  }
  return $form_id;
}

function close_form() {
  global $input_event_handlers;
  $input_event_handlers = '';
  echo "\n<span class='nodisplay'><input type='submit'></span>";
  close_tag( 'form' );
  echo "\n";
}

function open_fieldset( $class = '', $attr = '', $legend = '', $toggle = false ) {
  if( $toggle ) {
    if( $toggle == 'on' ) {
      $buttondisplay = 'none';
      $fieldsetdisplay = 'block';
    } else {
      $buttondisplay = 'inline';
      $fieldsetdisplay = 'none';
    }
    $id = new_html_id();
    open_span( '', "$attr id='button_$id' style='display:$buttondisplay;'" );
      echo "<a class='button' onclick=\"document.getElementById('fieldset_$id').style.display='block';
                            document.getElementById('button_$id').style.display='none';\"
            >$legend...</a>";
    close_span();

    open_fieldset( $class, "$attr style='display:$fieldsetdisplay;' id='fieldset_$id'" );
    echo "<legend><img src='img/close_black_trans.gif'
            onclick=\"document.getElementById('button_$id').style.display='inline';
                     document.getElementById('fieldset_$id').style.display='none';\">
          $legend</legend>";
  } else {
    open_tag( 'fieldset', $class, $attr );
    if( $legend )
      echo "<legend>$legend</legend>";
  }
}

function close_fieldset() {
  close_tag( 'fieldset' );
}

function open_javascript( $js = '' ) {
  echo "\n";
  open_tag( 'script', '', "type='text/javascript'" );
  echo "\n";
  if( $js ) {
    echo $js ."\n";
    close_javascript();
  }
}

function close_javascript() {
  close_tag('script');
}

function floating_submission_button() {
  global $form_id;

  open_span( 'alert floatingbuttons', "id='floating_submit_button_$form_id'" );
    open_table('layout');
      open_td('alert left');
        ?> <a class='close' title='Schließen'
          onclick='document.getElementById("floating_submit_button_<? echo $form_id; ?>").style.display = "none";'> <?
      open_td('alert center quad', '', "&Auml;nderungen sind noch nicht gespeichert!" );
    open_tr();
      open_td( 'alert center oneline smallskip', "colspan='2'" );
        reset_button();
        submission_button();
    close_table();
  close_tag('span');
}

function submission_button( $text = '', $active = true ) {
  global $form_id;
  $text or $text = 'Speichern';
  $class = ( $active ? 'button' : 'button inactive' );
  // open_span( 'qquad', '', "<a class='$class' id='submit_button_$form_id' title='$text' onClick=\"document.getElementById('form_$form_id').submit();\">$text</a>" );
  open_span( 'qquad', '', "<a class='$class' id='submit_button_$form_id' title='$text' onClick=\"submit_form( $form_id );\">$text</a>" );
}

function reset_button( $text = 'Zur&uuml;cksetzen' ) {
  global $form_id;
  open_span( 'qquad', '', "<a class='button inactive' id='reset_button_$form_id' title='Änderungen zurücknehmen'
                              onClick=\"document.getElementById('form_$form_id').reset(); on_reset($form_id); \">$text</a>" );
}

function check_all_button( $text = 'Alle ausw&auml;hlen', $title = '' ) {
  global $form_id;
  $title or $title = $text;
  echo "<a class='button' title='$text' onClick='checkAll($form_id);'>$text</a>";
}
function uncheck_all_button( $text = 'Alle abw&auml;hlen', $title = '' ) {
  global $form_id;
  $title or $title = $text;
  echo "<a class='button' title='$text' onClick='uncheckAll($form_id);'>$text</a>";
}

function close_button( $text = 'Schließen' ) {
  echo "<a class='button' onclick='if(opener) opener.focus(); closeCurrentWindow();'>$text</a>";
  // echo "<input value='Schließen' type='button' onClick='if(opener) opener.focus(); closeCurrentWindow();'>";
}

function open_select( $fieldname, $autoreload = false ) {
  global $input_event_handlers;
  if( $autoreload ) {
    $id = new_html_id();
    $url = fc_link( 'self', 'XXX=X,context=action' );
    open_tag( 'select', '', "id='$id' onchange=\"
      i = document.getElementById('$id').selectedIndex;
      s = document.getElementById('$id').options[i].value;
      self.location.href = '$url'.replace( /XXX=X/, '&$fieldname='+s );
    \" " );
  } else {
    open_tag( 'select', '', "$input_event_handlers name='$fieldname'" );
  }
}

function close_select() {
  close_tag( 'select' );
}

function option_checkbox( $fieldname, $flag, $text, $title = false ) {
  global $$fieldname;
  echo '<input type="checkbox" class="checkbox" onclick="'
         . fc_link('', array( $fieldname => ( $$fieldname ^ $flag ), 'context' => 'js' ) ) .'" ';
  if( $title ) echo " title='$title' ";
  if( $$fieldname & $flag ) echo " checked ";
  echo ">$text";
}

function option_radio( $fieldname, $flags_on, $flags_off, $text, $title = false ) {
  global $$fieldname;
  $all_flags = $flags_on | $flags_off;
  $groupname = "{$fieldname}_{$all_flags}";
  echo "<input type='radio' class='radiooption' name='$groupname' onclick=\""
        . fc_link('', array( 'context' => 'js' , $fieldname => ( ( $$fieldname | $flags_on ) & ~ $flags_off ) ) ) .'"';
  if( ( $$fieldname & $all_flags ) == $flags_on ) echo " checked ";
  echo ">$text";
}

function alternatives_radio( $items ) {
  $id = new_html_id();
  open_ul('plain');
  $keys = array_keys( $items );
  foreach( $items as $item => $value ) {
    open_li();
    $title = '';
    if( is_array( $value ) ) {
      $text = current($value);
      $title = "title='".next($value)."'";
    } else {
      $text = $value;
    }
    echo "<input type='radio' class='radiooption' name='radio_$id' $title onclick=\"";
    foreach( $keys as $key )
      echo "document.getElementById('$key').style.display='". ( $key == $item ? 'block' : 'none' ) ."'; ";
    echo "\">$text";
  }
  close_ul();
}

function close_all_tags() {
  global $open_tags, $print_on_exit;
  while( $n = count( $open_tags ) ) {
    if( $open_tags[$n] == 'body' ) {
      foreach( $print_on_exit as $p )
        echo $p;
    }
    close_tag( $open_tags[$n] );
  }
}

register_shutdown_function( 'close_all_tags' );

function div_msg( $class, $msg, $backlink = false ) {
  echo "<div class='$class'>$msg " . ( $backlink ? fc_link( $backlink, 'text=zur&uuml;ck...' ) : '' ) ."</div>";
}

function hidden_input( $name, $val = false ) {
  global $hidden_input;
  if( $val === false ) {
    global $$name;
    $val = $$name;
  }
  $hidden_input .= "<input type='hidden' name='$name' value='$val'>\n";
}

function smallskip() {
  open_div('smallskip', '', '' );
}
function medskip() {
  open_div('medskip', '', '' );
}
function bigskip() {
  open_div('bigskip', '', '' );
}
function quad() {
  open_span('quad', '', '' );
}
function qquad() {
  open_span('qquad', '', '' );
}


// option_menu_row:
// fuegt eine zeile in die <table id="option_menu_table"> ein.
// die tabelle wird beim ersten aufruf erzeugt, und nach ausgabe des dokuments
// in ein beliebiges elternelement mit id="option_menu" verschoben:
//
function open_option_menu_row( $payload = false ) {
  global $option_menu_counter, $print_on_exit;
  if( ! $option_menu_counter ) {
    // menu erstmal erzeugen (so dass wir einfuegen koennen):
    echo "<table class='menu' id='option_menu_table'></table>";
    $option_menu_counter = 0;
    // positionieren erst ganz am schluss (wenn parent sicher vorhanden ist):
    $print_on_exit[] = "
      <script type='text/javascript'>
        var option_menu_parent, option_menu_table;
        option_menu_table = document.getElementById('option_menu_table');
        if( option_menu_table ) {
          option_menu_parent = document.getElementById('option_menu');
          if( option_menu_parent ) {
            option_menu_parent.appendChild(option_menu_table);
          }
        }
      </script>
    ";
  }
  $option_menu_counter = new_html_id();
  open_table();
  open_tr( '', "id='option_entry_$option_menu_counter'" );
  if( $payload ) {
    echo $payload;
    close_option_menu_row();
  }
}

function close_option_menu_row() {
  global $option_menu_counter;
  close_table();
  open_javascript( move_html( 'option_entry_' . $option_menu_counter, 'option_menu_table' ) );
}

?>
