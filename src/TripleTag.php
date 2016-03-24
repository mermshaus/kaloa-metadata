<?php

/*
 * This file is part of the kaloa/metadata package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Metadata;

/**
 *
 */
final class TripleTag
{
    private $namespace = '';
    private $predicate = '';
    private $value = '';

    /**
     *
     * @param string $value
     * @param string $predicate
     * @param string $namespace
     */
    public function __construct($value = '', $predicate = '', $namespace = '')
    {
        $this->setValue($value);
        $this->setPredicate($predicate);
        $this->setNamespace($namespace);
    }

    public function setNamespace($newNamespace)
    {
        $this->namespace = trim($newNamespace);
    }

    public function setPredicate($newPredicate)
    {
        $this->predicate = trim($newPredicate);
    }

    public function setValue($newValue)
    {
        $this->value = trim($newValue);
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getPredicate()
    {
        return $this->predicate;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function equals(TripleTag $tripleTag)
    {
        return (
            $this->namespace === $tripleTag->getNamespace()
            && $this->predicate === $tripleTag->getPredicate()
            && $this->value === $tripleTag->getValue()
        );
    }
}
