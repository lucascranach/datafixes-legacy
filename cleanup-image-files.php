<?php

require 'vendor/mustache/mustache/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$globals = array();
$globals['inFolder'] = "xml-in";
$globals['outFolder'] = "xml-out";
$globals['searchItems'] = array(
  'Bucheinband', 'Devise', 'Initiale', 'Inschrift', 'Recto', 'Titelblatt', 'Verso', 'Wappen', 
  'Architekturzeichnung','Chronik', 'Entwurfszeichnung', 'Flugblatt', 'Gedenkblatt', 'Politische Karikatur', 'Presentationszeichnung', 'Probedruck', 'Vorzeichnung',
  'Buch', 'Einblatt', 'Frontispiz', 'Gebetbuch', 'I. Zustand', 'II. Zustand', 'III. Zustand', 'Illustriertes Buch', 'Karte', 'Serie', 'Skizzenbuch', 'Stammbuch',
  '1st state',
  '2nd state',
  '3rd state',
  'commemorative print'
);

$globals['template'] = '<term type="Descriptor" term="{{string}}"';

function readFolder($folder)
{
    return glob($folder . "/*.xml");
}

$files = readFolder($globals['inFolder']);

print "\nWas soll gemacht werden?\n";
$x = 1;
foreach ($files as $file) {
    print "[$x] $file\n";
    $x++;
}
print "[x] abbrechen\n";

$input = intval(chop(readline("Command: ")));
if($input == NULL){ exit; }

$path = $files[$input-1];
$xml = file_get_contents($path);

$m = new Mustache_Engine;
foreach($globals['searchItems'] as $searchItem){
  $pattern =  preg_quote($m->render($globals['template'], array('string' => $searchItem)), '=');
  $xml = preg_replace(
    "=$pattern.*?\/term>=is",
    '',
    $xml
  );
}

$filename = basename($path);
$target = $globals['outFolder']. '/' . $filename;
file_put_contents($target, $xml);

print "\nFertig: $target\n\n";