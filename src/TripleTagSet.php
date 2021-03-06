<?php

/*
 * This file is part of the kaloa/metadata package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Metadata;

use Kaloa\Metadata\TripleTag;
use Kaloa\Util\AbstractSet;

/**
 *
 */
final class TripleTagSet extends AbstractSet
{
    protected $_managedClass = 'Kaloa\\Metadata\\TripleTag';

    /**
     *
     * @param TripleTagSet
     * @param TripleTagSet
     * @return array
     */
    public static function calculateTransformations(
        TripleTagSet $oldSet = null,
        TripleTagSet $newSet = null
    ) {
        $transformations = array(
            'keep'   => array(),
            'add'    => array(),
            'delete' => array()
        );

        if (null === $oldSet && null === $newSet) {
            return $transformations;
        }

        if (null === $oldSet) {
            foreach ($newSet as $newTag) {
                $transformations['add'][] = $newTag;
            }

            return $transformations;
        }

        if (null === $newSet) {
            foreach ($oldSet as $oldTag) {
                $transformations['delete'][] = $oldTag;
            }

            return $transformations;
        }

        foreach ($newSet as $newTag) {
            foreach ($oldSet as $oldTag) {
                if ($newTag->equals($oldTag)) {
                    // Tags in ($newSet AND $oldSet)
                    $transformations['keep'][] = $newTag;
                    continue 2;
                }
            }

            // Tags in ($newSet AND NOT $oldSet)
            $transformations['add'][] = $newTag;
        }

        foreach ($oldSet as $oldTag) {
            foreach ($newSet as $newTag) {
                if ($oldTag->equals($newTag)) {
                    continue 2;
                }
            }

            // Tags in ($oldSet AND NOT $newSet)
            $transformations['delete'][] = $oldTag;
        }

        return $transformations;
    }

    /**
     * Transforms a string of linebreak-separated triple tags into a tag array
     * with namespace/predicate/value entries
     *
     * @param  string $s string of triple tags
     * @return TripleTagSet array of TripleTag
     */
    public static function convertStringToTagSet($s)
    {
        $tags = new TripleTagSet();

        $s = str_replace(array("\r\n", "\r"), array("\n", "\n"), $s);

        $tmp = explode("\n", $s);
        foreach ($tmp as $t) {
            $newTag = new TripleTag();

            $t = trim($t);
            if ('' !== $t) {
                if (false !== strpos($t, ':')) {
                    $parts = explode(':', $t, 2);
                    $newTag->setNamespace($parts[0]);
                    $t = $parts[1];
                }

                if (false !== strpos($t, '=')) {
                    $parts = explode('=', $t, 2);
                    $newTag->setPredicate($parts[0]);
                    $t = $parts[1];
                }

                $newTag->setValue($t);

                $tags->add($newTag);
            }
        }

        return $tags;
    }

    /**
     *
     * @param array $tags
     * @return TripleTagSet
     */
    public static function convertArrayToTagSet(array $tags)
    {
        $tagSet = new TripleTagSet();

        foreach ($tags as $tag) {
            $newTag = new TripleTag(
                $tag['value'],
                $tag['predicate'],
                $tag['namespace']
            );

            $tagSet->add($newTag);
        }

        return $tagSet;
    }
}
