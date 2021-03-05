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


function filterByDKultType(array $nodes): array {
  $matchingTypeVal = 'dkult Term Identifier';

  return array_filter($nodes, function($node) use($matchingTypeVal) {
    if (!$node->hasAttributes()) {
      return false;
    }

    $typeAttr = $node->attributes->getNamedItem('type');

    if (is_null($typeAttr)) {
      return false;
    }

    return trim($typeAttr->value) === $matchingTypeVal;
  });
}

function extractDKultIdentifier(DomNode $node) {
  if (!$node->hasAttributes() || $node->nodeName !== 'alt-term') {
    return null;
  }

  $termAttr = $node->attributes->getNamedItem('term');

  if (is_null($termAttr)) {
    return null;
  }

  return trim($termAttr->value);
}

function getDKultIdentifier(DomNode $node) {
  $childNodes = iterator_to_array($node->childNodes);
  $altTermNodes = array_filter($childNodes, function($node) { return $node->nodeName === 'alt-term'; });
  $dkultNodes = filterByDKultType($altTermNodes);

  return count($dkultNodes) === 1 ? extractDKultIdentifier(current($dkultNodes)) : null;
}

function sortNodesByDKultIdentifier(DomNode $nodeA, DomNode $nodeB) {
  $nodeAIdentifier = getDKultIdentifier($nodeA);
  $nodeBIdentifier = getDKultIdentifier($nodeB);

  if (is_null($nodeAIdentifier)) {
    if (is_null($nodeBIdentifier)) {
      return 0;
    } else {
      return 1;
    }
  } else {
    if (is_null($nodeBIdentifier)) {
      return -1;
    }

    return $nodeAIdentifier - $nodeBIdentifier;
  }
}

function reappendNodesToTheirParent(array $childNodes) {
  foreach($childNodes as $childNode) {
    $childNode->parentNode->appendChild($childNode->parentNode->removeChild($childNode));
  }
}

/* sorting elements by dKult Identifier */
applyFunctionOnNodeRecursivly(function($node, $depth) {
  if (!$node->hasAttributes() || !$node->hasChildNodes()) {
    return;
  }

  $childNodes = iterator_to_array($node->childNodes);

  $termNodes = array_filter($childNodes, function($node) { return $node->nodeName === 'term'; });

  usort($termNodes, 'sortNodesByDKultIdentifier');

  reappendNodesToTheirParent($termNodes);
}, $dom->documentElement);


$filename = basename($path);
$target = $globals['outFolder']. '/' . $filename;
$xmlOutput = str_replace('<?xml version="1.0"?>' . "\n", '', trim($dom->saveXML()));

file_put_contents($target, $xmlOutput);

print "\nFertig: $target\n\n";