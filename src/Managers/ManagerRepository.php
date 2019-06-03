<?php


namespace Kodilab\Deployer\Managers;


use Kodilab\Deployer\Configuration;
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
     * @param Configuration $config
     * @return FTPManager|SFTPManager|SimulateManager
     * @throws \Exception
     */
    public static function getManager(Configuration $config)
    {
        $id = $config->get('manager.protocol');

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
     * @param Configuration $config
     * @return FTPManager
     */
    private static function getFTPManager(Configuration $config)
    {
        return new FTPManager($config);
    }

    /**
     * Returns a SFTPManager instance
     *
     * @param Configuration $config
     * @return SFTPManager
     */
    private static function getSFTPManager(Configuration $config)
    {
        return new SFTPManager($config);
    }

    /**
     * Returns a SimulateManager instance
     *
     * @param Configuration $config
     * @return SimulateManager
     */
    private static function getSimulateManager(Configuration $config)
    {
        return new SimulateManager($config);
    }
}