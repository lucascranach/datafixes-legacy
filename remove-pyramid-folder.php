<?php


$globals = array();
$globals['entryPoints']["local-imageserver"] = "/Volumes/LaCieCn/cranach-data/test";
$globals['patterns'] = array();
array_push($globals['patterns'], "pyramid");
// array_push($globals['patterns'], "01_Overall");


foreach($globals['patterns'] as $pattern){

  foreach($globals['entryPoints'] as $entryPoint){

    $cmd = "find " . $entryPoint ." -type d -name $pattern";
    $folders = array();
    exec($cmd, $folders);

    foreach($folders as $folder){
      $cmd = "find " . $folder ." -type d -maxdepth 1";
      $subfolders = array();
      exec($cmd, $subfolders);
      array_shift($subfolders);
      
      foreach($subfolders as $subfolder){
        $cmd = "find " . $subfolder ." -type f -maxdepth 1";
        $files = array();
        exec($cmd, $files);

        $source = $subfolder;
        $grep = '=/'.$pattern.'=';
        $target = preg_replace($grep, "", $subfolder);
        $cmd = 'rsync -av '. $source . ' ' .$target;

        print "$cmd\n";

        foreach($files as $file){
          $source = $file;
          $grep = '=/'.$pattern.'=';
          $target = preg_replace($grep, "", $file);
          
          //print "$source -> $target\n";
        }
        

     //   $source = $subfolder;
     //   $target = preg_replace("=/pyramid=", "", $source);
    //  $cmd = "mv $source $target";
      //  $log = array();
        
        //exec($cmd, $log);
      }
  
      //rmdir($folder);
    }
  
  }
}



print "\nfertig :)\n\n";