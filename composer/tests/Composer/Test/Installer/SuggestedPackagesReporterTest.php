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

namespace Composer\Test\Installer;

use Composer\Installer\SuggestedPackagesReporter;
use Composer\Test\TestCase;

/**
 * @coversDefaultClass Composer\Installer\SuggestedPackagesReporter
 */
class SuggestedPackagesReporterTest extends TestCase
{
    private $io;
    private $suggestedPackagesReporter;

    protected function setUp()
    {
        $this->io = $this->getMockBuilder('Composer\IO\IOInterface')->getMock();

        $this->suggestedPackagesReporter = new SuggestedPackagesReporter($this->io);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $this->io->expects($this->once())
            ->method('write');

        $this->suggestedPackagesReporter->addPackage('a', 'b', 'c');
        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_LIST);
    }

    /**
     * @covers ::getPackages
     */
    public function testGetPackagesEmptyByDefault()
    {
        $this->assertEmpty($this->suggestedPackagesReporter->getPackages());
    }

    /**
     * @covers ::getPackages
     * @covers ::addPackage
     */
    public function testGetPackages()
    {
        $suggestedPackage = $this->getSuggestedPackageArray();
        $this->suggestedPackagesReporter->addPackage(
            $suggestedPackage['source'],
            $suggestedPackage['target'],
            $suggestedPackage['reason']
        );
        $this->assertSame(
            array($suggestedPackage),
            $this->suggestedPackagesReporter->getPackages()
        );
    }

    /**
     * Test addPackage appends packages.
     * Also test targets can be duplicated.
     *
     * @covers ::addPackage
     */
    public function testAddPackageAppends()
    {
        $suggestedPackageA = $this->getSuggestedPackageArray();
        $suggestedPackageB = $this->getSuggestedPackageArray();
        $suggestedPackageB['source'] = 'different source';
        $suggestedPackageB['reason'] = 'different reason';
        $this->suggestedPackagesReporter->addPackage(
            $suggestedPackageA['source'],
            $suggestedPackageA['target'],
            $suggestedPackageA['reason']
        );
        $this->suggestedPackagesReporter->addPackage(
            $suggestedPackageB['source'],
            $suggestedPackageB['target'],
            $suggestedPackageB['reason']
        );
        $this->assertSame(
            array($suggestedPackageA, $suggestedPackageB),
            $this->suggestedPackagesReporter->getPackages()
        );
    }

    /**
     * @covers ::addSuggestionsFromPackage
     */
    public function testAddSuggestionsFromPackage()
    {
        $package = $this->createPackageMock();
        $package->expects($this->once())
            ->method('getSuggests')
            ->will($this->returnValue(array(
                'target-a' => 'reason-a',
                'target-b' => 'reason-b',
            )));
        $package->expects($this->once())
            ->method('getPrettyName')
            ->will($this->returnValue('package-pretty-name'));

        $this->suggestedPackagesReporter->addSuggestionsFromPackage($package);
        $this->assertSame(array(
            array(
                'source' => 'package-pretty-name',
                'target' => 'target-a',
                'reason' => 'reason-a',
            ),
            array(
                'source' => 'package-pretty-name',
                'target' => 'target-b',
                'reason' => 'reason-b',
            ),
        ), $this->suggestedPackagesReporter->getPackages());
    }

    /**
     * @covers ::output
     */
    public function testOutput()
    {
        $this->suggestedPackagesReporter->addPackage('a', 'b', 'c');

        $this->io->expects($this->at(0))
            ->method('write')
            ->with('<comment>a</comment> suggests:');

        $this->io->expects($this->at(1))
            ->method('write')
            ->with(' - <info>b</info>: c');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE);
    }

    /**
     * @covers ::output
     */
    public function testOutputWithNoSuggestionReason()
    {
        $this->suggestedPackagesReporter->addPackage('a', 'b', '');

        $this->io->expects($this->at(0))
            ->method('write')
            ->with('<comment>a</comment> suggests:');

        $this->io->expects($this->at(1))
            ->method('write')
            ->with(' - <info>b</info>');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE);
    }

    /**
     * @covers ::output
     */
    public function testOutputIgnoresFormatting()
    {
        $this->suggestedPackagesReporter->addPackage('source', 'target1', "\x1b[1;37;42m Like us\r\non Facebook \x1b[0m");
        $this->suggestedPackagesReporter->addPackage('source', 'target2', "<bg=green>Like us on Facebook</>");

        $this->io->expects($this->at(0))
            ->method('write')
            ->with('<comment>source</comment> suggests:');

        $this->io->expects($this->at(1))
            ->method('write')
            ->with(' - <info>target1</info>: [1;37;42m Like us on Facebook [0m');

        $this->io->expects($this->at(2))
            ->method('write')
            ->with(' - <info>target2</info>: \\<bg=green>Like us on Facebook\\</>');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE);
    }

    /**
     * @covers ::output
     */
    public function testOutputMultiplePackages()
    {
        $this->suggestedPackagesReporter->addPackage('a', 'b', 'c');
        $this->suggestedPackagesReporter->addPackage('source package', 'target', 'because reasons');

        $this->io->expects($this->at(0))
            ->method('write')
            ->with('<comment>a</comment> suggests:');

        $this->io->expects($this->at(1))
            ->method('write')
            ->with(' - <info>b</info>: c');

        $this->io->expects($this->at(2))
            ->method('write')
            ->with('');

        $this->io->expects($this->at(3))
            ->method('write')
            ->with('<comment>source package</comment> suggests:');

        $this->io->expects($this->at(4))
            ->method('write')
            ->with(' - <info>target</info>: because reasons');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE);
    }

    /**
     * @covers ::output
     */
    public function testOutputSkipInstalledPackages()
    {
        $repository = $this->getMockBuilder('Composer\Repository\InstalledRepository')->disableOriginalConstructor()->getMock();
        $package1 = $this->getMockBuilder('Composer\Package\PackageInterface')->getMock();
        $package2 = $this->getMockBuilder('Composer\Package\PackageInterface')->getMock();

        $package1->expects($this->once())
            ->method('getNames')
            ->will($this->returnValue(array('x', 'y')));

        $package2->expects($this->once())
            ->method('getNames')
            ->will($this->returnValue(array('b')));

        $repository->expects($this->once())
            ->method('getPackages')
            ->will($this->returnValue(array(
                $package1,
                $package2,
            )));

        $this->suggestedPackagesReporter->addPackage('a', 'b', 'c');
        $this->suggestedPackagesReporter->addPackage('source package', 'target', 'because reasons');

        $this->io->expects($this->at(0))
            ->method('write')
            ->with('<comment>source package</comment> suggests:');

        $this->io->expects($this->at(1))
            ->method('write')
            ->with(' - <info>target</info>: because reasons');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE, $repository);
    }

    /**
     * @covers ::output
     */
    public function testOutputNotGettingInstalledPackagesWhenNoSuggestions()
    {
        $repository = $this->getMockBuilder('Composer\Repository\InstalledRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->exactly(0))
            ->method('getPackages');

        $this->suggestedPackagesReporter->output(SuggestedPackagesReporter::MODE_BY_PACKAGE, $repository);
    }

    private function getSuggestedPackageArray()
    {
        return array(
            'source' => 'a',
            'target' => 'b',
            'reason' => 'c',
        );
    }

    private function createPackageMock()
    {
        return $this->getMockBuilder('Composer\Package\Package')
            ->setConstructorArgs(array(md5(mt_rand()), '1.0.0.0', '1.0.0'))
            ->getMock();
    }
}
