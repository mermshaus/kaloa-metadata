<?php

namespace Kaloa\Tests\Metadata;

use PHPUnit_Framework_TestCase;
use Kaloa\Metadata\TripleTag;
use Kaloa\Metadata\TripleTagSet;

class TripleTagSetTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $tag = new TripleTag('en', 'language', 'dc');
        $tag2 = new TripleTag('de', 'language', 'dc');

        $set = new TripleTagSet();

        $set->add($tag);
        $set->add($tag2);

        $this->assertEquals(2, count($set));
    }

    public function testCalculateTransformations()
    {
        $tag = new TripleTag('en', 'language', 'dc');
        $tag2 = new TripleTag('de', 'language', 'dc');

        $set = new TripleTagSet();

        $set->add($tag);
        $set->add($tag2);

        $trans = TripleTagSet::calculateTransformations($set, new TripleTagSet());

        $this->assertEquals(0, count($trans['keep']));
        $this->assertEquals(0, count($trans['add']));
        $this->assertEquals(2, count($trans['delete']));
        $this->assertEquals(true, (in_array($tag, $trans['delete']) && in_array($tag2, $trans['delete'])));

        $trans = TripleTagSet::calculateTransformations(new TripleTagSet(), $set);

        $this->assertEquals(0, count($trans['keep']));
        $this->assertEquals(2, count($trans['add']));
        $this->assertEquals(true, (in_array($tag, $trans['add']) && in_array($tag2, $trans['add'])));
        $this->assertEquals(0, count($trans['delete']));

        $set = new TripleTagSet();
        $set2 = new TripleTagSet();

        $set->add($tag);
        $set2->add($tag2);

        $trans = TripleTagSet::calculateTransformations($set, $set2);

        $this->assertEquals(0, count($trans['keep']));
        $this->assertEquals(1, count($trans['add']));
        $this->assertEquals(true, (in_array($tag2, $trans['add'])));
        $this->assertEquals(1, count($trans['delete']));
        $this->assertEquals(true, (in_array($tag, $trans['delete'])));
    }
}
