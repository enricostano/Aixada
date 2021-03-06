<?php 
function existing_languages_selectbox()
{
    // We require that a line of the form 
    // $Text['es_es'] = 'Español'
    // exists in each language file
    $sbox = '<select id="pref_lang" name="pref_lang">';
    foreach (glob("local_config/lang/*.php") as $lang_file) {
        $a = strpos($lang_file, 'lang/');
        $lang = substr($lang_file, $a+5, strpos($lang_file, '.')-$a-5);
        $handle = @fopen($lang_file, "r");
        $line = fgets($handle);
        while (strpos($line, "Text['{$lang}']") === false and !feof($handle)) {
            $line = fgets($handle);            
        }
        if (feof($handle))
            $lang_desc = '';
        else {
            $tmp = trim(substr($line, strpos($line, '=')));
            $lang_desc = trim($tmp, " =;'\"");
        }
        $sbox .= "<option value=\"{$lang}\">{$lang_desc}</option>";
    }
    return $sbox . '</select>';
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Install</title>
   <link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/smoothness/jqueryui.css"/>
    
</head>
<body>
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
<script type="text/javascript">
   function callInstall(action, dataSerial) {
    var result = 2;
    $('#' + action)
    .removeClass('grayed')
    .addClass('processing')
    .attr({style:'visibility:visible'});
    $('#' + action + '_result')
    .removeClass('grayed')
    .addClass('processing')
    .attr({style:'visibility:visible'});
    var datai = $('#install').serialize();
    $.ajax({
	type: "POST",
		url: "php/ctrl/Install.php?oper=" + action,
		data : datai,
		success: function(msg) {
		$('#' + action)
		    .removeClass('processing')
		    .removeClass('noRed')
		    .addClass('okGreen')
		    .attr({style:'visibility:visible'});
		$('#' + action + '_result')
		    .removeClass('processing')
		    .removeClass('noRed')
		    .removeClass('grayed')
		    .removeClass('fgWhite')
		    .addClass('okGreen')
		    .attr({style:'visibility:visible'})
		    .text(' ok');
		result = 0;
	    },
		error : function(XMLHttpRequest, textStatus, errorThrown){
		$('#' + action)
		    .removeClass('processing')
		    .addClass('noRed')
		    .attr({style:'visibility:visible'});
		$('#' + action + '_result')
		    .text(XMLHttpRequest.responseText)
		    .removeClass('processing')
		    .addClass('noRed')
		    .removeClass('grayed')
		    .addClass('fgWhite')
		    .attr({style:'visibility:visible'});
		result = 1;
	    },
		async:false
	});
    return result;
    }
    $(function(){
	    $('#btn_install').button();
	    $('#btn_install').click(function(){
		    var actions = ['validate', 'connect', 'create_database', 'create_database_queries', 'create_user'];
		    for (var i=0; i<actions.length; i++) {
			$('#' + actions[i])
			    .removeClass('okGreen')
			    .removeClass('noRed')
			    .removeClass('processing')
			    .addClass('grayed')
			    .attr({style:'visibility:hidden'});
			$('#' + actions[i] + '_result')
			    .removeClass('okGreen')
			    .removeClass('noRed')
			    .removeClass('processing')
			    .addClass('grayed')
			    .attr({style:'visibility:hidden'})
			    .text('');
		    }
		    var result = 0;

		    //not sure this will work: call all installs at the same time?! 
		    // jp: async:false !!! :) :)
		    for (var i=0; i<actions.length && result==0; i++) {
			result = callInstall(actions[i]);
		    }
		});
	    return false;
	});
</script>

<br/><br/><br/>
  <div id="wrap">
   <div id="wrapForm">
   
      <form id="install">
         <h2>Install your Aixada platform</h2>   
         <br/><br/><br/>
         <p>What you need:
            <ul>
            <li>The name of an existing mySQL database</li>
            <li>An existing mySQL user (name, password) with sufficient rights for your database</li>
            </ul>         

                    
         </p>
         <p><br/><br/></p>
         
               <table>
                  <tr>
                     <td><label for="first_Uf">Name of first UF</label></td>
                     <td><input type="text" name="first_uf" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="user_login">First User's login</label></td>
                     <td><input type="text" name="user_login" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="user_password">First User's password</label></td>
                     <td><input type="password" name="user_password" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="retype_password">Retype password</label></td>
                     <td><input type="password" name="retype_password" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td colspan="2" class="textAlignRight"><br/><br/><p id="btn_install" type="submit">Install :-)</p></td>
                  </tr>
               </table>
      
      </form>
   </div>
<br/>
   <div id="wrapFeedback">
      <p id="validate" class="grayed " style="visibility:hidden">Validating input ... <b id="validate_result"></b></p>
      <p id="connect" class="grayed " style="visibility:hidden">Connect to database ... <b id="connect_result"></b></p>
      <p id="create_database" class="grayed" style="visibility:hidden">Create database ... <b id="create_database_result"></b></p>
      <p id="create_database_queries" class="grayed" style="visibility:hidden">Create database queries ... <b id="create_database_queries_result"></b></p>
      <p id="create_config_file" class="grayed" style="visibility:hidden">Create configuration file ... <b id="create_config_file_result"></b></p>
      <p id="create_user" class="grayed" style="visibility:hidden">Create special user ... <b id="create_user_result"></b></p>
      <p id="ok" class="grayed" style="visibility:hidden">Success!</p>
   </div>
  <br/>
  <div>
    <p id="installMsg" class="user_tips  minPadding"></p>
  </div>
 </div>
</body>
</html>

