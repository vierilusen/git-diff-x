<?php

namespace App\Commands\Generate;

use App\Services\RepoDpService;
use App\Services\RepoMasterService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;


class BlsAppCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'generate:bls_app
                            {branch : branch name repo (required)}
                            {--push : push to repo DP_APP_DB (optional)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate diff files and create BLS_APP repo.';

    /**
     * Https url repo master and dp_app_db.
     *
     * @var string
     */

    protected $httpsMasterRepo = "https://bli-org01@dev.azure.com/bli-org01/BLS/_git/BLS";
    protected $httpsDpAppDb = "https://bli-org01@dev.azure.com/bli-org01/DP_APP_DB/_git/BLS_APP";

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    private RepoMasterService $repoMasterService;
    private RepoDpService $repoDpService;
    public function __construct(RepoMasterService $repoMasterService, RepoDpService $repoDpService)
    {
        $this->repoMasterService = $repoMasterService;
        $this->repoDpService = $repoDpService;
        parent::__construct();
    }

    public function handle()
    {
        $branchName = $this->argument('branch');

        $this->task("Initialize Repo DP", function () use ($branchName) {
            try {
                $bar = $this->output->createProgressBar(100);
                $bar->setFormatDefinition('custom', '[%bar%] %percent:3s%% -- %message%');
                $bar->setFormat('custom');
                $bar->setMessage('Clone Repo...');
                $bar->start();
                    $this->repoDpService->clone($this->httpsDpAppDb);
                    $bar->advance(40);
        
                    $bar->setMessage('Checkout to branch...');
                    $this->repoDpService->checkout($branchName);
                    $bar->advance(30);
                    
                    $bar->setMessage('Rename branch...');
                    $this->repoDpService->rename('XXXFPPTAHUN', $branchName);
                    $bar->advance(30);
        
                    $bar->setMessage('Finish');
                $bar->finish();
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        }, '');

        $this->task("Initialize Repo Master", function () use ($branchName) {
            try {
                $bar = $this->output->createProgressBar(100);
                $bar->setFormatDefinition('custom', '[%bar%] %percent:3s%% -- %message%');
                $bar->setFormat('custom');
                $bar->setMessage('Clone Repo...');
                $bar->start();
                    $this->repoMasterService->clone($this->httpsMasterRepo);
                    $bar->advance(40);
        
                    $bar->setMessage('Checkout to branch...');
                    $this->repoMasterService->checkout($branchName);
                    $bar->advance(30);
                    
                    $bar->setMessage('Generate diff file...');
                    $this->repoMasterService->generateDiffTxt($branchName, 'origin/master');
                    $bar->advance(30);
    
                    $bar->setMessage('Finish');
                $bar->finish();
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        }, '');

        $this->task("Generate Diff Files", function () use ($branchName) {
            try {
                $bar = $this->output->createProgressBar();
                $bar->setFormatDefinition('custom', '[%bar%] %percent:3s%% -- %message%');
                $bar->setFormat('custom');
                $bar->setMessage('Copying Files...');
                $bar->start();
                
                    $filesDiff = $this->repoMasterService->getDiffData();
                    foreach ($filesDiff as $file) {
                        $this->repoDpService->copyFile($this->repoMasterService->getBaseUrlRepoMaster() . "/$file", $file, $branchName);
                        $bar->advance();
                    }

                    $bar->setMessage('Update Backup Data...');
                    $newDataBackup = $this->repoMasterService->getBackupData($branchName);
                    $newFileBackup = implode("\n", $newDataBackup);
                    $this->repoDpService->editFile("$branchName/SourceBackup.txt", $newFileBackup);
                    $bar->advance();

                    $bar->setMessage('Update Backup Views Data...');
                    $newDataBackupViews = $this->repoMasterService->getBackupViewData($branchName);
                    $newFileBackup = implode("\n", $newDataBackupViews);
                    $this->repoDpService->editFile("$branchName/SourceBackupVIEW.txt", $newFileBackup);
                    $bar->advance();

                    $bar->setMessage('Update Version...');
                    $this->repoDpService->updateVersion($branchName, 1);
                    $bar->advance();

                $bar->finish();
                return true;
            } catch (\Throwable $th) {
                throw $th;
                return false;
            }
        }, '');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
