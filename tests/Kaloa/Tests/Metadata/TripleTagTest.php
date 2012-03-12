<?php

namespace Kaloa\Tests\Metadata;

use PHPUnit_Framework_TestCase;
use Kaloa\Metadata\TripleTag;

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
