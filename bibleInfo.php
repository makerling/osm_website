<?php

/**
 * Ottoman Turkish project - holds information about each Bible/Testament
 *
 **/

$bibleInfo = array();

$bibleInfo["1665 Ali Bey'in el yazması - Eski Ahit"] = array(
    "eng" => "Temp english - 1665 Eski Ahit", 
// "1665 Ali Bey'in el yazması - Eski Ahit"
    "tur" => "Temp turkish - 1665 Eski Ahit",
); // "1665 Ali Bey'in el yazması - Eski Ahit"

$bibleInfo["1665 Ali Bey'in el yazması - Yeni Ahit (İncil-i Şerif)"] = array(
    "eng" => "Temp english - 1665 Yeni Ahit", 
// 1665 Ali Bey'in el yazması - Yeni Ahit (İncil-i Şerif)
    "tur" => "Temp turkish - 1665 Yeni Ahit",
); // 1665 Ali Bey'in el yazması - Yeni Ahit (İncil-i Şerif)

$bibleInfo["1739 Tekvin (Yaratılış), terc. Ali Bey, haz. N.Schröder"] = array(
    "eng" => "Temp english - 1739", 
// 1739 Tekvin (Yaratılış), terc. Ali Bey, haz. N.Schröder
    "tur" => "Temp turkish - 1739",
); // 1739 Tekvin (Yaratılış), terc. Ali Bey, haz. N.Schröder

$bibleInfo["1819 Yeni Ahit (Incil), terc. Ali Bey, haz. J.D. Kieffer"] = array(
    "eng" => "Temp english - 1819 Yeni Ahit", 
// "1819 Yeni Ahit (Incil), terc. Ali Bey, haz. J.D. Kieffer"
    "tur" => "Temp turkish - 1819 Yeni Ahit",
); // "1819 Yeni Ahit (Incil), terc. Ali Bey, haz. J.D. Kieffer"

$bibleInfo["1827 ESKİ AHİT, haz. J.D. Kieffer"] = array(
    "eng" => "Temp english - 1827 Eski Ahit",
 // "1827 ESKİ AHİT, haz. J.D. Kieffer"
    "tur" => "Temp turkish - 1827 Eski Ahit",
); // "1827 ESKİ AHİT, haz. J.D. Kieffer"

$bibleInfo["1827 YENİ AHİT (Incil), haz. J.D. Kieffer"] = array(
    "eng" => "Temp english - 1827 Yeni Ahit",
 // "1827 YENİ AHİT (Incil), haz. J.D. Kieffer"
    "tur" => "Temp turkish - 1827 Yeni Ahit",
); // "1827 YENİ AHİT (Incil), haz. J.D. Kieffer"


$bibleInfo["1852 Tekvin ve Zebur, haz. Turabi Efendi"] = array(
    "eng" => "Temp english - 1852",
// "1852 Tekvin ve Zebur, haz. Turabi Efendi"
    "tur" => "Temp turkish - 1852",
); // "1852 Tekvin ve Zebur, haz. Turabi Efendi"

$bibleInfo[] = array(
    "eng" => "Temp english - 1852",
// "1852 Tekvin ve Zebur, haz. Turabi Efendi"
    "tur" => "Temp turkish - 1852",
); //

$bibleInfo["1857 Yeni Ahit (İncil), terc. Turabi Efendi ve J.W. Redhouse"] = array(
    "eng" => "Temp english - 1857 Yeni Ahit ",
// "1857 Yeni Ahit (İncil), terc. Turabi Efendi ve J.W. Redhouse"
    "tur" => "Temp turkish - 1857 Yeni Ahit ",
); // "1857 Yeni Ahit (İncil), terc. Turabi Efendi ve J.W. Redhouse"

$bibleInfo["1866 Yeni Ahit (İncil), terc. Selim Efendi ve W. Schauffler"] = array(
    "eng" => "Temp english - 1866 Yeni Ahit",
// "1866 Yeni Ahit (İncil), terc. Selim Efendi ve W. Schauffler"
    "tur" => "Temp turkish - 1866 Yeni Ahit",
); // "1866 Yeni Ahit (İncil), terc. Selim Efendi ve W. Schauffler"


$bibleInfo["1868 Mezamir, terc. Selim Efendi ve W.Schauffler"] = array(
    "eng" => "Temp english - 1868",
// "1868 Mezamir, terc. Selim Efendi ve W.Schauffler"
    "tur" => "Temp turkish - 1868",
); // "1868 Mezamir, terc. Selim Efendi ve W.Schauffler"

$bibleInfo["1877 Tevrat - terc. Selim Efendi ve W. Schauffler"] = array(
    "eng" => "Temp english - 1877",
// "1877 Tevrat - terc. Selim Efendi ve W. Schauffler"
    "tur" => "Temp turkish - 1877",
); // "1877 Tevrat - terc. Selim Efendi ve W. Schauffler"

$bibleInfo["1886 KİTAB-I MUKADDES"] = array(
    "eng" => "Temp english - 1886",
// "1886 KİTAB-I MUKADDES"
    "tur" => "Temp turkish - 1886",
); // "1886 KİTAB-I MUKADDES"

$bibleInfo["Errata 1665 - Ali Bey'in el yazmasından"] = array(
    "eng" => "Temp english - 1665",
// "Errata 1665 - Ali Bey'in el yazmasından"
    "tur" => "Temp turkish - 1665",
); // "Errata 1665 - Ali Bey'in el yazmasından"



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
