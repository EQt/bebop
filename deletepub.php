<?php
include_once("bebop.conf.inc.php");
include_once("bibtexentry.php");

function deleteEntry($pub)
{
  global $bibtex_file;

  $bibtex = @file_get_contents($bibtex_file) or
    die ("Could not read \"$bibtex_file\" on server:\n \"" . $php_errormsg . "\"");

  findEntry($bibtex, $pub, $pos0, $end);
 
  $entry = substr($bibtex, $pos0, $end - $pos0 + 1);

  // delete entry
  $new_bibtex = substr($bibtex, 0, $pos0) . substr($bibtex, $end+1);

  @file_put_contents($bibtex_file, $new_bibtex) or
    die ("Could not write new \"$bibtex_file\" on server:\n \"" . $php_errormsg . "\"");

  recreateBibtexXML();

  return $entry;
}


if(isset($_GET['pub']))
  {
    global $deleted_file;

    $pub = $_GET['pub'];

    $f = @fopen($deleted_file, 'a') or
      die ("Could not open \"$deleted_file\" on server:\n \"" . $php_errormsg . "\"");

    $entry = deleteEntry($pub);
    if (is_string($entry))
      {
        fputs($f, "\n\n[" . date(DATE_RFC2822) . "] Deleting $pub ....\n");
        fputs($f, $entry);
      }
    fclose($f);

    echo "1";   // Signal, that everything is OK
  }
else
  echo "No pub to delete...";

?>