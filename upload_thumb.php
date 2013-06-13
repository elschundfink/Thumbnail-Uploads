<?php
/*
Plugin Name: Thumbnails Upload.
Plugin URI: http://www.elschpa.de
Description: This plugin will replace the default thumbnails of the wordpress gallery to the one specified.
Version: V.0.0.1
Author: Elschpa UG
Author URI: http://www.elschpa.de
License:Released under the WTFPL license - http://www.wtfpl.net/txt/copying/.
*/




//include the admin_function.php file. The purpose of this file is just make the code look clean.
//echo WP_PLUGIN_URL. '/upload_thumb/admin_functions.php'; exit;
//include(WP_PLUGIN_URL . '/upload_thumb/admin_functions.php');
	
init_plugin();


function upload_thumbnail_admin(){
	
	echo '<div class = "wrap">';
	echo '<div class="icon32" id="icon-themes"><br></div>';
	echo '<h2>Thumbnails Upload</h2>';
	
	
	the_form();
	
	echo '</div>';
	
	
}


function the_form(){
	?>	
		
		
		<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
		<div id="postbox-container-1" class="postbox-container">
		<div id="normal-sortables" class="meta-box-sortables ui-sortable">
			<div id="dashboard_right_now" class="postbox ">
			
				<div class="handlediv" title="Zum umschalten klicken"><br></div> 
				
				<h3 class="hndle">
					<span>Thumbnail Dimensions</span>
				</h3>
				<div>
				<form  id = "mydropzone" class = "" action="<?php echo site_url();?>/wp-admin/admin-ajax.php?action=upload_thumb" method="post" enctype="multipart/form-data">
					
					<table class = "form-table" >
						<tr valign = "top">
							<th scope="row"><strong>Thumbnail Size:</strong> </th>
							<td>
								<fieldset>
									
									<input type="text" class="small-text" value="" name="width" id = "width"> 
									<b>X</b> 
									<input type="text" class="small-text" value="" name="height" id = "height">
									&nbsp;<b>[ Width <b>X</b> Height ]</b>
									
								</fieldset>
							</td>
						</tr>
					</table>
					
				</form>
				</div>
			</div>
		</div></div></div>
		</div>
		
		<script type="text/javascript">
		<!--
			jQuery(document).ready(function(){

				
				/* var myDropzone = jQuery("form.mydropzone").dropzone({
					
					maxFilesize: 2
				});  */
				
				var myDropzone = jQuery("#my-dropzone").data("dropzone");
				
				myDropzone.on("success", function(file, responseText) {
					
					var msg = jQuery.parseJSON(responseText.substring(0, responseText.length - 1));

					if(msg.level == 0){
						alert('no uploaded');
						jQuery('.success-mark').hide();
						jQuery('.error-mark').show();
					}else{
						alert('yes uploaded');
						jQuery('.success-mark').show();
						jQuery('.error-mark').hide();
					}
					
					jQuery('.data-dz-errormessage').html(msg.msg);
					
				});
			});
		//-->
		</script>
		
		
<?php }


function init_plugin(){

	//add the thumbnail link in the damin menu.
	add_action('admin_menu', 'upload_thumbnails_actions');

	
	//load the css stye files.
	wp_enqueue_style('dropzone.css', plugins_url('/css/dropzone.css', __FILE__));

	//load the js script files.
	wp_enqueue_script("jquery");
	wp_enqueue_script( 'dropzone-amd-module.js', plugins_url( '/js/dropzone-amd-module.js', __FILE__ ));
	//wp_enqueue_script( 'dropzone.js', plugins_url( '/js/dropzone.js', __FILE__ ));
	
	

	
	
	
	//register the ajax handler for handeling of the forms submit values.
	add_action('wp_ajax_upload_thumb', 'ajaxResponse');


	function upload_thumbnails_actions(){
		//add_options_page('upload_thumbnail ', 'upload_thumbnail', 'manage_optiobs', 1, 'upload_thumbnail_admin' );
		add_menu_page("Thumbnail Upload","Thumbnail Upload",8,__FILE__,"upload_thumbnail_admin");
	}
}



function ajaxResponse(){

	$upload_path = wp_upload_dir();

	$allowed_file = array('jpg','JPG', 'png', 'PNG');

	$width = (int) $_POST['width'];
	$height  = (int) $_POST['height'];
			
	
	
	$uploaded_images = $_FILES['file'];
	
	$file_names = $uploaded_images['name'];
	
	
	
	$path = $upload_path['path'].'/';
	
	
	$existing_files = ListFiles($upload_path['basedir']);
	
	
	$fileLink = $path . $uploaded_images['name'];
			
	$ext = end(explode(".", $fileLink));
		
	$size = getimagesize($_FILES['file']['tmp_name']);

	
	$file_size = filesize($_FILES['file']['tmp_name']);
			
	if($file_size >(2*1024*1024)){
		
		$level = 0;
		$message = '<div class="error below-h2" id="error"><p><b>'.$uploaded_images['name'].'</b> was not uploaded. The file must be less then 2 MB. <br></p></div>';
	
	}else{

		if($width != $size[0] &&  $height != $size[1] ){

			$level = 0;
			$message = '<div class="error below-h2" id="error"><p><b>'.$uploaded_images['name'].'</b> was not uploaded. There was mismatch in the specified and actual height or width of the image  <br></p></div>';

		}else{

			$old_file = $upload_path['path'].'/'.$uploaded_images['name'];

			$uploading_path = '';

			$error_no_file = TRUE;
			$image_name_array = explode(".", $uploaded_images['name']);
				
			foreach ($existing_files as $existing_file){
					
				$file_path_info = pathinfo($existing_file);
					
				if($uploaded_images['name'] == $file_path_info['basename']){

					$uploading_path = $file_path_info['dirname'];

					$error_no_file = FALSE;
				}
			}

			if( $error_no_file != TRUE){
					
				if(!in_array($ext, $allowed_file)){

					$level = 0;
					$message ='<div class="error below-h2" id="error"><p><b>'.$uploaded_images['name'].'</b> was not uploaded. Please make sure it is in <i>.jpg </i> or <i>.png</i> format <br></p></div>';

				}else{

						$origen =$uploaded_images['name'][$i];
								
						$file_name         = substr($origen, 0, strlen($origen)-4);
						$file_extension  = substr($origen, strlen($origen)-4, strlen($origen));
						$frand = $file_name.'-'.$width .'x'.$height.$file_extension;
									
						$destinoFull = $destinoDir.$frand;
							
						if(move_uploaded_file($_FILES['file']['tmp_name'],$uploading_path.'/'.$destinoFull)){

							$level = 1;
							$message ='<div style = "background-color: #b0edbb;" class="updated below-h2" id="message"><p><b>'.$uploaded_images['name'].'</b> has been sucessfully replaced. <br></p></div>';

						}else{

							$level = 0;
							$message = '<div class="error below-h2" id="message"><p>Oops!!! Something went wrong. Could not upload <b>'.$uploaded_images['name'].'</b>. Please try again.<br></p></div>';
						}

						}
				
			}else{

				$level = 0;
				$message ='<div class="error below-h2" id="message"><p>The file <b>'.$uploaded_images['name']. '</b> was not found on the server. Please check the filename.<br></p></div>';
			}
		}
	}
	
	/* if($level ==1){
		echo json_encode(array('sucess'=>200));
	}else{
		echo json_encode(array('error'=>400));
	}
	 */
	echo json_encode(array('level' => $level, 'msg' => $message));
		
	
}

function ListFiles($dir) {

	if($dh = opendir($dir)) {

		$files = Array();
		$inner_files = Array();

		while($file = readdir($dh)) {
			if($file != "." && $file != ".." && $file[0] != '.') {
				if(is_dir($dir . "/" . $file)) {
					$inner_files = ListFiles($dir . "/" . $file);
					if(is_array($inner_files)) $files = array_merge($files, $inner_files);
				} else {
					array_push($files, $dir . "/" . $file);
				}
			}
		}

		closedir($dh);
		return $files;
	}
}

?>