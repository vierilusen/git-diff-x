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

  public function getBaseUrlRepo()
  {
    return $this->baseUrlRepo;
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

  public function copyFile($sourceFile, $toFile)
  {
    chdir($this->baseUrlRepo);
    $destRepoDp = "$this->baseUrlRepo/$toFile";
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

  public function editFile($filePath, $contentFile)
  {
    chdir($this->baseUrlRepo);
     file_put_contents("$this->baseUrlRepo/$filePath", $contentFile);
  }

  public function generateVersion($oldVersion, $incrementPoint)
  {
    chdir($this->baseUrlRepo);
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
    chdir($this->baseUrlRepo);
    $versionFile = file($this->baseUrlRepo . "/fppno.txt");
    $oldVersion = $versionFile[1];
    $newVersion = $this->generateVersion($oldVersion, $updatePoint);

    file_put_contents($this->baseUrlRepo . "/fppno.txt", "$branchName\n$newVersion");
  }

  public function getDiffData($branchName)
  {
    chdir($this->baseUrlRepo);
    $this->checkout($branchName);
    exec("git ls-files -mo", $output);

    $output = array_map(function ($file) use ($branchName) {
      return str_replace("$branchName/Source/bls/", '', $file);
    }, $output);

    return $output;
  }

  public function restoreFile($branchName, $path)
  {
    chdir($this->baseUrlRepo);
    $this->checkout($branchName);
    
    exec("git ls-files -m", $modifiedFiles);
    foreach ($modifiedFiles as $key => $modifiedFile) {
      if (str_contains($modifiedFile, $path)) {
          exec("git restore *$path", $restore);
      }
    }

    exec("git ls-files -o", $untrackedFiles);
    foreach ($untrackedFiles as $key => $untrackedFile) {
      if (str_contains($untrackedFile, $path)) {
          exec("git clean -fd *$path", $untracked);
      }
    }
  }

}

