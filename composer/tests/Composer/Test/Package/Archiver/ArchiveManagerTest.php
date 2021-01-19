<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test\Package\Archiver;

use Composer\IO\NullIO;
use Composer\Factory;
use Composer\Package\Archiver\ArchiveManager;
use Composer\Package\PackageInterface;
use Composer\Util\Loop;
use Composer\Test\Mock\FactoryMock;
use Composer\Util\ProcessExecutor;

class ArchiveManagerTest extends ArchiverTest
{
    /**
     * @var ArchiveManager
     */
    protected $manager;

    protected $targetDir;

    public function setUp()
    {
        parent::setUp();

        $factory = new Factory();
        $dm = $factory->createDownloadManager(
            $io = new NullIO,
            $config = FactoryMock::createConfig(),
            $httpDownloader = $factory->createHttpDownloader($io, $config),
            new ProcessExecutor($io)
        );
        $loop = new Loop($httpDownloader);
        $this->manager = $factory->createArchiveManager($factory->createConfig(), $dm, $loop);
        $this->targetDir = $this->testDir.'/composer_archiver_tests';
    }

    public function testUnknownFormat()
    {
        $this->setExpectedException('RuntimeException');

        $package = $this->setupPackage();

        $this->manager->archive($package, '__unknown_format__', $this->targetDir);
    }

    public function testArchiveTar()
    {
        $this->skipIfNotExecutable('git');

        $this->setupGitRepo();

        $package = $this->setupPackage();

        $this->manager->archive($package, 'tar', $this->targetDir);

        $target = $this->getTargetName($package, 'tar');
        $this->assertFileExists($target);

        $tmppath = sys_get_temp_dir().'/composer_archiver/'.$this->manager->getPackageFilename($package);
        $this->assertFileDoesNotExist($tmppath);

        unlink($target);
    }

    public function testArchiveCustomFileName()
    {
        $this->skipIfNotExecutable('git');

        $this->setupGitRepo();

        $package = $this->setupPackage();

        $fileName = 'testArchiveName';

        $this->manager->archive($package, 'tar', $this->targetDir, $fileName);

        $target = $this->targetDir . '/' . $fileName . '.tar';

        $this->assertFileExists($target);

        $tmppath = sys_get_temp_dir().'/composer_archiver/'.$this->manager->getPackageFilename($package);
        $this->assertFileDoesNotExist($tmppath);

        unlink($target);
    }

    protected function getTargetName(PackageInterface $package, $format, $fileName = null)
    {
        if (null === $fileName) {
            $packageName = $this->manager->getPackageFilename($package);
        } else {
            $packageName = $fileName;
        }

        return $this->targetDir.'/'.$packageName.'.'.$format;
    }

    /**
     * Create local git repository to run tests against!
     */
    protected function setupGitRepo()
    {
        $currentWorkDir = getcwd();
        chdir($this->testDir);

        $output = null;
        $result = $this->process->execute('git init -q', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not init: '.$this->process->getErrorOutput());
        }

        $result = $this->process->execute('git checkout -b master', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not checkout master branch: '.$this->process->getErrorOutput());
        }

        $result = $this->process->execute('git config user.email "you@example.com"', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not config: '.$this->process->getErrorOutput());
        }

        $result = $this->process->execute('git config commit.gpgsign false', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not config: '.$this->process->getErrorOutput());
        }

        $result = $this->process->execute('git config user.name "Your Name"', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not config: '.$this->process->getErrorOutput());
        }

        $result = file_put_contents('composer.json', '{"name":"faker/faker", "description": "description", "license": "MIT"}');
        if (false === $result) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not save file.');
        }

        $result = $this->process->execute('git add composer.json && git commit -m "commit composer.json" -q', $output, $this->testDir);
        if ($result > 0) {
            chdir($currentWorkDir);
            throw new \RuntimeException('Could not commit: '.$this->process->getErrorOutput());
        }

        chdir($currentWorkDir);
    }
}
