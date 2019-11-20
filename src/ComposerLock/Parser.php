<?php


namespace Kodilab\Deployer\ComposerLock;



class Parser
{
    public static function parse(string $composerLockContent): ComposerLock
    {
        $composer = new ComposerLock();

        return $composer;
    }
}
