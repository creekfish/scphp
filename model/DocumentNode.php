<?php

namespace org\funmonkeys\scphp\model;

/**
 * Base class for all SCXML document nodes.
 *
 * @author bherring
 */
abstract class DocumentNode
{
    /**
     * Document order number.
     * @var int
     */
    private $document_order;

    /**
     * Id attribute for this node.
     * @var string
     */
    private $id;

    public function __construct()
    {
        $this->document_order = NULL;
        $this->id = NULL;
    }

    /**
     * Return the document order number for this node.
     * This value must be the absolute document ordering,
     * a unique number for every node in the document.
     *
     * @return int - the document order number
     */
    public function getDocumentOrder() {
        return $this->document_order;
    }

    /**
     * Return the identifier attribute (id) for this node.
     *
     * @return string - the id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Return the document order number for this node.
     * This value must be the absolute document ordering,
     * a unique number for every node in the document.
     *
     * @param int $order_number - the document order number
     * @return void
     */
    public function setDocumentOrder($order_number) {
        $this->document_order = $order_number;
    }

    /**
     * Set the identifier attribute (id) for this node.
     *
     * @param string $id - the id to set
     * @return void
     * @throws SCXMLModelException
     */
    public function setId($id) {
        if (!is_string($id)) {
            throw new SCXMLModelException('Id must be string.');
        }
        $this->id = $id;
    }
}
