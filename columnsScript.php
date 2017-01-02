<?php

/******************************************************************************/
/* Ottoman Turkish project - script tag to view documents in multiple columns */
/******************************************************************************/
/*  Developed by:  Steve Bagwell                                              */
/******************************************************************************/


$jsSetStyles = "

  document.getElementsByTagName(\"html\")[0].style.height = '100%';
  document.getElementsByTagName(\"body\")[0].style.height = '97%';
  document.getElementById(\"form1\").style.height = '100%';
  document.getElementById(\"wrapper\").style.height = '100%';

  document.getElementById(\"content\").parentElement.parentElement.style.height = '100%';

  
  document.getElementById(\"content\").id = 'viewColumnsContent';
  document.getElementById(\"viewColumnsContent\").style.height = '100%';

  document.getElementById(\"wrapper\").style.width = '100%';
  document.getElementById(\"viewColumnsContent\").style.width = '98%';

";


$jsSetAnnotations = "

  lastNotation = '';

  // Language abbreviations that need to get a special style
  // Special characters are hard to deal with in vim, so separate them out.
  langAbbrevs = ['Akad.', 'Alm.', 'Ar.', 'Aram.', 'Bulg.', 'Fa.', 'Fra.', 'Lat.',
                 'Mac.', 'Macar.', 'Osm.',
                 'Rum.', 'Skt.', 'E.Yun.', 'Yun.'];
  langAbbrevs.push('İbr.');  // Ruth 1:6, Allah (1665)
  langAbbrevs.push('İng.');
  langAbbrevs.push('İta.')
  langAbbrevs.push('Moğ.');
  langAbbrevs.push('Soğ.');
  langAbbrevs.push('Soğd.');
  langAbbrevs.push('E.Tü.');  // Ruth 1:1, avrat (1665)
  langAbbrevs.push('K.Tü.');
  langAbbrevs.push('Kz.Tü.');  // Ruth 2:21 bay (1665)
  langAbbrevs.push('T.Tü.');
  langAbbrevs.push('U.Tü.');  // Ruth 2:14 etmek (1665)
  langAbbrevs.push('Y.Tü.')
  langAbbrevs.push('Tü.');  // Ruth 1:1, avrat (1665)

 
   // Wrap a string (e.g. mec.) in a span with a class to make italicized
  function replaceLangAbbrLite(origString, replString) {
      spanClass = 'langAbbrLite';
      outString = origString;

      newReplString = '<span class=\"' + spanClass + '\">' + replString + '</span>';
      outString = outString.replace(replString, newReplString);

      return outString;
  }


  // Remove periods from a substring (e.g. Ar.) and wrap in a span with a class
  // to make it bold and italicized
  function replaceLangAbbrev(origString, replString) {
      spanClass = 'langAbbreviation';
      outString = origString;
      while (outString.indexOf(replString) >= 0 ) {
          // Start building the new replacement string
          newReplString = replString;

          // Remove the one or two periods
          newReplString = newReplString.replace('.', '');
          newReplString = newReplString.replace('.', '');

          // Wrap the replacement string in a span
          newReplString = '<span class=\"' + spanClass + '\">' + newReplString + '</span>';
          outString = outString.replace(replString, newReplString);
      }

      return outString;
  }


  function setAnnotations(key) {
      var a = annotations[key].split('^');
      var annDef = a[1];

      // Give the language abbreviations a different style via wrapping in a span
      annDef = replaceLangAbbrLite(annDef, 'mec.');

      for (index = 0; index < langAbbrevs.length; index++) {
          annDef = replaceLangAbbrev(annDef, langAbbrevs[index]);
      }
      document.getElementById(\"viewAnnotations\").innerHTML = '<b>' + a[0] + '</b> : ' + annDef;

      var links = a[2].split('\t');

      for (i =0; i < links.length; i++)
      {
          if (links[i].search('http')) {
              document.getElementById(\"viewAnnotations\").innerHTML += '<p />' + links[i];
          } else {
              if (i == 0) {
                  document.getElementById(\"viewAnnotations\").innerHTML += '<p />';
              } else {
                  document.getElementById(\"viewAnnotations\").innerHTML += '<br>';
              }
              document.getElementById(\"viewAnnotations\").innerHTML += '<a href=\"' + links[i]+ '\" target=\"_blank\">' + links[i]+ '</a>';
          }
      }

      document.getElementById(\"viewAnnotationsDiv\").style.visibility='visible';
      document.getElementById(\"viewAnnotationsDiv\").style.top = '2px';
      document.getElementById(\"viewAnnotationsDiv\").style.left = '2px';
  }

";

$jsScrollFunctions = "

function scroll2Chapter(chapterSelect) {
    id = chapterSelect.value; 
    element = document.getElementById(id);
    element.scrollIntoView(true); // true => align with top
}

";
