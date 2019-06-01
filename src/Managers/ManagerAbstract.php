<?php


namespace Kodilab\Deployer\Managers;


class ManagerAbstract
{
    protected $host;

    protected $port;

    protected $user;

    protected $password;

    protected $path;

    protected $sftp;

    public function __construct($config = [])
    {
        $this->validateConfig();
        $this->startConnection();
    }

    protected function startConnection()
    {
        //
    }

    protected function generateRemotePath($file)
    {
        return $this->path . $file;
    }

    protected function validateConfig()
    {
        //
    }
}