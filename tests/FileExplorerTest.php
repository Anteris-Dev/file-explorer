<?php

namespace Tests;

use Anteris\FileExplorer\FileExplorer;
use Anteris\FileExplorer\FileObject\Directory;
use Anteris\FileExplorer\FileObject\File;
use Anteris\FileExplorer\FileObject\FileObjectCollection;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileExplorerTest extends TestCase
{
    protected const TEST_FILE           = 'test.txt';
    protected const TEST_DIRECTORY      = 'testdir';
    protected const TEST_FILE_PATH      = __DIR__ . '/' . self::TEST_FILE;
    protected const TEST_DIRECTORY_PATH = __DIR__ . '/' . self::TEST_DIRECTORY . '/';

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
        $this->expectExceptionMessage(self::TEST_DIRECTORY_PATH . ' does not exist!');
        new FileExplorer(self::TEST_DIRECTORY_PATH);
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_create_a_directory()
    {
        $this->assertDirectoryDoesNotExist(self::TEST_DIRECTORY_PATH);
        $this->filesystem->createDirectory(self::TEST_DIRECTORY);
        $this->assertDirectoryExists(self::TEST_DIRECTORY_PATH);
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_create_and_enter_a_directory()
    {
        $this->assertDirectoryDoesNotExist(self::TEST_DIRECTORY_PATH);
        $this->assertNotEquals(self::TEST_DIRECTORY_PATH, $this->filesystem->getCurrentDirectory());

        // Now create and enter the directory
        $this->filesystem->createAndEnterDirectory(self::TEST_DIRECTORY);

        $this->assertDirectoryExists(self::TEST_DIRECTORY_PATH);
        $this->assertEquals(self::TEST_DIRECTORY_PATH, $this->filesystem->getCurrentDirectory());
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_create_a_file()
    {
        $this->assertFileDoesNotExist(self::TEST_DIRECTORY_PATH);
        $this->filesystem->createFile(self::TEST_FILE, 'hello world!');
        $this->assertFileExists(self::TEST_FILE_PATH);
        $this->assertEquals('hello world!', file_get_contents(self::TEST_FILE_PATH));
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_cannot_create_existing_file()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(self::TEST_FILE_PATH . ' already exists: please use overwrite or choose another filename!');
        file_put_contents(self::TEST_FILE_PATH, 'hello world!');
        $this->filesystem->createFile(self::TEST_FILE, 'hi there!');
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_cannot_enter_a_directory_that_does_not_exist()
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage(self::TEST_DIRECTORY_PATH . ' does not exist!');

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

        mkdir(self::TEST_DIRECTORY_PATH);
        $this->filesystem->enterDirectory(self::TEST_DIRECTORY);

        $this->assertEquals(
            $this->filesystem->getCurrentDirectory(),
            self::TEST_DIRECTORY_PATH
        );
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_see_if_directory_exists()
    {
        mkdir(self::TEST_DIRECTORY_PATH);
        $this->assertEquals(true, $this->filesystem->exists(self::TEST_DIRECTORY));
    }

    /**
     * @covers \Anteris\FileExplorer\FileExplorer
     */
    public function test_it_can_see_if_directory_does_not_exist()
    {
        $this->assertEquals(false, $this->filesystem->exists(self::TEST_DIRECTORY));
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
        if (is_dir(self::TEST_DIRECTORY_PATH)) {
            rmdir(self::TEST_DIRECTORY_PATH);
        }

        if (file_exists(self::TEST_FILE_PATH)) {
            unlink(self::TEST_FILE_PATH);
        }
    }
}
