<?php


namespace Kodilab\Deployer\Command;


use Kodilab\Deployer\Deployer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployCommand extends Command
{

    /** @var string */
    protected $project_path;

    /** @var array */
    protected $config;

    /** @var string */
    protected $production_commit;

    public function __construct(string $project_path, array $config = [], $production_commit = null)
    {
        $name = 'deploy';
        parent::__construct($name);

        $this->project_path = $project_path;
        $this->config = $config;
        $this->production_commit = $production_commit;
    }

    protected function configure()
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy the project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $deployer = new Deployer(
            $this->project_path,
            $this->config,
            $io
        );

        $deployer->deploy($this->production_commit);
    }
}
