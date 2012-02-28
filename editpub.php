<?php
include_once("functions.inc.php");
include_once("bebop.conf.inc.php");
include_once("bibtexentry.php");

function replaceEntry($oldPub, $bibtexCode)
{
  global $bibtex_file;

  $bibtex = @file_get_contents($bibtex_file) or
    die ("Could not read \"$bibtex_file\" on server:\n \"" . $php_errormsg . "\"");

  findEntry($bibtex, $oldPub, $pos0, $end);
  $oldEntry = substr($bibtex, $pos0, $end - $pos0 + 1);
 
  $new_bibtex = substr($bibtex, 0, $pos0) . "\n\n$bibtexCode\n\n" . substr($bibtex, $end+1);

  @file_put_contents($bibtex_file, $new_bibtex) or
    die ("Could not write new \"$bibtex_file\" on server:\n \"" . $php_errormsg . "\"");

  recreateBibtexXML();

  return $oldEntry;
}

function printNewEntry($newID)
{
  global $FAVICON, $BEBOP_HOME;

  print '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
  <head>
    <meta http-equiv="Content-Type" content="text/xhtml; charset=UTF-8" />
    <title>Edit Entry <?php echo $newID; ?></title>
    <link href="site.css" rel="stylesheet" type="text/css" />
    <script id="bebopjs" src="ajax.js" type="text/javascript"></script>
    <?php print "<link rel=\"shortcut icon\" href=\"$FAVICON\" />"; ?>
  </head>
    <?php print "<body onload=\"showCategory('ID', '$newID');\">"; ?>
    <div class="bebop">
      <h3>Edited Entry ...</h3>
      <div class="whitebox">
        <div id="keywordsCloud"></div>
        <div id="CfPTable"><div class="content"></div></div>
      </div>
      <?php print "<a href=\"$BEBOP_HOME\"><h3>Return Home ...</h3></a>"; ?>
    </div>
  </body>
</html>
<?php   
}

function extractBibtexID($bibcode)
{
  $pos0 = strpos($bibcode, '{') + 1;
  $pos1 = strpos($bibcode, ',', $pos0);

  return substr($bibcode, $pos0, $pos1 - $pos0);
}


if(isset($_GET['pub']))
  {
    $pub = $_GET['pub'];

    if (isset($_POST['submit']))
      {
        global $deleted_file;

        $bibtexCode = $_POST['bibtexcode'];
        $bibtexCode = stripslashes($bibtexCode);
        $bibtexCode = str_replace("\r", "", $bibtexCode);

        $oldEntry = replaceEntry($pub, $bibtexCode);
        $newID = extractBibtexID($bibtexCode);

        if (is_string($oldEntry))
          {
            $f = @fopen($deleted_file, 'a') or
              die ("Could not open \"$deleted_file\" on server:\n \"" . $php_errormsg . "\"");
            fputs($f, "\n\n[" . date(DATE_RFC2822) . "] Replacing $pub ....\n");
            fputs($f, $oldEntry);
            fclose($f);

            printNewEntry($newID);
          }

      }
    else
      {
        $xmlfile = 'bibtex.xml';
        $xslfile = 'editpub.xsl';
        $params['pubid'] = $pub;
        $params['favicon'] = $FAVICON;
        echo transform($xmlfile, $xslfile, $params);
      }
  }
else
  {
    include "addpub.php";
  }

?>