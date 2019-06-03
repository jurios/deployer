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
WIP

### How to call it?
WIP

### Example

```(php)
<?php

use Kodilab\Deployer\Deployer;

$config = []; //See "Configuration" section

$deployer = new Deployer($project_path, $config); // See "How to call it" section
$deployer->deploy();

```
