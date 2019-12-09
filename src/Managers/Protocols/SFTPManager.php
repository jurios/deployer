<?php


namespace Kodilab\Deployer\Managers\Protocols;


use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Managers\ManagerAbstract;
use Kodilab\Deployer\Managers\ManagerInterface;
use phpseclib\Net\SFTP;

class SFTPManager extends ManagerAbstract implements ManagerInterface
{
    /** @var SFTP */
    protected $sftp;

    public function __construct(Configuration $config)
    {
        $this->host = $config->get('manager.sftp.host');
        $this->port = is_null($config->get('manager.sftp.port'))? 21 : $config->get('manager.sftp.port');
        $this->user = $config->get('manager.sftp.user');
        $this->password = $config->get('manager.sftp.password');
        $this->path = $config->get('manager.sftp.path', null);

        parent::__construct($config);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        /*if ($this->ftp) {
            ftp_close($this->ftp);
        }*/
    }

    public function download(string $prod_path, string $local_path = null)
    {
        if (is_null($local_path)) {
            $local_path = $prod_path;
        }

        $status = $this->sftp->get($prod_path, $local_path);

        return $status;

    }

    public function upload(string $local_path, string $prod_path = null)
    {
        if (is_null($prod_path)) {
            $prod_path = $local_path;
        }

        $this->mkdir(dirname($prod_path));

        $status = $this->sftp->put($prod_path, $local_path, SFTP::SOURCE_LOCAL_FILE);

        return $status;
    }

    public function rm(string $prod_path)
    {
        $status = $this->sftp->delete($prod_path);

        return $status;
    }

    public function rmDir(string $prod_path)
    {
        $status = $this->sftp->delete($prod_path, true);

        return $status;
    }

    public function mkDir(string $prod_path)
    {
        $this->sftp->mkdir($prod_path, -1, true);
    }

    protected function startConnection()
    {
        try {
            $this->sftp = new SFTP($this->host, $this->port);

            if (!$this->sftp) {
                throw new \Exception("Connection to FTP failed");
            }

        } catch (\Exception $e) {
            die("FTP not reachable\n");
        }

        try {
            $login = $this->sftp->login($this->user, $this->password);

            if (!$login) {
                throw new \Exception("FTP Authentication failed");
            }

        } catch (\Exception $e) {
            die("FTP Authentication failed");
        }

        if (is_null($this->path)) {
            $this->sftp->chdir($this->path);
        }

        printf("Connected to the FTP Server\n");
    }

    protected function validateConfig()
    {
        $validated = true;

        if (is_null($this->host) || $this->host === '') {
            $validated = false;
        }

        if (is_null($this->port)) {
            $validated = false;
        }

        if (is_null($this->user) || $this->user === '') {
            $validated = false;
        }

        if (is_null($this->password) || $this->password === '') {
            $validated = false;
        }

        if (!$validated) {
            throw new \Exception("SFTP Config is not valid.");
        }
    }
}