<?php

namespace Kaloa\Metadata;

/**
 *
 */
class TripleTag
{
    protected $_namespace = '';
    protected $_predicate = '';
    protected $_value = '';

    public function __construct($value = null, $predicate = null, $namespace = null)
    {
        if (null !== $value) {
            $this->setValue($value);
        }
        if (null !== $predicate) {
            $this->setPredicate($predicate);
        }
        if (null !== $namespace) {
            $this->setNamespace($namespace);
        }
    }

    public function setNamespace($newNamespace)
    {
        $this->_namespace = trim($newNamespace);
    }

    public function setPredicate($newPredicate)
    {
        $this->_predicate = trim($newPredicate);
    }

    public function setValue($newValue)
    {
        $this->_value = trim($newValue);
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function getPredicate()
    {
        return $this->_predicate;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function equals(TripleTag $tripleTag)
    {
        return ($this->_namespace == $tripleTag->getNamespace()
                && $this->_predicate == $tripleTag->getPredicate()
                && $this->_value == $tripleTag->getValue());
    }
}
