<?php

require_once WP_SVBTLE_EDITOR_DIR . '/views/process_post.php'; 

nocache_headers();
$page = "edit";
include('header.php');
?>

<aside id="logo" class="clearfix">
		<a href="index.php?page=dashboard">

		</a>
</aside>


<form action="" method="post" enctype="multipart/form-data">
	<?php if ($err != ""): ?>
		<?php echo "<p class='wps-notice'>".$err."</p>" ?>
	<?php elseif (isset($_GET['edit']) and ($_GET['success'] == "success")): ?>
		<?php echo "<p class='wps-notice'>Your post was successfully submitted.</p>" ?>
	<?php elseif (isset($_GET['edit']) and ($_GET['edit'] == "success")): ?>
		<?php echo "<p class='wps-notice'>Your post was successfully updated.</p>" ?>					
	<?php endif ?>

	<div class="wrap">

		<?php if (is_user_logged_in()): // checking weather or not the user has logged in.?>
			<?php if(isset($post_id)): ?>
				<input type="hidden" name="action" value="edit" />
				<input type="hidden" name="id" value="<?php echo $post_id; ?>" />
				<?php wp_nonce_field( 'manage-post' ); ?>
			<?php else: ?>
				<input type="hidden" name="action" value="post" />
				<?php wp_nonce_field( 'new-post' ); ?>
			<?php endif; ?>
				<textarea  id="post_title" class="text expand" name="post_title" placeholder="Title Here" size="60" tabindex="1"><?php echo $post_title;?></textarea>

			<p>
				<i class="icon-markdown"></i>
				<textarea name="post_content" id="post_content" placeholder="Write post here" class="content expand"  tabindex="2"><?php echo $post_content ?></textarea>
			</p>

		<?php else: ?>
			<?php // a lo mejor convendría un redirect? ?>
			<p>Sorry, you don't have permission to post new article!</p>
		<?php endif ?>
			
	</div><!-- .wrap -->

	<div class="buttons">
		<?php if (!empty($_GET['id'])): ?>
			<a href="<?php echo get_permalink($post_id) ?>" target="_blank" class="button preview">Preview</a>
		<?php endif ?>
		<a href="#external-url" class="open-external button">Option</a>
		
		<div class="double">
			<input type="radio" class="RadioClass" name="post_status" value="draft" <?php if($post_status == 'draft' or empty($_GET['id'])): ?>checked="checked"<?php endif; ?> id="">
			<a href="#" class="button <?php if(($post_status == 'draft') or empty($_GET['id'])): ?>checked<?php endif; ?>"><span class="tick">&#10004;</span>	Idea</a>
			
			<input type="radio" class="RadioClass" name="post_status" value="publish" <?php if($post_status == 'publish'): ?>checked="checked"<?php endif; ?> id="">
			<a href="#" class="button <?php if($post_status == 'publish'): ?>checked<?php endif; ?>"><span class="tick">&#10004;</span> Public</a>
		</div>
		<a href="index.php?page=edit&action=del&id=<?php echo $_GET['id'] ?>" class="button remove">Remove</a>
		
		
		<div class="overlay">
			<div id="external-url" >
				<label>External Url</label>
				<p><input type="text" placeholder="http://your-url.com" name="external_url" style="border: 1px solid black; padding: 4px; width: 300px" value="<?php echo $external_url ?>" id=""></p>
				<input class="button close-fancy" type="button" value="OK" />
			</div>
		</div><!-- .overlay -->

		
		<input type="submit" class="button" value="Save"/>

	</div><!-- .buttons -->
	
	

</form>

<?php if (!empty($_GET['id'])): ?>
     <div class="preview">
          <a href="#close" class="close button">×</a>
          <iframe class="preview" src="<?php echo get_permalink($post_id) ?>"></iframe>
     </div>
<?php endif ?>


<script type="text/javascript">

	var createAttachment = function(file) {
	  // var uid  = [App.username, (new Date).getTime(), 'raw'].join('-');

	  var data = new FormData();

	  data.append('file', file);
	  data.append('action', 'upload_attachment');

	  $.ajax({
	    url: "<?php echo admin_url('admin-ajax.php'); ?>",
	    data: data,
	    cache: false,
	    contentType: false,
	    processData: false,
	    type: 'POST',
	    
	    success: function( res ) {
        	var attID = res;
    	    // console.log('Success: ' + attID);
			
			var absText = '![' + file.name + ']('+attID+')';
	  		$('#post_content').insertAtCaret(absText);
	    },

	    error: function(request,error) 
		{
			// console.log(arguments);
			alert ( "Check your upload dir permissions or your file size uploading limits");
		}

	  });


	};



	$(document).ready(function() {
		$notice = $('p.wps-notice');
		if($notice.length) {
			$notice.fadeOut(2000);
		} 
		
		$('.open-external').click(function(){
			$('.overlay').show();
		});
		
		$('.close-fancy').click(function(){
			$('.overlay').hide();
		});
		
		$('.expand').autosize();
	});
</script>



<?php include('footer.php'); ?>