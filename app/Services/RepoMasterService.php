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
    chdir($this->basePath);
    exec("git clone $httpsUrl temp/repomaster 2>/dev/null", $output);
  }

  public function checkout($branchName)
  {
    chdir($this->baseUrlRepo);
    exec("git checkout $branchName 2>/dev/null", $output);
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

