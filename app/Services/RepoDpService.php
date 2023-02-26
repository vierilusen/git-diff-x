<?php
namespace App\Services;

class RepoDpService
{
  protected $basePath;
  protected $baseUrlRepo;

  public function __construct() 
  {
      $this->basePath = base_path();
      $this->baseUrlRepo = base_path("/temp/repodp");
  }

  public function clone($httpsUrl) 
  {
    if (!is_dir($this->baseUrlRepo)) {
      chdir($this->basePath);
      exec("git clone $httpsUrl temp/repodp 2>&1", $output);
    }

    chdir($this->baseUrlRepo);
    exec("git checkout master 2>&1", $output);
    exec("git pull origin 2>&1", $output);
  }

  public function checkout($branchName)
  {
    chdir($this->baseUrlRepo);
    exec("git checkout $branchName 2>&1", $output);
    exec("git restore . 2>&1", $output);
    exec("git pull origin $branchName 2>&1", $output);
  }

  public function rename($oldName, $newName)
  {
    chdir($this->baseUrlRepo);
    if (is_dir($oldName)) {
      rename($oldName, $newName);
    }
  }

  public function changeFile($sourceFile, $toFile, $branchName)
  {
    $destRepoDp = "$this->baseUrlRepo/$branchName/Source/bls/$toFile";
    $destPathInfo = pathinfo($destRepoDp);
    $destDirname = $destPathInfo['dirname'];

    if(!is_dir($destDirname)) {
      mkdir($destDirname, 0777, true);
    }

    if (!touch($destRepoDp) || !copy($sourceFile, $destRepoDp)) {
      $errors= error_get_last();
      echo "COPY ERROR: ".$errors['type'];
      echo "<br />\n".$errors['message'];
      return false;
    }
  }

  public function generateVersion($oldVersion, $incrementPoint)
  {
    $a = explode('.', $oldVersion);
    if($incrementPoint > 0){
        $a[count($a)-1]++;
    } elseif ($incrementPoint < 0){
        $a[count($a)-1]--;
    }
    return implode('.', $a); 
  }

  public function updateVersion($branchName, $updatePoint)
  {
    $versionFile = file($this->baseUrlRepo . "/fppno.txt");
    $oldVersion = $versionFile[1];
    $newVersion = $this->generateVersion($oldVersion, $updatePoint);

    file_put_contents($this->baseUrlRepo . "/fppno.txt", "$branchName\n$newVersion");
  }

}

