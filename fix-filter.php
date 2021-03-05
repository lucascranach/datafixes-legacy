<?php

$globals = array();
$globals['inFolder'] = "xml-in";
$globals['outFolder'] = "xml-out";
$globals['searchItems'] = array(
  'Bucheinband', 'Devise', 'Initiale', 'Inschrift', 'Recto', 'Titelblatt', 'Verso', 'Wappen', 
  'Architekturzeichnung', 'Chronik', 'Entwurfszeichnung', 'Flugblatt', 'Gedenkblatt', 'Politische Karikatur', 'Presentationszeichnung', 'Probedruck', 'Vorzeichnung',
  'Buch', 'Einblatt', 'Frontispiz', 'Gebetbuch', 'I. Zustand', 'II. Zustand', 'III. Zustand', 'Illustriertes Buch', 'Karte', 'Serie', 'Skizzenbuch', 'Stammbuch',
  '1st state',
  '2nd state',
  '3rd state',
  'commemorative print'
);

function readFolder(string $folder)
{
    return glob($folder . "/*.xml");
}

function applyFunctionOnNodeRecursivly(callable $func, DOMNode $node, $depth = 0) {
  call_user_func($func, $node, $depth);

  for($i = $node->childNodes->length - 1; $i >= 0; $i -= 1) {
    applyFunctionOnNodeRecursivly($func, $node->childNodes->item($i), $depth  + 1);
  }
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

$dom = new DOMDocument();
$dom->load($path);

/* Remove nodes */
applyFunctionOnNodeRecursivly(function($node) use ($globals) {
  if (!$node->hasAttributes() || $node->nodeName !== 'term') {
    return;
  }

  $termAttr = $node->attributes->getNamedItem('term');

  if (is_null($termAttr)) {
    return;
  }

  $termValue = $termAttr->value;

  foreach($globals['searchItems'] as $searchItem) {
    if (in_array($termValue, $globals['searchItems'])) {
      $node->parentNode->removeChild($node);
      break;
    }
  }
}, $dom->documentElement);

$filename = basename($path);
$target = $globals['outFolder']. '/' . $filename;
$xmlOutput = str_replace('<?xml version="1.0"?>' . "\n", '', trim($dom->saveXML()));

file_put_contents($target, $xmlOutput);

print "\nFertig: $target\n\n";