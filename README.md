# EZBackup - PHP Class Library
**EZBackup** is a PHP class library for backing up and restoring MySQL databases to/from **CSV**, **JSON**, and **XLS** files.

### Installation
You can install **EZBackup** via **Composer**. If you're not familiar with Composer, you can learn more about it [here](https://getcomposer.org/doc/00-intro.md "here").

First, make sure you have Composer installed on your machine. You can download and install Composer from [here.](https://getcomposer.org/doc/00-intro.md "here.")

Next, navigate to your project directory in the terminal/command prompt.

Run the following command to install **EZBackup** and its dependencies:
```php
    composer require iamjohndev/ijd-ezbackup
```
Once **Composer** has finished installing the library, you can start using it in your project.

### Usage
To use **EZBackup**, you'll first need to create an instance of the **BackupAndRestore** class and set its configuration. Here's an example:

```php
use IamJohnDevEZBackup\BackupAndRestore;

// Create a new BackupAndRestore object
$backup = new BackupAndRestore();

// Set the database configuration
$backup->setConfig('localhost', 'username', 'password', 'database', 'path/to/export_file', 'path/to/import_file');

```
Once you`ve set the configuration, you can call any of the available backup/restore methods:
```php
// Backup the database to a CSV file
$backup->backupToCSV();

// Backup the database to a JSON file
$backup->backupToJSON();

// Backup the database to an XLS file
$backup->backupToXLS();

// Restore the database from a CSV file
$backup->restoreFromCSV($_FILES['csv_file']);

// Restore the database from a JSON file
$backup->restoreFromJSON($_FILES['json_file']);

// Restore the database from an XLS file
$backup->restoreFromXLS($_FILES['xls_file']);

```

# License
**EZBackup** is open-sourced software licensed under the[ **MIT license.**](https://opensource.org/licenses/MIT " **MIT license.**")
