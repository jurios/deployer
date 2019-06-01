<?php


namespace Kodilab\Deployer\Managers;


use Kodilab\Deployer\Managers\Protocols\FTPManager;
use Kodilab\Deployer\Managers\Protocols\SFTPManager;
use Kodilab\Deployer\Managers\Protocols\SimulateManager;

class ManagerRepository
{
    const FTP = 'ftp';
    const SFTP = 'sftp';
    const SIMULATE = 'simulate';

    /**
     * Returns the Manager
     *
     * @param string $id
     * @param array $config
     * @return FTPManager|SFTPManager|SimulateManager
     * @throws \Exception
     */
    public static function getManager(array $config)
    {
        $id = $config['protocol'];

        if ($id === self::FTP) {
            return self::getFTPManager($config);
        }

        if ($id === self::SFTP) {
            return self::getSFTPManager($config);
        }

        if ($id === self::SIMULATE) {
            return self::getSimulateManager($config);
        }

        throw new \Exception('Manager with id ' . $id . ' unknown');
    }

    /**
     * Returns a FTPManager instance
     *
     * @param array $config
     * @return FTPManager
     */
    private static function getFTPManager(array $config)
    {
        return new FTPManager($config);
    }

    /**
     * Returns a SFTPManager instance
     *
     * @param array $config
     * @return SFTPManager
     */
    private static function getSFTPManager(array $config)
    {
        return new SFTPManager($config);
    }

    /**
     * Returns a SimulateManager instance
     *
     * @param array $config
     * @return SimulateManager
     */
    private static function getSimulateManager(array $config)
    {
        return new SimulateManager($config);
    }
}