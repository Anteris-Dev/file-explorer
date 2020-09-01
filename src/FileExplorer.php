<?php

namespace Anteris\FileExplorer;

use Anteris\FileExplorer\FileObject\Directory;
use Anteris\FileExplorer\FileObject\File;
use Anteris\FileExplorer\FileObject\FileObjectCollection;
use DirectoryIterator;
use Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Bringing a little class to your filesystem operations.
 */
class FileExplorer
{
    /** @var Filesystem A Symfony helper so we are not re-inventing the wheel. */
    protected Filesystem $filesystem;

    /** @var string A placeholder for where we currently are on the filesystem. */
    public string $pointer;

    /**
     * Sets up this class to begin browsing.
     * 
     * @param  string  $startDirectory  The directory to begin in. If not passed, defaults to the current working directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(string $startDirectory = null)
    {
        $this->filesystem   = new Filesystem;
        $directory          = $this->cleanupDirectorySlashes(
            $startDirectory ?? getcwd()
        );

        if (! $this->exists($directory)) {
            throw new FileNotFoundException("$directory does not exist!");
        }

        $this->pointer = $directory;
    }

    /**
     * Creates the specified directory (recursively).
     * 
     * @param  string  $directory  The directory to be creates.
     * @see    Filesystem::mkdir()
     */
    public function createDirectory(string $directory)
    {
        if (!$this->isAbsolutePath($directory)) {
            $directory = $this->joinPaths($this->pointer, $directory);
        }

        $this->filesystem->mkdir(
            $this->cleanupDirectorySlashes($directory)
        );
    }

    /**
     * Creates a new file. If relative path, this is relative to pointer, otherwise place in absolute path.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function createFile(string $filename, $contents, bool $overwrite = false)
    {
        if (! $this->isAbsolutePath($filename)) {
            $filename = $this->pointer . $filename;
        }

        if (! $overwrite && $this->exists($filename)) {
            throw new Exception("$filename already exists: please use overwrite or choose another filename!");
        }

        file_put_contents($filename, $contents);
    }

    /**
     * Sets the current context to the directory specified.
     * 
     * @param  string  $directory  The directory to enter.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function enterDirectory(string $directory)
    {
        $directory = $this->cleanupDirectorySlashes($directory);

        if (! $this->isAbsolutePath($directory)) {
            $directory = $this->joinPaths($this->pointer, $directory);
        }

        if (! $this->exists($directory)) {
            throw new FileNotFoundException("$directory does not exist!");
        }

        $this->pointer = $directory;
    }

    /**
     * Determines whether or not the requested resource exists.
     * 
     * @param  string  $pointer  The resource to check for.
     * @see    Filesystem::exists()
     */
    public function exists(string $pointer): bool
    {
        if (!$this->isAbsolutePath($pointer)) {
            $pointer = $this->joinPaths($this->pointer, $pointer);
        }

        return $this->filesystem->exists($pointer);
    }

    /**
     * Returns our current location on the filesystem.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getCurrentDirectory()
    {
        return $this->pointer;
    }

    /**
     * Returns an alphabetical array of files and folders in the current directory.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function getDirectoryContents(): FileObjectCollection
    {
        $items = [];

        foreach (new DirectoryIterator($this->pointer) as $item) {
            if ($item->isDot()) {
                continue;
            }

            if ($item->isDir()) {
                $items[] = new Directory([
                    'name' => $item->getFilename(),
                    'path' => $item->getPath()
                ]);
            }

            if ($item->isFile()) {
                $items[] = new File([
                    'name' => $item->getFilename(),
                    'path' => $item->getPath(),
                ]);
            }
        }

        return new FileObjectCollection(
            $this->sortFileObjects($items)
        );
    }

    /**
     * Moves to the parent folder.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function goUp()
    {
        $directory = substr(
            $this->pointer,   
            0,
            strrpos(substr($this->pointer, 0, -1), '/')  // Get rid of the last slash
        );

        if (! $directory) {
            throw new FileNotFoundException("Cannot go up beyond the root directory!");
        }

        $this->pointer = $this->cleanupDirectorySlashes($directory);
    }

    /**
     * Determines whether the path is absolute or relative.
     * 
     * @param  string  $path  The path to evaluate.
     * @see    Filesystem::isAbsolutePath()
     */
    public function isAbsolutePath(string $path): bool
    {
        return $this->filesystem->isAbsolutePath($path);
    }

    /**
     * Joins multiple paths together.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function joinPaths(...$paths): string
    {
        $joinedPath = '';
        $firstPath = true;

        foreach ($paths as $path) {
            if ($firstPath) {
                $joinedPath = rtrim($path, '\\/') . '/';
                $firstPath = false;
                continue;
            }

            $joinedPath .= trim($path, '\\/') . '/';
        }

        return $joinedPath;
    }

    /**
     * Removes trailing slashes and replaces it with a forward slash.
     * 
     * @param  string  $directory  The directory to perform this action on.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function cleanupDirectorySlashes(string $directory): string
    {
        return rtrim(str_replace('\\', '/', $directory), '/') . '/';
    }

    /**
     * Sorts file objects alphabetically.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    protected function sortFileObjects(array $collection): array
    {
        usort($collection, function ($a, $b) {
            return ($a->name < $b->name) ? -1 : 1;
        });

        return $collection;
    }
}
