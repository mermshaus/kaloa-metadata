<?php

/*
 * This file is part of the kaloa/metadata package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Metadata;

use Kaloa\Metadata\TripleTag;
use PHPUnit_Framework_TestCase;

class TripleTagTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $tag = new TripleTag('en', 'language', 'dc');

        $this->assertEquals('dc:language=en', $tag->getNamespace() . ':'
                . $tag->getPredicate() . '=' . $tag->getValue());

        $tag2 = new TripleTag('en', 'language', 'dc');

        $this->assertEquals(true, $tag->equals($tag2));

        $tag2->setValue('de');

        $this->assertEquals(false, $tag->equals($tag2));
    }
}
