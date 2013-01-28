<?php

namespace scphp\model;

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

    /**
     * The SCPHP model for this node.
     * @var \scphp\Model
     */
    private $model;


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
     * @throws ModelException
     */
    public function setId($id) {
        if (!is_null($id) && !is_string($id)) {
            throw new ModelException('Id must be string.');
        }
        $this->id = $id;
    }

    /**
     * Set the model for this node.
     *
     * @param \scphp\Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for this node.
     *
     * @return \scphp\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    public function __toString()
    {
        return end(explode('\\', get_class($this))).':: id:'.$this->getId().'; doc_order:'.$this->getDocumentOrder();
    }
}
