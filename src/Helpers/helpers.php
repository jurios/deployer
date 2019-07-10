<?php

use Symfony\Component\Console\Application;

/**
 * Returns whether is a valid commit SHA reference
 *
 * @param string $commit
 * @return false|int
 */
function isCommitValid(string $commit)
{
    return preg_match("/[a-z0-9]{40}/", $commit);
}

/**
 * Helper method to start a deploy process
 *
 * @param string $project_path
 * @param array $config
 * @param string|null $production_commit
 * @throws Exception
 */
function deploy_it(string $project_path, array $config = [], string $production_commit = null) {

    $app = new Symfony\Component\Console\Application('Deployer', \Kodilab\Deployer\Deployer::VERSION);

    $app->add(new \Kodilab\Deployer\Command\DeployCommand($project_path, $config, $production_commit));

    $app->run(new \Symfony\Component\Console\Input\StringInput('deploy'));
}


/**
 * Returns whether the path matches for the given rules. Matched rules are listed in $matched_rules
 *
 * @param array $rules
 * @param string $path
 * @param array $matched_rules
 * @return bool
 */
function matchPath(array $rules, string $path, &$matched_rules = [])
{
    $match = false;

    foreach ($rules as $rule){
        if (fnmatch($rule, $path)) {
            $matched_rules[] = $rule;
            $match = true;
        }
    }

    return $match;
}
