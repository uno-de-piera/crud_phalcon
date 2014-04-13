<div class="row-fluid">
	<h1 class="text-center">Crud con Phalcon, Bootstrap y jQuery</h1><hr><br>
	<div class="col-md-8 col-md-offset-2">
		<!--botón que abre una modal para añadir un nuevo post-->
		<a href="#" class="btn btn-success add pull-right" onclick="crudPhalcon.add()">Añadir post</a><br><hr>
		<table class="table table-striped table-bordered table-condensed text-center">
			<thead>
				<tr>
					<th class="text-center">Id</th>
		            <th class="text-center">Título</th>
		            <th class="text-center">Contenido</th>
		            <th class="text-center">Fecha de creación</th>
		            <th class="text-center">Editar</th>
		            <th class="text-center">Eliminar</th>
		        </tr>
			</thead>
			<tbody>
	        <?php 
	        //si hay posts
	        if(count($page->items) > 0)
	        {
	        	//los recorremos y printamos
		        foreach ($page->items as $post) 
		        { 
		        ?>
		        <tr>
		            <td><?php echo $post->id; ?></td>
		            <td><?php echo $post->title; ?></td>
		            <td><?php echo $post->content; ?></td>
		            <td><?php echo $post->created_at; ?></td>
		            <td>
		            	<!--en el evento onclick pasamos el post en formato json-->
			            <a href="#" class="btn btn-info editar" 
			            onclick="crudPhalcon.edit('<?php echo htmlentities(json_encode($post)) ?>')">
			            	Editar
			            </a>
		            </td>
		            <td>
		            	<!--en el evento onclick pasamos el post en formato json-->
			            <a href="#" class="btn btn-danger eliminar" 
			            onclick="crudPhalcon.delete('<?php echo htmlentities(json_encode($post)) ?>')">	
			            	Eliminar
			            </a>
		            </td>
		        </tr>
		        <?php 
		        } 
		        ?>
			    </tbody>
			<?php
			}
			//si no hay posts
			else
			{
			?>
			<tbody>
				<tr>
		            <td colspan="6">
		            	<div class="alert alert-danger alert-dismissable text-center">
		            		Actualmente no hay ningún post
		            	</div>
		            </td>
		        </tr>
		    </tbody>
			<?php
			}
			?>
		</table>
		<?php 
		//comprobamos si hay posts para montar la paginación
	    if(count($page->items) > 0)
	    {
	    ?>
		<center>
		<ul class="pagination pagination-lg pager">
            <li><?php echo $this->tag->linkTo(array("", 'Primera', "class" => "btn")) ?></li>
            <li><?php echo $this->tag->linkTo(array("?page=".$page->before, ' Anterior')) ?></li>
            <li><?php echo $this->tag->linkTo(array("?page=".$page->next, 'Siguiente')) ?></li>
            <li><?php echo $this->tag->linkTo(array("?page=".$page->last, 'Última')) ?></li>
		</ul>
		<p><?php echo "Página ", $page->current, " de ", $page->total_pages; ?></p>
		</center>
		<?php
		}
		?>
	</div>
</div>
<script type="text/javascript">
//objeto javascript al que le añadimos toda la funcionalidad del crud
var crudPhalcon = {};
$(document).ready(function()
{
	//mostramos la modal para crear un post
	crudPhalcon.add = function()
	{
		var html = "";
		$("#modalCrudPhalcon .modal-title").html("Crear un nuevo post");
		html += '<?php echo $this->tag->form(array("index/add", "method" => "post", "id" => "form")); ?>';
		html += crudPhalcon.csrfProtection();
		html += '<label>Título</label>';
		html += '<input type="text" name="title" class="form-control">';
		html += '<label class="control-label">Contenido</label>';
		html += '<textarea class="form-control" name="content" rows="3"></textarea>';
		html += '<?php echo $this->tag->endForm() ?>';
		$("#onclickBtn").attr("onclick","crudPhalcon.addPost()").text("Crear").show();
		$("#modalCrudPhalcon .modal-body").html(html);
		$("#modalCrudPhalcon").modal("show");
	},
	//mostramos la modal para editar un post con sus datos
	crudPhalcon.edit = function(post)
	{
		//en post tenemos todos los datos del post parseado
		var json = crudPhalcon.parse(post), html = "";
		$("#modalCrudPhalcon .modal-title").html("Editar " + json.title);
		html += '<?php echo $this->tag->form(array("index/edit", "method" => "post", "id" => "form")); ?>';
		html += crudPhalcon.csrfProtection();
		html += '<label>Título</label>';
		html += '<input type="text" name="title" value="'+json.title+'" class="form-control">';
		html += '<label class="control-label">Contenido</label>';
		html += '<textarea class="form-control" name="content" rows="3">'+json.content+'</textarea>';
		html += '<input type="hidden" name="id" value="'+json.id+'" />';
		html += '<?php echo $this->tag->endForm() ?>';
		$("#onclickBtn").attr("onclick","crudPhalcon.editPost()").text("Editar").show();
		$("#modalCrudPhalcon .modal-body").html(html);
		$("#modalCrudPhalcon").modal("show");
	},
	//mostramos la modal para eliminar un post
	crudPhalcon.delete = function(post)
	{
		var json = crudPhalcon.parse(post), html = "";
		$("#modalCrudPhalcon .modal-title").html("Eliminar " + json.title);
		html += "<p class='alert alert-warning'>¿Estás seguro que quieres eliminar el post?</div>";
		$("#onclickBtn").attr("onclick","crudPhalcon.deletePost("+json.id+")").text("Eliminar").show();
		$("#modalCrudPhalcon .modal-body").html(html);
		$("#modalCrudPhalcon").modal("show");
	},
	//hacemos la petición ajax para añadir un nuevo post
	crudPhalcon.addPost = function()
	{
		$.ajax({
			url: "<?php echo $this->url->get('index/add') ?>",
			data: $("#form").serialize(),
			method: "POST",
			success: function(data)
			{
				$("#modalCrudPhalcon .modal-body").html("").html(
					"<p class='alert alert-success'>Post creado correctamente.</p>"
				);
				$("#onclickBtn").hide();
			},
			error: function(error)
			{
				console.log(error);
			}
		});
	},
	//hacemos la petición ajax para editar un post
	crudPhalcon.editPost = function()//procesamos la edición
	{
		$.ajax({
			url: "<?php echo $this->url->get('index/edit') ?>",
			data: $("#form").serialize(),
			method: "POST",
			success: function(data)
			{
				$("#modalCrudPhalcon .modal-body").html("").html(
					"<p class='alert alert-success'>Post actualizado correctamente.</p>"
				);
				$("#onclickBtn").hide();
			},
			error: function(error)
			{
				console.log(error);
			}
		})
	},
	//hacemos la petición ajax para eliminar un post
	crudPhalcon.deletePost = function(id)
	{
		$.ajax({
			url: "<?php echo $this->url->get('index/delete') ?>",
			data: "id="+id,
			method: "GET",
			success: function(data)
			{
				$("#modalCrudPhalcon .modal-body").html("").html(
					"<p class='alert alert-success'>Post eliminado correctamente.</p>"
				);
				$("#onclickBtn").hide();
			},
			error: function(error)
			{
				console.log(error);
			}
		});
	},
	//devuelve un json parseado para utilizar con javascript
	crudPhalcon.parse = function(post)
	{
		return JSON.parse(post);
	},
	//devuelve el campo oculto para evitar csrf en phalcon
	crudPhalcon.csrfProtection = function()
	{
		return '<input type="hidden" name="<?php echo $this->security->getTokenKey() ?>"'+
        	   'value="<?php echo $this->security->getToken() ?>"/>';
	}
});
</script>
<!--ventana modal de bootstrap que utilizaremos para cada caso, crear, editar y eliminar-->
<div class="modal fade" id="modalCrudPhalcon" tabindex="-1" role="dialog" aria-labelledby="modalCrudPhalconLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
        	<button type="button" id="onclickBtn" class="btn btn-success">Enviar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>

