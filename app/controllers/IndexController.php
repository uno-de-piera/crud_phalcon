<?php

//otro objecto para crear paginación con QueryBuilder
use \Phalcon\Paginator\Adapter\QueryBuilder as PaginacionBuilder;

class IndexController extends ControllerBase
{

	/**
	* @desc - creamos la paginación de los posts y los pasamos a la vista
	* @return object
	*/
    public function indexAction()
    {

        $posts = $this->modelsManager->createBuilder()
	    ->from('Posts')
	    ->orderBy('id');

		$paginator = new PaginacionBuilder(array(
		    "builder" => $posts,
		    "limit"=> 5,
		    "page" => $this->request->getQuery('page', 'int')
		));
 		
        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();
    }

    /**
	* @desc - permitimos añadir nuevos posts
	* @return json
	*/
    public function addAction()
    {
    	//deshabilitamos la vista para peticiones ajax
        $this->view->disable();
 
        //si es una petición post
        if($this->request->isPost() == true) 
        {
            //si es una petición ajax
            if($this->request->isAjax() == true) 
            {
            	//si existe el token del formulario y es correcto(evita csrf)
            	if($this->security->checkToken()) 
            	{

		        	$post = new Posts();
	                $post->title = $this->request->getPost('title', array('striptags', 'trim'));
	                $post->content = $this->request->getPost('content', array('striptags', 'trim'));
	                //si el post se guarda correctamente
	                if($post->save())
	                {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"success"
				        ));
				        //devolvemos un 200, todo ha ido bien
				        $this->response->setStatusCode(200, "OK");
	                }
	                else
	                {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"error"
				        )); 
				        //devolvemos un 500, error
				        $this->response->setStatusCode(500, "Internal Server Error");
	                }
				    $this->response->send();
	            }
            }
        }
    }

    /**
	* @desc - permitimos editar un post
	* @return json
	*/
    public function editAction()
    {
    	//deshabilitamos la vista para peticiones ajax
        $this->view->disable();
 
        //si es una petición post
        if($this->request->isPost() == true) 
        {
            //si es una petición ajax
            if($this->request->isAjax() == true) 
            {
            	//si existe el token del formulario y es correcto(evita csrf)
            	if($this->security->checkToken()) 
            	{
            		$parameters = array(
			            "id" => $this->request->getPost('id')
			        );

		        	$post = Posts::findFirst(array(
			            "id = :id:",
			            "bind" => $parameters
			        ));

	                $post->title = $this->request->getPost('title', array('striptags', 'trim'));
	                $post->content = $this->request->getPost('content', array('striptags', 'trim'));
	                //si el post se actualiza correctamente
	                if($post->update())
	                {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"success"
				        ));
				        //devolvemos un 200, todo ha ido bien
				        $this->response->setStatusCode(200, "OK");
	                }
	                else
	                {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"error"
				        )); 
				        //devolvemos un 500, error
				        $this->response->setStatusCode(500, "Internal Server Error");
	                }
				    $this->response->send();
	            }
            }
        }
    }

    /**
	* @desc - permitimos eliminar un post
	* @return json
	*/
    public function deleteAction()
    {
    	//deshabilitamos la vista para peticiones ajax
        $this->view->disable();
 
        //si es una petición get
        if($this->request->isGet() == true) 
        {
            //si es una petición ajax
            if($this->request->isAjax() == true) 
            {
	        	$post = Posts::findFirst($this->request->get("id"));
				if($post != false) 
				{
				    if($post->delete() != false) 
				    {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"success"
				        ));
				        //devolvemos un 200, todo ha ido bien
				        $this->response->setStatusCode(200, "OK");
	                }
	                else
	                {
	                	$this->response->setJsonContent(array(
				            "res"		=>		"error"
				        )); 
				        //devolvemos un 500, error
				        $this->response->setStatusCode(500, "Internal Server Error");
	                }
	            }
			    $this->response->send();
            }
        }
    }
}
//Location: app/controllers IndexController.php
