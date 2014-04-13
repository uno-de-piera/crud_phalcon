<?php


class Posts extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $title;
     
    /**
     *
     * @var string
     */
    public $content;
     
    /**
     *
     * @var string
     */
    public $created_at;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap() {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'content' => 'content', 
            'created_at' => 'created_at'
        );
    }

    public function initialize()
    {
        //queremos que este campo se genere sólo, lo evitamos al hacer inserts/updates
        $this->skipAttributes(array('created_at'));
    }

}