# Deployer

## 1 - Disclaimer
This project is still in a pre-release stage. It should work but could contains unexpected bugs. 

Deployer is an unattended script. However, in order to let you check the changes which Deployer is going to do, it will
print the change list and wait 10 seconds before start the process. If you see something unexpected, close the process.

What's more, Deployer can be launched with a `simulated mode` where it simulates the deployment process (no changes are 
done in production) in order to let you check the files which are going to be modified/added/removed.

Please, consider fill an issue if you see a bug or an unexpected behaviour. That would be really useful
to make Deployer better.

## 2 - What's Deployer
Deployer is an unattended script which compares the production project version and the current version in order
to list the files changed (that means: new files, modified files and removed files) and upload/remove them in 
the production environment using SFTP or FTP 
(at this moment, SFTP and FTP are provided but more protocols can be added easily).

It uses git (your project must be included in a Git repository) to compare the versions. And parses the `composer.lock`
file in order to know which composer packages required by the project are new/updated/removed.

### 2.1 - BUILD file
In order to let Deployer know the production environment version, it will generate a file called `BUILD` in the production
environment which contains the commit reference. This file should be reachable for Deployer as it will try to download it
in the initial step of the deployment process.
If Deployer can't get this file, then it will compare from the first commit of the project 
(That means, it will deploy all files tracked).

You can change that behaviour specifying the commit SHA reference manually when you call to Deployer. You can see this
in the `How to use it` section.

## 3 - Getting Started

Deployer has been designed to work in any PHP project (but could be integrated in other kind of project).
If you are developing a Laravel project, a deployer integration package is available here (WIP),

You can install deployer into your project with:

```
composer require kodilab/deployer dev-master
```

### How to use it

You can call to Deployer using a `deploy_it()` helper method. This is the signature of the function:

```(php)
deploy_it(string $project_path, array $config = [], string $from_commit = null)
```

* **project_path** is the path where the project is present
* **config** The configuration array. Explained in the next section.
* **from_commit(optional)** You can define a SHA commit reference instead of download it from the produccion environment.

This is an example of a script which launch Deployer:
 
```(php)
<?php

deploy_it($project_path, $config);

```

## 4 - Configuration
The deployer configuration is an array which is defined when Deployer is instantiated. A configuration array example
is present in `config/config.php` file, you can use it as example. The file contains comments of each parameter 
in order to adapt `deployer` to your needs. Take a look!

### 4.1 - Protocols
Protocols let `deployer` know which is the way deployment must be done. At this moment there are 3 different protocols
implemented: `FTP`, `SFTP` and `Simulate`. The last one, `Simulate`, is an special protocol to simulate a deployment in
order to let you know how `deployer` would work without make any changes into production. 

`Deployer` has been designed to be extensible adding more protocols. 