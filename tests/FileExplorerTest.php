<?php

namespace Tests;

use Anteris\FileExplorer\FileExplorer;
use Anteris\FileExplorer\FileObject\Directory;
use Anteris\FileExplorer\FileObject\File;
use Anteris\FileExplorer\FileObject\FileObjectCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileExplorerTest extends TestCase
{
    /** @var FileExplorer An instance of the file explorer. */
    protected FileExplorer $filesystem;

    /**
     * Setup the file explorer.
     */
    protected function setUp(): void
    {
        $this->filesystem = new FileExplorer(__DIR__);
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_throws_error_when_start_directory_does_not_exist()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(__DIR__ . '/testdir/ does not exist!');
        new FileExplorer(__DIR__ . '/testdir');
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_create_a_directory()
    {
        $this->assertDirectoryDoesNotExist(__DIR__ . '/testdir');
        $this->filesystem->createDirectory('testdir');
        $this->assertDirectoryExists(__DIR__ . '/testdir');
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_cannot_enter_a_directory_that_does_not_exist()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(__DIR__ . '/testdir/ does not exist!');

        $this->filesystem->enterDirectory('testdir');
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_enter_a_directory()
    {
        $this->assertEquals(
            $this->filesystem->getCurrentDirectory(),
            __DIR__ . '/'
        );

        mkdir(__DIR__ . '/testdir');
        $this->filesystem->enterDirectory('testdir');

        $this->assertEquals(
            $this->filesystem->getCurrentDirectory(),
            __DIR__ . '/testdir/'
        );
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_see_if_directory_exists()
    {
        mkdir(__DIR__ . '/testdir');
        $this->assertEquals(true, $this->filesystem->exists('testdir'));
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_see_if_directory_does_not_exist()
    {
        $this->assertEquals(false, $this->filesystem->exists('testdir'));
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_go_up_the_filesystem()
    {
        $this->assertNotEquals(
            dirname(__DIR__, 1) . '/',
            $this->filesystem->getCurrentDirectory()
        );

        $this->filesystem->goUp();

        $this->assertEquals(
            dirname(__DIR__, 1) . '/',
            $this->filesystem->getCurrentDirectory()
        );
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_cannot_go_up_beyond_root()
    {
        $this->filesystem->enterDirectory('/');
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('Cannot go up beyond the root directory!');
        $this->filesystem->goUp();
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_get_directory_contents()
    {
        $this->filesystem->enterDirectory(__DIR__ . '/../src');
        $items = $this->filesystem->getDirectoryContents();

        $this->assertInstanceOf(FileObjectCollection::class, $items);

        $this->assertEquals($items[0]->name, 'FileExplorer.php');
        $this->assertInstanceOf(File::class, $items[0]);

        $this->assertEquals($items[1]->name, 'FileObject');
        $this->assertInstanceOf(Directory::class, $items[1]);
    }

    /**
     * Cleans up the mess we made.
     */
    protected function tearDown(): void
    {
        if (is_dir(__DIR__ . '/testdir')) {
            rmdir(__DIR__ . '/testdir');
        }
    }
}
