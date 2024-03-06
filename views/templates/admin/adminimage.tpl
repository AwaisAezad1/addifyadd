{if isset($get_image)}

	<div class="col-lg-3">
		<img src="{$baseUrl}modules/addifyadd/views/img/{$get_image}" id="i" class="img-thumbnail">
	</div>
	<button id="r" onclick="del()" type="button" class="btn btn-danger">Remove{}</button>

	<script>
		function del() {
			var de1 = document.getElementById('i').style.display = 'none';
			var de1 = document.getElementById('r').style.display = 'none';
		}
	</script>

{/if}