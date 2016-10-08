<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Adapter;

use Symfony\Component\Cache\Adapter\AbstractAdapter;

class MaxIdLengthAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testLongKey()
    {
        $cache = $this->getMockBuilder(MaxIdLengthAdapter::class)
            ->setConstructorArgs(array(str_repeat('-', 10)))
            ->setMethods(array('doHave', 'doFetch', 'doDelete', 'doSave', 'doClear'))
            ->getMock();

        $cache->expects($this->exactly(2))
            ->method('doHave')
            ->withConsecutive(
                array($this->equalTo('----------:nWfzGiCgLczv3SSUzXL3kg:')),
                array($this->equalTo('----------:---------------------------------------'))
            );

        $cache->hasItem(str_repeat('-', 40));
        $cache->hasItem(str_repeat('-', 39));
    }

    /**
     * @expectedException \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage Namespace must be 26 chars max, 40 given ("----------------------------------------")
     */
    public function testTooLongNamespace()
    {
        $cache = $this->getMockBuilder(MaxIdLengthAdapter::class)
            ->setConstructorArgs(array(str_repeat('-', 40)))
            ->getMock();
    }
}

abstract class MaxIdLengthAdapter extends AbstractAdapter
{
    protected $maxIdLength = 50;

    public function __construct($ns)
    {
        parent::__construct($ns);
    }
}