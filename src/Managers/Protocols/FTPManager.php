<?php


namespace Kodilab\Deployer\Managers\Protocols;


use Kodilab\Deployer\Configuration\Configuration;
use Kodilab\Deployer\Managers\ManagerAbstract;
use Kodilab\Deployer\Managers\ManagerInterface;

class FTPManager extends ManagerAbstract implements ManagerInterface
{
    public function __construct(Configuration $config)
    {
        $this->host = $config->get('manager.ftp.host');
        $this->port = is_null($config->get('manager.ftp.port'))? 21 : $config->get('manager.ftp.port');
        $this->user = $config->get('manager.ftp.user');
        $this->password = $config->get('manager.ftp.password');
        $this->path = is_null($config->get('manager.ftp.path'))? "" : $config->get('manager.ftp.path');

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

        $status = ftp_get($this->sftp, $local_path, $prod_path, FTP_BINARY);

        return $status;

    }

    public function upload(string $local_path, string $prod_path = null)
    {
        if (is_null($prod_path)) {
            $prod_path = $local_path;
        }

        $this->mkdir(dirname($prod_path));
        $status = ftp_put($this->sftp, $prod_path, $local_path, FTP_BINARY);

        return $status;
    }

    public function rm(string $prod_path)
    {
        $status = ftp_delete($this->sftp, $prod_path);

        return $status;
    }

    public function rmDir(string $prod_path)
    {
        if ($children = @ftp_nlist ($this->sftp, $prod_path)) {
            foreach ($children as $p) {
                ftp_rdel($this->sftp, $p);
            }
        }

        @ftp_rmdir ($this->sftp, $prod_path);

        return true;
    }

    public function mkDir(string $prod_path)
    {
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $prod_path));

        foreach($parts as $part){
            if(!@ftp_chdir($this->sftp, $part)){
                ftp_mkdir($this->sftp, $part);
                ftp_chdir($this->sftp, $part);
            }
        }
    }

    protected function startConnection()
    {
        try {
            $this->sftp = ftp_connect($this->host, $this->port);

            if (!$this->sftp) {
                throw new \Exception("Connection to FTP failed");
            }

        } catch (\Exception $e) {
            die("FTP not reachable\n");
        }

        try {
            $login = ftp_login($this->sftp, $this->user, $this->password);

            if (!$login) {
                throw new \Exception("FTP Authentication failed");
            }

        } catch (\Exception $e) {
            die("FTP Authentication failed");
        }

        ftp_pasv($this->sftp, true);

        if (!is_null($this->path)) {
            ftp_chdir($this->sftp, $this->path);
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
            throw new \Exception("FTP Config is not valid.");
        }
    }
}