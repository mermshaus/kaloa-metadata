<?php

/*
 * This file is part of the kaloa/metadata package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Metadata;

use Kaloa\Metadata\TripleTag;
use Kaloa\Metadata\TripleTagSet;
use PHPUnit_Framework_TestCase;

/**
 *
 */
class TripleTagSetTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @param TripleTag $tag
     * @return string
     */
    private function tagToString(TripleTag $tag)
    {
        $ret = '';

        if ('' === $tag->getValue()) {
            return $ret;
        } else {
            $ret = $tag->getValue();
        }

        if ('' === $tag->getPredicate()) {
            return $ret;
        } else {
            $ret = $tag->getPredicate() . '=' . $ret;
        }

        if ('' === $tag->getNamespace()) {
            return $ret;
        } else {
            $ret = $tag->getNamespace() . ':' . $ret;
        }

        return $ret;
    }

    /**
     *
     */
    public function testIntegrity()
    {
        $tag = new TripleTag('en', 'language', 'dc');
        $tag2 = new TripleTag('de', 'language', 'dc');

        $set = new TripleTagSet();

        $set->add($tag);
        $set->add($tag2);

        $this->assertEquals(2, count($set));
    }

    /**
     *
     */
    public function testFullExample()
    {
        $setA = TripleTagSet::convertStringToTagSet(
            "dc:language=en\n"
            . "foo=bar\n"
            . "baz"
        );

        $setB = TripleTagSet::convertStringToTagSet(
            "dc:language=de\n"
            . "foo=bar\n"
            . "qux"
        );


        $trans = TripleTagSet::calculateTransformations($setA, $setB);

        $tags = array('foo=bar');
        foreach ($trans['keep'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }

        $tags = array('dc:language=de', 'qux');
        foreach ($trans['add'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }

        $tags = array('dc:language=en', 'baz');
        foreach ($trans['delete'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }


        $trans = TripleTagSet::calculateTransformations($setB, $setA);

        $tags = array('foo=bar');
        foreach ($trans['keep'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }

        $tags = array('dc:language=en', 'baz');
        foreach ($trans['add'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }

        $tags = array('dc:language=de', 'qux');
        foreach ($trans['delete'] as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }
    }

    /**
     *
     */
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

    /**
     *
     */
    public function testCalculateTransformationsBothEmpty()
    {
        $expected = array(
            'keep'   => array(),
            'add'    => array(),
            'delete' => array()
        );

        $trans = TripleTagSet::calculateTransformations(null, null);
        $this->assertEquals($expected, $trans);

        $trans = TripleTagSet::calculateTransformations(new TripleTagSet(), new TripleTagSet());
        $this->assertEquals($expected, $trans);

        $trans = TripleTagSet::calculateTransformations(new TripleTagSet(), null);
        $this->assertEquals($expected, $trans);

        $trans = TripleTagSet::calculateTransformations(null, new TripleTagSet());
        $this->assertEquals($expected, $trans);
    }

    /**
     *
     */
    public function testCalculateTransformationsOneEmpty()
    {
        $setWithOne = new TripleTagSet;
        $setWithOne->add(new TripleTag('en', 'language', 'dc'));

        $trans = TripleTagSet::calculateTransformations($setWithOne, null);
        $this->assertEquals(0, count($trans['keep']));
        $this->assertEquals(0, count($trans['add']));
        $this->assertEquals(1, count($trans['delete']));
        $this->assertEquals('dc', $trans['delete'][0]->getNamespace());

        $trans = TripleTagSet::calculateTransformations(null, $setWithOne);
        $this->assertEquals(0, count($trans['keep']));
        $this->assertEquals(1, count($trans['add']));
        $this->assertEquals(0, count($trans['delete']));
        $this->assertEquals('dc', $trans['add'][0]->getNamespace());
    }

    /**
     *
     */
    public function testCalculateTransformationsSame()
    {
        $setA = new TripleTagSet;
        $setA->add(new TripleTag('en', 'language', 'dc'));

        $setB = new TripleTagSet;
        $setB->add(new TripleTag('en', 'language', 'dc'));

        $trans = TripleTagSet::calculateTransformations($setA, $setB);
        $this->assertEquals(1, count($trans['keep']));
        $this->assertEquals(0, count($trans['add']));
        $this->assertEquals(0, count($trans['delete']));
        $this->assertEquals('dc', $trans['keep'][0]->getNamespace());

        $trans = TripleTagSet::calculateTransformations($setB, $setA);
        $this->assertEquals(1, count($trans['keep']));
        $this->assertEquals(0, count($trans['add']));
        $this->assertEquals(0, count($trans['delete']));
        $this->assertEquals('dc', $trans['keep'][0]->getNamespace());
    }

    /**
     *
     */
    public function testConvertArrayToTagSet()
    {
        $tags = array(
            array('namespace' => 'dc', 'predicate' => 'language', 'value' => 'en'),
            array('namespace' => ''  , 'predicate' => 'foo'     , 'value' => 'bar'),
            array('namespace' => ''  , 'predicate' => ''        , 'value' => 'qux'),
        );

        $set = TripleTagSet::convertArrayToTagSet($tags);

        $tags = array('dc:language=en', 'foo=bar', 'qux');
        foreach ($set as $tag) {
            $this->assertEquals(true, in_array($this->tagToString($tag), $tags, true));
        }
    }
}
