<?php

namespace Anteris\FileExplorer\FileObject;

use Anteris\FileExplorer\FileObject\FileObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * Represents a collection of file objects.
 */
class FileObjectCollection extends DataTransferObjectCollection
{
    /**
     * @inheritdoc
     */
    public function current(): FileObject
    {
        return parent::current();
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset): FileObject
    {
        return parent::offsetGet($offset);
    }
}
