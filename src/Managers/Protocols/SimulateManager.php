<?php


namespace Kodilab\Deployer\Managers\Protocols;


use Kodilab\Deployer\Configuration;
use Kodilab\Deployer\Managers\ManagerAbstract;
use Kodilab\Deployer\Managers\ManagerInterface;

class SimulateManager extends ManagerAbstract implements ManagerInterface
{
    const SLEEP_TIME = 1;

    public function __construct(Configuration $config)
    {
        parent::__construct($config);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        //
    }

    public function download(string $prod_path, string $local_path = null)
    {
        $status = true;

        sleep(self::SLEEP_TIME);

        return $status;
    }

    public function upload(string $local_path, string $prod_path = null)
    {
        $status = true;

        sleep(self::SLEEP_TIME);

        return $status;
    }

    public function delete(string $prod_path)
    {
        $status = true;

        sleep(self::SLEEP_TIME);

        return $status;
    }

    public function rmDir(string $prod_path)
    {
        $status = true;

        return $status;
    }

    public function mkDir(string $prod_path)
    {
        $status = true;

        return $status;
    }

    protected function startConnection()
    {
        printf("Started simulation\n");
    }
}