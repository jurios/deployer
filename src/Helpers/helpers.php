<?php

use Symfony\Component\Console\Application;

if(!function_exists('isCommitValid')) {
    function isCommitValid(string $commit)
    {
        return preg_match("/[a-z0-9]{40}/", $commit);
    }
}

if (!function_exists('deploy_it')) {
    function deploy_it(string $project_path, array $config = [], string $production_commit = null) {

        $app = new Symfony\Component\Console\Application('Deployer', \Kodilab\Deployer\Deployer::VERSION);

        $app->add(new \Kodilab\Deployer\Command\DeployCommand($project_path, $config, $production_commit));

        $app->run(new \Symfony\Component\Console\Input\StringInput('deploy'));
    }
}
