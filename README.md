# Deployer

## Disclaimer
This project is still in a pre-release stage. It should work but could contains unexpected bugs. 

Deployer is an unattended script. However, in order to let you check the changes which Deployer is going to do, it will
print the change list and wait 10 seconds before start the process. If you see something unexpected, close the process.

What's more, Deployer can be launched with a `simulated mode` where it simulates the deployment process (no changes are 
done in production) in order to let you check the files which are going to be modified/added/removed.

Please, consider fill an issue if you see a bug or an unexpected behaviour. That would be really useful
to make Deployer better.

## What's Deployer
Deployer is an unattended script which compares the production project version and the current version in order
to list the files changed (that means: new files, modified files and removed files) and upload/remove them in the production
environment using SFTP or FTP (at this moment, SFTP and FTP are provided but more protocols can be added easily).

It uses git (your project must be included in a Git repository) to compare the versions. And parses the `composer.lock`
file in order to know which composer packages required by the project are new/updated/removed.

### BUILD file
In order to let Deployer know the production environment version, it will generate a file called `BUILD` which
contains the commit reference in the production environment. This file should be reachable for Deployer. 
If Deployer can't get this file, then it will compare from the first commit of the project. (That means, it will deploy all files tracked)

You can change that behaviour specifying the commit SHA reference manually in the Deployer call. You can see this
in the `How to use it` section.

## Getting Started

Deployer has been designed to work in any PHP project (but could be integrated in other kind of project).
If you are developing a Laravel project, a deployer integration package is available here (WIP),

### Add deployer as dependency of your project:

If you are using Laravel, please follow the instruction of the deployer's package here (WIP). Otherwise,
follow these steps:

```
composer require kodilab/deployer dev-master
```

### Configuration
The deployer configuration is an array which is defined when Deployer is instantiated. A configuration example
is present in `config/config.php`. That file returns an array so you can use it directly in the Deployer instance.

The `config/config.php` file has comments and explanations so you can modify as you want.

### How to use it

This is an example of a script which launch Deployer:
 
```(php)
<?php

use Kodilab\Deployer\Deployer;

$config = []; //See "Configuration" section

$deployer = new Deployer($project_path, $config);
$deployer->deploy();

```

The signature of the Deployer's constructor is:

```(php)
Deployer(string $project_path, array $config = [], string $from_commit = null)
```

* **project_path** is the path where the project is present
* **config** The configuration array
* **from_commit(optional)** You can define a SHA commit reference instead of download it from the produccion environment.
