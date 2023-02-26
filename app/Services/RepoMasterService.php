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

  public function getBaseUrlRepoMaster()
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

  public function checkout($branchName)
  {
    chdir($this->baseUrlRepo);
    exec("git checkout $branchName 2>&1", $output);
    exec("git restore . 2>&1", $output);
    exec("git pull origin $branchName 2>&1", $output);
  }

  public function generateDiffTxt($fromBranch, $toBranch)
  {
     $this->checkout($fromBranch);
     exec("git diff --name-only $toBranch > diff.txt", $output);
  }

  public function getDiffData()
  {
    $files = file("$this->baseUrlRepo/diff.txt", FILE_IGNORE_NEW_LINES);
    return $files;
  }

}

