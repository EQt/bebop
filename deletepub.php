<?php
include_once("bebop.conf.inc.php");

$bibtex_file = 'bibtex.bib';
$deleted_file = 'deleted.bib';

function recreateBibtexXML()
{
  global $JAVA_EXECUTABLE, $bibtex_file;

  $cmd = $JAVA_EXECUTABLE . " -jar bib2xml/bib2xml.jar ". $bibtex_file;
  unset($array);
  $output = exec($cmd, $array, $ret);
  if ($ret <> 0)
    die ("Could not call $JAVA_EXECUTABLE to recreate bibtex.xml!\n"
         ."Command: \"$cmd\".\n"
         ."Return Value: $ret\n"
         ."Output:  \"$output\"\n");
}

function findEntry($bibtex, $pub, & $pos0, & $end)
{
  global $bibtex_file;

 // find $pub-Entry in $bibtex
  $pattern = '{' . $pub . ',';  // ggf. noch Leerzeichen dazwischen...
  $len = strlen($bibtex);
  if($len <= 0) die ("File \"$bibtex_file\" seems empty...");
  $pos1 = strpos($bibtex, $pattern);

  // found?
  if ($pos1 === false)
    die ("No entry with ID \"$pub\" found in \"$bibtex_file\".");

  // unique?
  $pos2 = strpos($bibtex, $pattern, $pos1 + strlen($pattern) -1);
  if ($pos2 !== false)
    {
      $line1 = substr_count($bibtex, "\n", 0, $pos1);
      $line2 = substr_count($bibtex, "\n", 0, $pos2);
      die ("There is more than one entry with ID \"$pub\" in \"$bibtex_file\": "
           . "At line $line1 and line $line2");
    }

  // find beginning @... tag
  $pos0 = strrpos($bibtex, "@", $pos1 + 1 - $len);
  if ($pos0 > 0 && $bibtex[$pos0 -1] == '\n')
    $pos0--;
  if ($pos0 > 0 && $bibtex[$pos0 -1] == '\n')
    $pos0--;

  // find ending '}' by counting braces
  $nbraces = 1;
  for ($i = $pos1+strlen($pattern)-1; $i < $len; $i++)
    {
      $c = $bibtex[$i];
      if ($c== '{')
        $nbraces++;
      elseif ($c == '}')
        {
          $nbraces--;
          if ($nbraces <= 0)
            break;
        }
    }

  if ($i == $len)
    die ("Entry with ID \"$pub\" does not end with '}' ...");

  $end = $i;
  if ($end < $len -1 && $bibtex[$end+1] == '\n')
    $end++;

  if ($end < $len -1 && $bibtex[$end+1] == '\n')
    $end++;

}

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