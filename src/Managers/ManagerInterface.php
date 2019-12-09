<?php


namespace Kodilab\Deployer\Managers;


interface ManagerInterface
{
    /**
     * Download a file
     *
     * @param string $prod_path
     * @param string|null $local_path
     * @return mixed
     */
    public function download(string $prod_path, string $local_path = null);

    /**
     * Upload a file
     *
     * @param string $local_path
     * @param string|null $prod_path
     * @return mixed
     */
    public function upload(string $local_path, string $prod_path = null);

    /**
     * Delete a file
     *
     * @param string $prod_path
     * @return mixed
     */
    public function rm(string $prod_path);

    /**
     * Remove a directory recursively
     *
     * @param string $prod_path
     * @return mixed
     */
    public function rmDir(string $prod_path);

    /**
     * Create a directory recursively
     *
     * @param string $prod_path
     * @return mixed
     */
    public function mkDir(string $prod_path);
}