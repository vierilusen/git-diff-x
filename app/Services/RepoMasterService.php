<?php
namespace App\Services;

class RepoMasterService
{
  protected $basePath;
  protected $baseUrlRepo;

  public function __construct() 
  {
      $this->basePath = base_path();
      $this->baseUrlRepo = base_path("/temp/repomaster");
  }

  public function getBaseUrlRepo()
  {
      return $this->baseUrlRepo;
  }

  public function clone($httpsUrl) 
  {
    if (!is_dir($this->baseUrlRepo)) {
      chdir($this->basePath);
      exec("git clone $httpsUrl temp/repomaster 2>&1", $output);
    }

    chdir($this->baseUrlRepo);
    exec("git checkout master 2>&1", $output);
    exec("git pull origin 2>&1", $output);
  }

  public function checkout($branchName, $restore = true)
  {
    chdir($this->baseUrlRepo);
    exec("git checkout $branchName 2>&1", $output);

    if ($restore) {
      exec("git restore . 2>&1", $output);
    }

    exec("git pull origin $branchName 2>&1", $output);
  }

  public function generateDiffTxt($fromBranch, $toBranch)
  {
    chdir($this->baseUrlRepo);
     $this->checkout($fromBranch);
     exec("git diff --name-only $toBranch > diff.txt", $output);
  }

  public function getDiffData()
  {
    chdir($this->baseUrlRepo);
    $files = file("$this->baseUrlRepo/diff.txt", FILE_IGNORE_NEW_LINES);
    return $files;
  }

  public function getValidBackupData($dataDiff, $branchName)
  {
    chdir($this->baseUrlRepo);
    $this->checkout('master');

    $arrayFiles = [];
    foreach ($dataDiff as $key => $value) {
      if (!str_contains($value, 'app/views') && file_exists("$this->baseUrlRepo/$value")) {
        $arrayFiles[] = "/$value";
      }
    }

    $this->checkout($branchName);
    return $arrayFiles;
  }

  public function getValidBackupViewData($dataDiff, $branchName)
  {
    chdir($this->baseUrlRepo);
    $this->checkout('master');

    $arrayFiles = [];
    foreach ($dataDiff as $key => $value) {
      $viewFolder = explode("/", $value);
      if (str_contains($value, 'app/views/') && is_dir("$this->baseUrlRepo/app/views/$viewFolder[2]")) {
        $arrayFiles[] = "/app/views/$viewFolder[2]";
      }
    }

    $this->checkout($branchName);
    return array_unique($arrayFiles);
  }

  public function getDiffMerge($fromBranch, $toBranch)
  {
    chdir($this->baseUrlRepo);
    $this->checkout($fromBranch);
    exec("git diff --name-only $fromBranch...$toBranch", $output);

    return $output;
  }

}

