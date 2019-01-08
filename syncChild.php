<?php

/******************************************************************************/
/*  Developed by:  Ken Sladaritz                                              */
/*                 Marshall Computer Service                                  */
/*                 2660 E End Blvd S, Suite 122                               */
/*                 Marshall, TX 75672                                         */
/*                 ken@marshallcomputer.net                                   */
/******************************************************************************/

require 'config.php';
require "authorization.php";

global $st;

$marker_size = 20;

if ( (!$menu_tab_access['sync.php'] and !$menu_tab_access['everything']) or $sec_password!=$myrow_us['us_pass'])
{
 echo '<span class="errmsg"> Access denied or timed out. Please Login. Thank you. </span>';
 require "./foot.php";
 exit;
}

// get annotations
$bibleTitleId = 1;
$bookId = 'NO_BOOK_ID';

if($_POST['bibleTitle'])
{
 $query =
 "SELECT * FROM `bibleTitles`
  WHERE `title` = \"".mysql_real_escape_string($_POST['bibleTitle'])."\"
  LIMIT 1 
 ";
 $result=mysql_query($query) or die ("<pre>".$query.mysql_error()."</pre>");
 $myrow=mysql_fetch_array($result);
 $bibleTitleId = $myrow['id']; 
}

if($_POST['bookName'])
{
 $query =
 "SELECT * FROM `books`
  WHERE `name`          = \"".mysql_real_escape_string($_POST['bookName'])."\"
  AND   `bibleTitleId`  = \"".$bibleTitleId."\"
  LIMIT 1 
 ";
 $result=mysql_query($query) or die ("<pre>".$query.mysql_error()."</pre>");
 $myrow=mysql_fetch_array($result);
 if($myrow) 
 $bookId = $myrow['id'];
} 

if ($_POST['lastFocus'])
{
	$lastFocus=$_POST['lastFocus'];
}
else
{
	$lastFocus='firsttime';
}


if($_POST['devent']) 
{
 foreach($_POST['cords_p'] as $key=>$coordinates)
 {
  $query =
  "UPDATE `notations` SET
   `coordinates`        = \"".$coordinates."\"
   WHERE `key`          = \"".$key."\"
   AND   `bibleTitleId` = \"".$bibleTitleId."\"
   AND   `bookId`       = \"".$bookId."\"
   LIMIT 1 
   ";
   mysql_query($query) or die ("<pre>".$query."</pre>".mysql_error()."</pre>");
 }
}


// get document image thumbnails
$docDir = 'docs/'.$bookId;
$docSelections = '';
$annotations = '';

$page = 0;
$files = scandir($docDir);
foreach($files as $file)
{
 list($filename, $ext) = explode('.', $file);
 if($ext=='jpg')
 {
  $page++;
  $docSelections .= "
  <p id=\"".$docDir."/".$file."\" onclick=\"setDoc('".$docDir."/".$file."');\">
   ".$file."
   <img class=\"thumbSelection\" src=\"".$docDir."/thumbs/".$file."\">
  </p>
";  
 }
}

$js_coordinates = '';
$query =
"SELECT * FROM `notations`
 WHERE `bibleTitleId` = \"".$bibleTitleId."\"
 AND   `bookId`       = \"".$bookId."\"
 AND   `inactive`    != \"Y\"
 ORDER BY `key`
";
// AND   `quote`        > \"\"
$result=mysql_query($query) or die ("<pre>".$query.mysql_error()."</pre>");
while($myrow=mysql_fetch_array($result))
{
 $style = "style=\"padding-top:5px;\"";
 if($myrow['coordinates']) {$style="style=\"padding-top:5px; color:green;\"";}
 
 $dnotation = '';
 $notations = unserialize($myrow['notation']);
 $paragraphs = unserialize($myrow['paragraph']);
 $i = 0; 
 foreach($notations as $notation) 
 {
  if($paragraphs[$i]=='Y') 
  {
   if($i==0) {$dnotation = "<p />".$dnotation;}
   else {$notation = "<div class=\"paragraph\">&nbsp;&nbsp".$notation."</div>";}
  }
  $dnotation .= $notation." ";
  $i++;
 }

 $annotations .= "
  <div $style name=\"p" . $myrow['key'] . "\" id=\"p" . $myrow['key'] . "\" title=\"" . strip_tags($dnotation) . "\"' onclick=\"focusAnnotation(this)\"\">
   <div style=\"float:right\">
    <img src='images/edit.png'
     onclick=\"editVerse('".$myrow['id']."','".$myrow['key']."');\"
     title='".translate('Edit verse details', $st, 'sys')."'>
    <img src='images/delete.png'
     onclick=\"removeElement('p".$myrow['key']."');\"
     title='".translate('Delete image highlight', $st, 'sys')."'>
   </div>
   <b>".$myrow['key']."</b>
   <input name=\"cords_p[".$myrow['key']."]\" id=\"cords_p".$myrow['key']."\" value=\"".$myrow['coordinates']."\" type=\"hidden\">
  </div>";

//  <br />".$myrow['quote']."

 if($myrow['coordinates']) {$js_coordinates .= "\"p".$myrow['key']."\":\"".$myrow['coordinates']."\",";}
}
$js_coordinates = rtrim($js_coordinates, ",");



// get bible title options
$bibleTitle_options = '';
$query =
"SELECT * FROM `bibleTitles`
 WHERE `code` = \"".mysql_real_escape_string($sec_code)."\"
 ORDER BY `title`
";
$result=mysql_query($query) or die ("<pre>".$query.mysql_error()."</pre>");
while($myrow=mysql_fetch_array($result))
{
 $selected = '';
 if($myrow['title']==$_POST['bibleTitle']) {$selected = 'selected';}
 $bibleTitle_options .= "<option value=\"".$myrow['title']."\" ".$selected.">".$myrow['title'];
}

// get book name options
$bookName_options = '';
$query =
"SELECT * FROM `books`
 WHERE `bibleTitleId` = \"".$bibleTitleId."\"
 ORDER BY `displayOrder`, `name`
";
$result=mysql_query($query) or die ("<pre>".$query.mysql_error()."</pre>");
while($myrow=mysql_fetch_array($result))
{
 $selected = '';
 if($myrow['name']==$_POST['bookName']) {$selected = 'selected';}
 $bookName_options .= "<option value=\"".$myrow['name']."\" ".$selected.">".$myrow['name'];
}


echo "
<!DOCTYPE html>
<html>
<head>
 <meta content=\"text/html; charset=UTF-8\" http-equiv=\"content-type\">
 <title>".translate('Tag image quotes', $st, 'sys')."</title>
 <link REL=\"SHORTCUT ICON\" HREF=\"images/favicon.ico\">
 <link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\">

 <script language=JavaScript>

  imageFile = '".$_POST['imageFile']."';  
  lastPFocus = '';
  lastDFocus = '';
  PFocusCount = 0;
  docFocus = false;
  coordinates = {".$js_coordinates."};

  function setDoc(file)
  {
   PFocusCount = 0;
   if(imageFile) {document.getElementById(imageFile).style.background='';}
   document.getElementById(file).style.background='#ccccff';
   document.getElementById(\"doc\").innerHTML = \"<img src='\"+file+\"' onMouseover='docFocus=true;' onMouseout='docFocus=false;'>\";

   imageFile = file;
   document.getElementById('imageFile').value = file;
   document.getElementById(file).scrollIntoView(true);


   for(key in coordinates)
   {
    var c = coordinates[key].split(',');
    var file = c[0];
    var t    = c[1];
    var l    = c[2];
    var h    = '" . $marker_size . "px'; // originally c[3];
    var w    = '" . $marker_size . "px'; // originally c[4];

    if(file==imageFile)
    {
     var ni = document.getElementById('doc');
     var newdiv = document.createElement('div');
     var divIdName = 'marker_'+key;
     newdiv.setAttribute('id',divIdName);
     newdiv.setAttribute('onMouseover','docFocus=true;');
     newdiv.setAttribute('onclick', 'focusCoord(this)');
     newdiv.style.position = 'absolute';
     newdiv.style.top = t;
     newdiv.style.left = l;
     newdiv.style.width = w;
     newdiv.style.height = h;
     newdiv.style.zIndex = '2';
     newdiv.style.background = 'darkgreen';
     newdiv.style.filter = 'alpha(opacity=50)';
     newdiv.style.MozOpacity = .5;
     newdiv.style.opacity = .5;
     ni.appendChild(newdiv);
    }
   }
  }


  function focusAnnotation(p)
  {
   updateCoordinates();

   var c = document.getElementById('cords_'+p.id).value.split(',');
   var file = c[0];

   if(file && file!=imageFile) {setDoc(file);}

   // unset focus on last annotation 
   if(lastPFocus) 
   {
    document.getElementById(lastPFocus).style.background='';
	marker = document.getElementById('marker_'+lastPFocus);
    if(marker)
    { 
     marker.style.background='darkgreen';
     marker.style.border='';
     PFocusCount = 0;
    }
   }

   // set focus on current annotation
   p.style.background='#ccccff';

   marker = document.getElementById('marker_'+p.id);
   if(marker)
   {
   
    marker.style.border='.1em dotted red';
    marker.style.background = 'limegreen';
    PFocusCount = 1;
   }

   lastPFocus = p.id;
   document.getElementById('lastFocus').value=lastPFocus;   

   document.getElementById('notation').innerHTML=p.title;
  }

  function focusCoord(c)
  {
    p = document.getElementById(c.id.replace('marker_', ''));
	if (p)
	{
		focusAnnotation(p);
		p.scrollIntoView();
	}
  }
  
  function restoreLastFocus(key)
  {
	if (key)
	{
		p = document.getElementById(key);
		if (p)
		{
			focusAnnotation(p);
			p.scrollIntoView();
		}
	}
  }
  
  function updateCoordinates()
  {
   if(document.getElementById('marker_'+lastPFocus))
   {
    var ni = document.getElementById('marker_'+lastPFocus);
    coordinates[lastPFocus] = imageFile +','+ ni.style.top +','+ ni.style.left +','+ ni.style.height +','+ ni.style.width;
    document.getElementById('cords_'+lastPFocus).value = coordinates[lastPFocus];
   }
  }

// Add a green square on the scripture image
  function addElement(t,l)
  {
   var ni = document.getElementById('doc');
   var newdiv = document.createElement('div');
   var divIdName = 'marker_'+lastPFocus;
   newdiv.setAttribute('id',divIdName);
   newdiv.setAttribute('onMouseover','docFocus=true;');
   newdiv.style.position = 'absolute';
   newdiv.style.top = t+'px';
   newdiv.style.left = l+'px';
   newdiv.style.width = '" . $marker_size . "px'; // originally '30px';
   newdiv.style.height = '" . $marker_size . "px'; // originally '30px';
   newdiv.style.border = '.1em dotted red';
   newdiv.style.zIndex = '2';
   newdiv.style.background = '#009900';
   newdiv.style.filter = 'alpha(opacity=50)';
   newdiv.style.MozOpacity = .5;
   newdiv.style.opacity = .5;
   ni.appendChild(newdiv);
   document.getElementById(lastPFocus).style.color = 'darkGreen';
  }

/*
  function extendElement(tt,ll)
  {
   if(document.getElementById('marker_'+lastPFocus))
   {
    var ni = document.getElementById('marker_'+lastPFocus);
    t  = ni.offsetTop;
    l  = ni.offsetLeft;

    if(tt>t) {h = tt-t;} else {h = (t-tt)+parseInt(ni.style.height); t=tt;}
    if(h<30) {h = 30;}

    if(ll>l) {w = ll-l;} else {w = (l-ll)+parseInt(ni.style.width); l=ll;}
    if(w<10) {w = 10;}

    ni.style.top = t+'px';
    ni.style.left = l+'px'; 
    ni.style.height = h+'px';
    ni.style.width  = w+'px'; 
   }
  }
*/

  function removeElement(el)
  {
   coordinates[el]='';
   document.getElementById('cords_'+el).value = '';
   document.getElementById(el).style.color = '';
   var d = document.getElementById(\"doc\");
   var olddiv = document.getElementById('marker_'+el);
   d.removeChild(olddiv);
   PFocusCount = 0;
  }

  // Detect if the browser is IE or not.
  // If it is not IE, we assume that the browser is NS.
  var IE = document.all?true:false;
  var tempX = 0;
  var tempY = 0;
  var sav_tempX = 0;
  var sav_tempY = 0;

  document.onmousemove = getMouseXY;
  function getMouseXY(e)
  {
   if(!sav_tempY) {sav_tempY = tempY;}
   if(!sav_tempX) {sav_tempX = tempX;}

   if (IE) { // grab the x-y pos.s if browser is IE
    if(event && document.body) { 
     tempX = event.clientX + document.body.scrollLeft;
     tempY = event.clientY + document.body.scrollTop;
    }
   } else {  // grab the x-y pos.s if browser is NS
    if(e)
    { 
     tempX = e.pageX + document.body.scrollLeft;
     tempY = e.pageY + document.body.scrollLeft;
    } 
   }  
   // catch possible negative values in NS4
   if (tempX < 0) {tempX = 0;}
   if (tempY < 0) {tempY = 0;}  

   if(mouse_rc_pressed)
   {
    var ni = document.getElementById('marker_'+lastPFocus);
    ni.style.top = ni.offsetTop+(tempY-sav_tempY)+'px';
    ni.style.left = ni.offsetLeft+(tempX-sav_tempX)+'px'; 
   }
   sav_tempX = 0;
   sav_tempY = 0; 
 }


 mouse_lc_pressed = 0;
 mouse_rc_pressed = 0;
 document.onmousedown=click_down;
 function click_down(e)
 {
  if (IE) {
   if(event.button == 1) {
    if(mouse_lc_pressed==0) {mouse_lc_pressed = 1; mouse_rc_pressed = 0;} else {mouse_lc_pressed = 0;}
   }
   if(event.button==2) {
    if(mouse_rc_pressed==0) {mouse_rc_pressed = 1; mouse_lc_pressed = 0;} else {mouse_rc_pressed = 0;}
   }   
  }
  else {
   if(e.which == 1) {
    if(mouse_lc_pressed==0) {mouse_lc_pressed = 1; mouse_rc_pressed = 0;} else {mouse_lc_pressed = 0;}
   }
   if(e.which == 3) {
    if(mouse_rc_pressed==0) {mouse_rc_pressed = 1; mouse_lc_pressed = 0;} else {mouse_rc_pressed = 0;}
   }
  }

  if(docFocus && lastPFocus)
  {


   var t = e.pageY - document.getElementById(\"doc\").offsetTop + document.getElementById(\"doc\").scrollTop;
   var l = e.pageX - document.getElementById(\"doc\").offsetLeft;  
   if(!PFocusCount && mouse_lc_pressed) {addElement(t,l); PFocusCount++;}
//   if(PFocusCount && mouse_lc_pressed)  {extendElement(t,l);}

  }
 }

 document.onmouseup=click_up;
 function click_up()
 {
  mouse_lc_pressed = 0;
  mouse_rc_pressed = 0;
  sav_tempX = 0;
  sav_tempY = 0;
 }
  
 function saveFunc()
 {
  updateCoordinates();
  document.getElementById(\"devent\").value='save';
  document.form1.submit();
 }

 function editVerse(el,key)
 {
  editScreen = window.open('edit.php?id='+el+'&key='+key,'editScreen','status=0,toolbar=0,menubar=0,location=0,titlebar=0,scrollbars=1,width=1150px,height=800px');
  editScreen.focus();
 }

</script>

</head>

<form id=\"form1\" name=\"form1\" action=\"\" method=post enctype=\"multipart/form-data\">

<body onload=\"restoreLastFocus('".$lastFocus."');\">

    <div id=\"docSelect\">
     ".$docSelections."
    </div>
 
    <div id=\"bookSelect\">
     &nbsp;&nbsp;&nbsp;&nbsp;
     ".translate('Bible or Testament name', $st, 'sys')."
     <select name=\"bibleTitle\" onchange=\"submit();\">
      <option value=\"\"> -- ".translate('Select a current title', $st, 'sys')." -- 
      ".$bibleTitle_options."
     </select>
      &nbsp;&nbsp;&nbsp;&nbsp;
     ".translate('Book name', $st, 'sys')."
     <select name=\"bookName\" onchange=\"submit();\">
      <option value=\"\"> -- ".translate('Select a current name', $st, 'sys')." -- 
      ".$bookName_options."
     </select> 
     &nbsp;&nbsp;&nbsp;&nbsp;
     <input name=\"imageFile\" id=\"imageFile\" style=\"border:0;\" type=\"hidden\">
    </div>

    <div id=\"doc\">
		<h3 class=instructionsHeading>BASIC INSTRUCTIONS FOR SYNC TAGGERS:</h3>
		<ul class=instructions>
                        <li style=\"color: #F00; font-weight: bold\">Log out of Last Pass before you start tagging. It slows down the SyncChild.</li>
			<li>Select your assigned Bible and book from the drop-down lists above.</li>
			<li>Select the first image from the column of thumbnails on the left.</li>
			<li>Click once on a verse in the list of chapter/verse numbers on the right, and click again on the equivalent verse number in the jpeg image. A green box (tag) will appear on the jpeg. Continue to the next verse.</li>
			<li>If a ch/vs number in the verse list does not get highlighted when you click on it, click Save, which refreshes the data. If this doesn't work either, exit the sync program (click Home) and try again.</li>
			<li>Click \"Save\" when you finish tagging all the verses on a page, then wait while your data is transferred to the server. The save is complete when the last verse number you tagged appears at the top of the verse list.</li>
			<li>Select the next jpeg thumbnail and continue tagging.</li>
		</ul>
		<h3 class=instructionsHeading>WHERE TO PUT THE GREEN TAGS:</h3>
		<ul class=instructions>
			<li>Always aim high on the horizontal plane. The top edge of the green box must be slightly higher than the tallest letter in the line of Ottoman text, including vowel points above the letters.</li>
			<li>Special cases: Tag 000.000 at the very top of the first jpeg image.</li>
			<li>Tag ***.000, etc. above the chapter header entirely, if necessary on the last line of the previous chapter.</li>
			<li>Tag ***.001, etc. (vs.1 of each chapter) just above the Ottoman letters of the chapter header.</li>
			<li>Tag ***.002 and all subsequent verses on or slightly above the equivalent number on the jpeg image...</li>
			<li>Except that on a new page, please place the first tag above the page header, not on its verse number.</li>
		</ul>
		<h3 class=instructionsHeading>CORRECTING ERRORS:</h3>
		<ul class=instructions>
			<li>When you highlight a previously tagged verse its jpeg will load and the tag will show in light green (lighter than the other tags). You may have to scroll down the jpeg to find the light green tag.</li>
			<li>To remove this tag and adjust its location, click on the red X, then on a new location on the jpeg.</li>
			<li>Please do not click on the little pencil next to the X, unless you are a transcription editor.</li>
		</ul>
    </div>
    <div id=\"notation\"></div>

    <div id=\"pageControlTop\">
     <input type=\"button\" value=\"".translate('Save', $st, 'sys')."\" onclick=\"saveFunc();\"> 
     <input type=button value=\"".translate('Home', $st, 'sys')."\" onclick=\"window.close();\">
    </div>

    <div id=\"annotations\">
      ".$annotations."
    </div>

    <div id=\"pageControlBottom\">
     <input type=\"button\" value=\"".translate('Save', $st, 'sys')."\" onclick=\"saveFunc();\"> 
     <input type=button value=\"".translate('Home', $st, 'sys')."\" onclick=\"window.close();\">
    </div>

 <input name=\"devent\" id=\"devent\" type=\"hidden\">
 <input name=\"bibleTitleId\" value=\"".$bibleTitleId."\" type=\"hidden\">
 <input name=\"bookId\" value=\"".$bookId."\" type=\"hidden\">
 <input name=\"lastFocus\" id=\"lastFocus\" value=\"".$lastFocus."\" type=\"hidden\">


 <script language=JavaScript>
  if(imageFile) {setDoc(imageFile);}
 </script>


</body>
</form>
</html>
";

?>
