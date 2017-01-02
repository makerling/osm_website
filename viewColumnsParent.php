<?php

/******************************************************************************/
/* Ottoman Turkish project - view documents in multiple columns               */
/******************************************************************************/
/*  Developed by:  Steve Bagwell                                              */
/******************************************************************************/

require "config.php";
require "head.php";
require "columnsScript.php";

echo "
  <script language=JavaScript>
  " . $jsSetStyles . "  
  </script>

  <table class=\"viewColumns\">
   <tr>
    <td>
     <iframe src='viewColumns.php?iso=" . $sec_code . "' width=100%; height=100%;></iframe>
    </td>
    <td>
     <iframe src='viewColumns.php?iso=" . $sec_code . "' width=100%; height=100%;></iframe>
    </td>
    <td>
     <iframe src='viewColumns.php?iso=" . $sec_code . "' width=100%; height=100%;></iframe>
    </td>
   </tr>
  </table>
 </div>
</body>

</html>
";

require "foot.php";
