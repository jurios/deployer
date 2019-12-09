<?php


namespace Kodilab\Deployer;



use Illuminate\Filesystem\Filesystem;
use Kodilab\Deployer\Changes\ChangeList;
use Kodilab\Deployer\ComposerLock\ComposerLock;
use Kodilab\Deployer\ComposerLock\ComposerLockComparator;
use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Git\Commit;
use Kodilab\Deployer\Git\Git;
use Kodilab\Deployer\Managers\ManagerAbstract;
use Kodilab\Deployer\Managers\ManagerRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

class Deployer
{
    const VERSION = '1.0.0';

    const BUILD_FILEPATH = 'BUILD';
    const PRODUCTION_BUILD_FILEPATH = 'BUILD.production';
    const PRODUCTION_COMPOSERLOCK_FILEPATH = 'composer.lock.production';


    /** @var string */
    protected $project_path;

    /**
     * Configuration class
     *
     * @var Configuration
     */
    protected $config;

    /**
     * Git helper class
     *
     * @var Git
     */
    protected $git;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ManagerAbstract
     */
    protected $manager;

    /**
     * Deployer constructor.
     *
     * @param string $project_path
     * @param array $config
     * @param SymfonyStyle $output
     * @throws \Exception
     */
    public function __construct(string $project_path, array $config = [], SymfonyStyle $output = null)
    {
        $this->output = $output;
        $this->output->title('<fg=green;options=bold>Starting deployer ' . self::VERSION .'<fg=default>');

        $this->project_path = $project_path;
        $this->config = new Configuration($config);
        $this->manager = ManagerRepository::getManager($this->config);
        $this->git = new Git($this->project_path);
    }

    /**
     * Deploy process
     * @param string|null $production_commit
     * @param string|null $local_commit
     * @throws \Exception
     */
    public function deploy(string $production_commit = null, string $local_commit = null)
    {
        // Get the given commit or get it remotely from production
        $production_commit = $this->getProductionCommit($production_commit);

        // Get the given commit or get the last one in the log
        $local_commit = $this->getLocalCommit($local_commit);

        $changeList = new ChangeList($this->config, $this->project_path);

        $this->checkoutComposerLockTo($production_commit, self::PRODUCTION_COMPOSERLOCK_FILEPATH);

        $composerlock_local = new ComposerLock(json_decode(file_get_contents('composer.lock'), true));
        $composerlock_production = new ComposerLock(json_decode(file_get_contents(self::PRODUCTION_COMPOSERLOCK_FILEPATH), true));

        $this->generateLocalBuildFile($local_commit);

        $this->output->writeln(
            sprintf("Indexing changes from <fg=cyan;options=bold>%s</> to <fg=cyan;options=bold>%s</>",
                $production_commit, $local_commit
            )
        );

        $diff = $this->git->diff($production_commit, $local_commit);
        $changeList->merge($diff->getEntries());

        $dep_diff = (new ComposerLockComparator($composerlock_local, $composerlock_production))->compare();
        $changeList->merge($dep_diff);

        $changeList->outputConfirmedList($this->output);

        $this->output->writeln("Deployment finished successfuly. Generating new BUILD file");
    }

    /**
     * Returns Commit from the given reference or get it remotely
     *
     * @param string|null $commit
     * @return Commit
     * @throws Exceptions\InvalidCommitSHAReference
     * @throws \Exception
     */
    protected function getProductionCommit(string $commit = null)
    {
        if (!is_null($commit)) {
            return new Commit($commit);
        }

        if (is_null($commit) && $this->config->get('manager.protocol') === 'simulate') {
            throw new \InvalidArgumentException('A production commit must be provided');
        }

        try {
            $this->downloadBuildFile();
        } catch (\Exception $e) {
            $this->output->writeln('Production BUILD file not found. Starting first deployment');
            /*
             * If the file does not exists in production means that this is the first deployment. In this special case,
             * we use the "empty commit". This is an special commit which refers to the initial state where the project
             * doesn't contains any file in order to upload all files
             */
            $this->generateProductionBuildFileWithEmptyCommit();
        }

        return new Commit($this->getBuildFileContent());
    }

    /**
     * Returns a Commit from the given commit or get the last commit performed in the project
     *
     * @param string|null $commit
     * @return Commit
     * @throws Exceptions\InvalidCommitSHAReference
     * @throws \Exception
     */
    protected function getLocalCommit(string $commit = null)
    {
        if (!is_null($commit)) {
            return new Commit($commit);
        }

        return $this->git->getLastCommit();
    }

    /**
     * Generates a build.production file with a "empty commit" reference on it in order to start
     * the first deployment process
     *
     * @throws \Exception
     */
    protected function generateProductionBuildFileWithEmptyCommit()
    {
        file_put_contents(
            $this->git->getEmptyCommit(),
            static::PRODUCTION_BUILD_FILEPATH
        );
    }

    /**
     * Download the BUILD file from production
     *
     */
    private function downloadBuildFile()
    {
        $this->manager->download(self::BUILD_FILEPATH, self::PRODUCTION_BUILD_FILEPATH);
    }

    /**
     * Get the production commit from the production BUILD file
     *
     * @return array|false|mixed|string|null
     * @throws \Exception
     */
    private function getBuildFileContent()
    {
        if (!file_exists(self::PRODUCTION_BUILD_FILEPATH)) {
            throw new \Exception('Production BUILD file not found:' . self::PRODUCTION_BUILD_FILEPATH);
        }

        $commit = file_get_contents(self::PRODUCTION_BUILD_FILEPATH);
        $commit = trim($commit);

        return $commit;
    }

    /**
     * Checkout the composer.lock file and save it to path
     *
     * @param Commit $production_commit
     * @param string $path
     */
    private function checkoutComposerLockTo(Commit $production_commit, string $path)
    {
        $this->git->checkoutFileToCommit(
            'composer.lock', $production_commit, $path
        );
    }

    /**
     * Generate the BUILD file to be deployed
     *
     * @param Commit $local_commit
     * @return void
     */
    private function generateLocalBuildFile(Commit $local_commit)
    {
        file_put_contents(self::BUILD_FILEPATH, $local_commit->getReference());
    }
}
