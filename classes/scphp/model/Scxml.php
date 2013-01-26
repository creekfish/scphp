<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Scxml extends CompoundNode
{
    /**
     * The SCXML namespace
     */
    const SCXML = 'http://www.w3.org/2005/07/scxml';

    /**
     * The SCXML version of this document.
     * @var string
     */
    private $version;

    /**
     * Set the document version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the document version.
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return FALSE;
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof TransitionTarget;
    }
}
