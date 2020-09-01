# [WIP] Bringing a little class to your filesystem operations

This package seeks to make your filesystem operations easier by bringing in some easy-to-use verbal methods.

## To Install

Run `composer require anteris/file-explorer:dev-master`

**Requirements**
- PHP ^7.4 for stricter type casting.
- [Spatie Data-Transfer-Objects](https://github.com/spatie/data-transfer-objects) for file objects.
- [Symfony Filesystem](https://github.com/symfony/filesystem) for their existing easy-to-use functions.

# Getting Started

To get started with this package, create a new instance of the `FileExplorer` class. If you pass a directory to the constructor, this will be your starting location, otherwise the current working directory is used.

Example:

```php

use Anteris\FileExplorer\FileExplorer;

$fileExplorer = new FileExplorer; // This uses the cwd
$fileExplorer = new FileExplorer('/users/foo'); // This uses /users/foo

```

# Interacting with the File System

There are several methods that will help you to start interacting with the file system. These are listed below.

## createDirectory( string $directory )
This method creates a new directory. If a relative path, this directory will be created relative to the current pointer. If absolute, it will be created at that location.

Example:

```php

$fileExplorer->createDirectory('./myFolder'); // Creates the folder here
$fileExplorer->createDirectory('/users/foo/myFolder'); // Creates the folder in /users/foo

```

## createFile( string $filename, $contents, bool $overwrite = false )
This method creates a new file. If a relative path, this file will be created relative to the current pointer. If absolute, it will be created at that location. Unless $overwrite is passed as true, the file will not be overwritten.

Example:

```php

$fileExplorer->createFile('test.txt', 'Hello world!'); // Will not overwrite test.txt
$fileExplorer->createFile('test.txt', 'Hello world!', true); // Will overwrite test.txt

```

## enterDirectory( string $directory )
Sets the current context of the class to this directory (think about entering a sub-folder within your file browser). If a relative path is passed, this is relative to the current directory context.

Example:

```php

$fileExplorer->enterDirectory('mySubFolder'); // Relative directory
$fileExplorer->enterDirectory('/users/foo'); // Absolute path

```

## exists( string $pointer )
Returns true if the requested resource exists, otherwise false. This could be a file or directory. If the path passed is relative, this will be relative to the current directory context.

Example:

```php

$fileExplorer->exists('myFolder');

```

## getCurrentDirectory()
Returns the current directory context of the class. This is where relative paths are resolved.

## getDirectoryContents()
Returns a collection of files and directories in the current directory context.

Example:

```php

use Anteris\FileExplorer\FileObject\Directory;
use Anteris\FileExplorer\FileObject\File;

$items = $fileExplorer->getDirectoryContents();

foreach ($items as $item) {
    if ($item instanceof Directory) {
        echo 'Directory!' . PHP_EOL;
    }

    if ($item instanceof File) {
        echo 'File!' . PHP_EOL;
    }

    echo $file->name . PHP_EOL;
}

```

## goUp()
Sets the directory context to the parent folder.

Example:

```php

$fileExplorer = new FileExplorer('/users/foo');
$fileExplorer->goUp();

echo $fileExplorer->getCurrentDirectory(); // returns /users/

```

## isAbsolutePath(string $path)
Returns true if the path is absolute, otherwise false.

## joinPaths(...$paths)
Joins multiple directory paths together. The end is suffixed with a forward slash, so this should not be used with filenames.

Example:

```php

/**
 * Returns /users/foo/desktop/
 */
$path = $fileExplorer->joinPaths('/users', '/foo', 'Desktop');

```
