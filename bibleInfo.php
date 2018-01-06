<?php

/**
 * Ottoman Turkish project - holds information about each Bible/Testament
 *
 **/

$bibleInfo = array();

$bibleInfo["1665 Ali Bey'in el yazması - Eski Ahit"] = array(
    "eng" => "Temp english Bible info", // "1665 Ali Bey'in el yazması - Eski Ahit"
    "tur" => "Temp turkish Bible info",

); // "1665 Ali Bey'in el yazması - Eski Ahit"


$bibleInfo[] = array(
    "eng" => "
", //
    "tur" => "
",
); //

$GLOBALS['bibleInfo'] = $bibleInfo;

/**
  * @param string $bibleKey
  * @param string $langCode 'eng'|'tur'
  *
  * @return string
  */
function getBibleInfo($bibleKey, $langCode) {
    $bibleInfo = $GLOBALS['bibleInfo'];
    $languages = array('tur');

    if ( ! in_array($langCode, $languages)) {
        $langCode = 'eng';
    }

    if (isset($bibleInfo[$bibleKey])) {
        return $bibleInfo[$bibleKey][$langCode];
    }

    return "";
}  
