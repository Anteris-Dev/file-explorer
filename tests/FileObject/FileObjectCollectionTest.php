<?php

namespace Tests\FileObject;

use Anteris\FileExplorer\FileObject\FileObject;
use Anteris\FileExplorer\FileObject\FileObjectCollection;
use PHPUnit\Framework\TestCase;

class FileObjectCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->collection = new FileObjectCollection([
            new FileObject([
                'name' => 'test',
                'path' => __DIR__
            ])
        ]);
    }

    /**
     * @covers \Anteris\FileExplorer\FileObject\FileObjectCollection
     */
    public function test_current_returns_file_object()
    {
        $this->assertInstanceOf(FileObject::class, $this->collection->current());
    }

    /**
     * @covers \Anteris\FileExplorer\FileObject\FileObjectCollection
     */
    public function test_offset_get_returns_file_object()
    {
        $this->assertInstanceOf(FileObject::class, $this->collection->offsetGet(0));
    }
}
