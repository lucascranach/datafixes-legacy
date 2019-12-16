<?php


$globals = array();
$globals['entryPoints'] = [];
$globals['entryPoints']["thumbs"] = "/var/www/thumbnails";
$globals['entryPoints']["hiRes"] = "/home/mkpacc/IIPIMAGES/";

$remove = [];
array_push($remove, 'PRIVATE_NONE-P039_FR391C_Overall');
array_push($remove, 'PRIVATE_NONE-P040_FR393B_Overall');
array_push($remove, 'PRIVATE_NONE-P065_FR-none_Overall');
array_push($remove, 'PRIVATE_NONE-P239_FR-none_Overall');
array_push($remove, 'PRIVATE_NONE-P240_FR-none_Overall');
array_push($remove, 'PRIVATE_NONE-P241_FR-none_Overall');
array_push($remove, 'PRIVATE_NONE-P245_FR-none_2006_Overall');
array_push($remove, 'PRIVATE_NONE-P249_FR-none_2015_Overall');
array_push($remove, 'PRIVATE_NONE-P259_FR349D_2010_Overall');
array_push($remove, 'PRIVATE_NONE-P270_FR-none_Overall');
array_push($remove, 'PRIVATE_NONE-P033_FR333_Overall');
array_push($remove, 'PRIVATE_NONE-P275_FR-none_2005_Overall');
array_push($remove, 'PRIVATE_NONE-P135_FRSup018_Overall');
array_push($remove, 'PRIVATE_NONE-P027_FR298_Overall');
$globals['remove'] = $remove;

$rename = [];
array_push($rename, array('CH_PTSS-MAS_A671_FR-none_Overall','CH_PTSS-MAS_A671_FR-none_Overall-002'));
array_push($rename, array('IT_GNAAT_18_FR409D_Overall','IT_GNAAT_18_FR409D_Overall-001'));
array_push($rename, array('US_artic_1935-294_FR197_Overall','US_artic_1935-294_FR197_Overall-001'));
array_push($rename, array('US_artic_1935-295_FR197Overall','US_artic_1935-295_FR197Overall-001'));
array_push($rename, array('PRIVATE_NONE-P272_FR-none_Overall','PRIVATE_NONE-P272_FR-none_Overall-001'));
array_push($rename, array('PRIVATE_NONE-P275_FR-none_2005_Overall-001','PRIVATE_NONE-P275_FR-none_2005_Overall-002'));
array_push($rename, array('PRIVATE_NONE-P064_FR040_Overall','PRIVATE_NONE-P064_FR040_Overall-001'));
array_push($rename, array('PRIVATE_NONE-P213_FR416_Overall','PRIVATE_NONE-P213_FR416_Overall-001'));
array_push($rename, array('PRIVATE_NONE-P185_FR-none_2008_Overall','PRIVATE_NONE-P185_FR-none_2008_Overall-002'));
array_push($rename, array('PRIVATE_NONE-P006_FR054_Overall','PRIVATE_NONE-P006_FR054_Overall-001'));
array_push($rename, array('DE_HHK_NONE-001_FR306_Overall','DE_HHK_NONE-001_FR306_Overall-001'));
array_push($rename, array('DE_HHK_NONE-002_FR307_Overall','DE_HHK_NONE-002_FR307_Overall-001'));
array_push($rename, array('PRIVATE_NONE-P043_FR413A_Overall','PRIVATE_NONE-P043_FR-none_Overall'));
array_push($rename, array('PRIVATE_NONE-P043_FR413A_RKD_Overall-001v','PRIVATE_NONE-P043_FR-none_RKD_Overall-001v'));
array_push($rename, array('PRIVATE_NONE-P043_FR413A_RKD_Overall-001r','PRIVATE_NONE-P043_FR-none_RKD_Overall-001r'));
$globals['rename'] = $rename;

foreach($globals['entryPoints'] as $entryPoint){

  foreach($globals['remove'] as $item){
    $cmd = "find " . $entryPoint ." -name '$item.*'";
    $files = array();
    print "Searching for $item.*\n";
    exec($cmd, $files);
    foreach($files as $file){
      print "  Remove $file\n";
      unlink($file);
    }
  }

  foreach($globals['rename'] as $item){
    $source = $item[0];
    $target = $item[1];
    $cmd = "find " . $entryPoint ." -name '$source.*'";
    $files = array();
    print "\nSearching for $source.*\n";
    exec($cmd, $files);
    foreach($files as $sourceFile){
      $targetFile = preg_replace("=$source=", $target, $sourceFile);
      print "  Rename:\n\t$sourceFile\n\t$targetFile\n";
      rename($sourceFile, $targetFile);
    }
  }
}

print "\nfertig :)\n\n";