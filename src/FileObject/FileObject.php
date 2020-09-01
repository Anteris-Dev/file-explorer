<?php

namespace Anteris\FileExplorer\FileObject;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Represents a file object.
 */
class FileObject extends DataTransferObject
{
    public string $name;
    public string $path;
}
