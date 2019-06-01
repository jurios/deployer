<?php


namespace Kodilab\Deployer;



use Illuminate\Console\OutputStyle;
use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\ChangeList;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Changes\Rename;
use Kodilab\Deployer\Git\Git;
use Kodilab\Deployer\Managers\ManagerRepository;
use Kodilab\Deployer\Vendor\VendorDiff;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class Deployer
{
    const BUILD_PRODUCTION_FILENAME = 'BUILD.production';
    const COMPOSER_LOCK_PRODUCTION_FILENAME = 'composer.lock.production';
    const COMPOSER_JSON_PRODUCTION_FILENAME = 'composer.json.production';

    /**
     * Project path
     *
     * @var string
     */
    protected $project_path;

    /**
     * Configuration class
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Deployment manager
     *
     * @var Managers\Protocols\FTPManager|Managers\Protocols\SFTPManager|Managers\Protocols\SimulateManager
     */
    protected $manager;

    /**
     * Git helper class
     *
     * @var Git
     */
    protected $git;

    /**
     * Vendor diff helper class
     *
     * @var VendorDiff
     */
    protected $vendor;

    /**
     * Production commit
     *
     * @var array|false|mixed|string|null
     */
    protected $production_commit;

    /**
     * Current environment commit
     *
     * @var mixed|null
     */
    protected $local_commit;

    /**
     * Change list
     *
     * @var ChangeList
     */
    protected $changeList;

    /**
     * The output interface implementation.
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Files to be downloaded before start diff process
     * @var array
     */
    protected $download_files = [
        'BUILD' => self::BUILD_PRODUCTION_FILENAME
    ];

    public function __construct(string $project_path, array $config = [], string $from_commit = null)
    {
        $this->setOutput();
        $this->output->writeln('<fg=green;options=bold>Starting deployer v2.0.0<fg=default>');

        $this->project_path = $project_path;
        $this->config = new Configuration($config);

        $this->output->write('Listing project files...:');
        $this->project = new Project($this->project_path);
        $this->output->writeln('<fg=green>' . count($this->project->files()) . ' files found');


        $this->manager = ManagerRepository::getManager($this->config->get('manager'));

        $this->git = new Git($this->project_path);

        $this->changeList = new ChangeList($this->config);
        $this->changeList->addIncludedFiles($this->project->files());

        $this->retrieveCommits($from_commit);

        $this->checkoutProductionComposerFiles();

        $this->output->writeln("<fg=blue>Getting the difference between " . $this->production_commit . " and " . $this->local_commit);
        $diff = $this->git->diff($this->production_commit, $this->local_commit);
        $this->changeList->merge($diff->changes());

        if ($diff->isVendorChanged()) {
            $this->vendor = new VendorDiff($this->project_path, $this->project);
            $this->changeList->merge($this->vendor->diff());
        }
    }

    public function __get($name)
    {
        if(property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Deploy process
     */
    public function deploy()
    {
        $this->listDeployTasks();

        ProgressBar::setFormatDefinition('custom',
            ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% %message%'
        );

        $progressBar = new ProgressBar($this->output, count($this->changeList->changes()));
        $progressBar->setMessage('Starting deploy...');
        $progressBar->setFormat('custom');

        $progressBar->start();

        /** @var Change $change */
        foreach ($this->changeList->changes() as $change)
        {

            $status = false;

            if (get_class($change) === Add::class) {
                $progressBar->setMessage('Uploading ' . $change->path());
                $this->manager->upload($change->path());
            }

            if (get_class($change) === Modify::class) {
                $progressBar->setMessage('Uploading ' . $change->path());
                $this->manager->upload($change->path());
            }

            if (get_class($change) === Rename::class) {
                $progressBar->setMessage('Removing ' . $change->from());
                $this->manager->delete($change->from());
                $progressBar->setMessage('Uploading ' . $change->from());
                $this->manager->upload($change->to(), $change->to());
            }

            if (get_class($change) === Delete::class) {
                $progressBar->setMessage('Removing ' . $change->path());
                $this->manager->delete($change->path());
            }

            sleep(1);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    /**
     * Show the change list
     */
    private function listDeployTasks()
    {
        $this->output->writeln("\n\n");
        $this->output->title('Change list');

        foreach ($this->changeList->changes() as $change)
        {
            if (get_class($change) === Add::class) {
                $this->output->writeln('<fg=green>[A] ' . $change->path() . '<fg=default>');
            }

            if (get_class($change) === Modify::class) {
                $this->output->writeln('<fg=blue>[M] ' . $change->path() . '<fg=default>');
            }

            if (get_class($change) === Rename::class) {
                $this->output->writeln('<fg=yellow>[R] ' . $change->from() . ' => ' . $this->changes->to() . '<fg=default>');
            }

            if (get_class($change) === Delete::class) {
                $this->output->writeln('<fg=red>[D] ' . $change->path() . '<fg=default>');
            }
        }
    }

    /**
     * Downlaod, using the manger, the deployer files needed from production
     *
     */
    private function downloadBuildFile()
    {
        foreach ($this->download_files as $prod_path => $local_path) {
            try {
                $this->manager->download($prod_path, $this->project_path . DIRECTORY_SEPARATOR . $local_path);
            } catch (\Exception $e) {
                printf("File %s not found.\n", $prod_path);
            }
        }
    }

    /**
     * Get the production and local commits
     *
     * @param string $from_commit
     * @throws \Exception
     */
    private function retrieveCommits(string $from_commit = null)
    {
        if(is_null($from_commit)) {
            $this->downloadBuildFile();
        }

        $this->local_commit = $this->git->getLastCommit();
        $this->production_commit = !is_null($from_commit) ? $from_commit : $this->getProductionCommitFromBuildFile();
    }

    /**
     * Get the production commit from the buid file
     *
     * @return array|false|mixed|string|null
     * @throws \Exception
     */
    private function getProductionCommitFromBuildFile()
    {
        if (!is_null($this->config->get('production_commit'))) {
            $commit = $this->config->get('production_commit');

            if (!isCommitValid($commit)) {
                new \Exception('The configuration commit is not valid');
            }

            return $commit;
        }

        if (file_exists($this->project_path . DIRECTORY_SEPARATOR . self::BUILD_PRODUCTION_FILENAME)) {
            $commit = file_get_contents(self::BUILD_PRODUCTION_PATH);
            $commit = str_replace("\n", "", $commit);

            if (!isCommitValid($commit)) {
                new \Exception('The production commit is not valid');
            }

            return $commit;
        }

        printf("BUILD file not found. Using first project commit as reference\n");
        $commit = $this->git->getEmptyCommit();

        return $commit;
    }

    /**
     * Set the output
     */
    private function setOutput()
    {
        $filename = env('APP_ENV') === 'testing' ? 'php://memory' : 'php://stdout';

        $output = new StreamOutput(fopen($filename, 'w'), OutputInterface::VERBOSITY_NORMAL);
        $this->output = new OutputStyle(new StringInput(''), $output);
    }

    /**
     * Get the production version files
     */
    private function checkoutProductionComposerFiles()
    {
        $this->git->checkout(
            'composer.json', $this->production_commit, $this->project_path . DIRECTORY_SEPARATOR . 'composer.json.production'
        );

        $this->git->checkout(
            'composer.lock', $this->production_commit, $this->project_path . DIRECTORY_SEPARATOR . 'composer.lock.production'
        );
    }
}