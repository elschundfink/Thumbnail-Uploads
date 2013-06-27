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
		
		<script type="text/javascript">
		<!--
		
			Dropzone.options.mydropzone = { 
			  init: function() {
			    var mydropzone = this;
			    
			    jQuery("#clear_images").click(function() {
				    
					// remove all the files and start a new session.
			    	mydropzone.removeAllFiles();
			    	jQuery('#width').val('');
			    	jQuery('#height').val('');
			    	
			      });
			      
			    mydropzone.on("success", function(file, responseText) {
	
					var msg = jQuery.parseJSON(responseText);

					//display the red cross if the file was not replaced.
					if(msg.level == 0){

						file.previewElement.classList.add("dz-error");
						file.previewElement.querySelector("[data-dz-errormessage]").textContent = msg.msg;
					}
				});
			  }
			};
		
		-->
		</script>
		
		<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-2">
		<div id="postbox-container-1" class="postbox-container">
		<div id="normal-sortables" class="meta-box-sortables ui-sortable">
			<div id="dashboard_right_now" class="postbox ">
			
				<form  id = "mydropzone" class = "dropzone" action="<?php echo site_url();?>/wp-admin/admin-ajax.php?action=upload_thumb" method="post" enctype="multipart/form-data">
					
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
				<br>
				<input style = "float:center !important;" type="reset" value="Clear Images" accesskey="p" id="clear_images" class="button button-primary button-large" name="save">
				</div>
			</div>
		</div></div></div>
		
<?php }


function init_plugin(){

	//add the thumbnail link in the damin menu.
	add_action('admin_menu', 'upload_thumbnails_actions');

	
	//load the css stye files.
	wp_enqueue_style('dropzone.css', plugins_url('/css/dropzone.css', __FILE__));

	//load the js script files.
	wp_enqueue_script("jquery");
	//wp_enqueue_script( 'dropzone-amd-module.js', plugins_url( '/js/dropzone-amd-module.js', __FILE__ ));
	wp_enqueue_script( 'dropzone.js', plugins_url( '/js/dropzone.js', __FILE__ ));
	
	
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
	
	//get the list of the existing files in the upload folder.
	$existing_files = ListFiles($upload_path['basedir']);
	
	
	$fileLink = $path . $uploaded_images['name'];
			
	$ext = end(explode(".", $fileLink));
		
	$size = getimagesize($_FILES['file']['tmp_name']);

	
	$file_size = filesize($_FILES['file']['tmp_name']);

	//check the size of the file. If it is greater then 2 MB responds error.
	if($file_size >(2*1024*1024)){
		
		$level = 0;
		$message = 'The file must be less then 2 MB.';
	
	}else{

		// check the dimentions of the image and checks with the one specified in the input form.
		if($width != $size[0] &&  $height != $size[1] ){

			$level = 0;
			$message = 'Mismatch in the specified and actual height or width of the image.';

		}else{

			$old_file = $upload_path['path'].'/'.$uploaded_images['name'];

			$uploading_path = '';
			
			$error_no_file = TRUE;
			$image_name_array = explode(".", $uploaded_images['name']);
				
			foreach ($existing_files as $existing_file){
					
				$file_path_info = pathinfo($existing_file);
				
				// check if the file with the same name exists in the upload folder or not if not throw and error.
				if($uploaded_images['name'] == $file_path_info['basename']){

					$uploading_path = $file_path_info['dirname'];
					

					$error_no_file = FALSE;
				}
			}

			if( $error_no_file != TRUE){
				
				//check the file extension.
				if(!in_array($ext, $allowed_file)){

					$level = 0;
					$message ='Please make sure it is in .jpg or .png format ';

				}else{

						$origen =$uploaded_images['name'];
						
								
						 $file_name         = substr($origen, 0, strlen($origen)-4);
						
						$file_extension  = substr($origen, strlen($origen)-4, strlen($origen));
						$frand = $file_name.'-'.$width .'x'.$height.$file_extension;

						
						//remane the file according to the dimension that are provided in the input form.
						 $destinoFull = $destinoDir.$frand;

						//replace the file 
						if(move_uploaded_file($_FILES['file']['tmp_name'],$uploading_path.'/'.$destinoFull)){

							$level = 1;
							$message ='Sucessfully replaced.';

						}else{

							$level = 0;
							$message = 'Oops!!! Something went wrong. Could not upload. Please try again.';
						}

						}
				
			}else{

				$level = 0;
				$message ='File not found on the server. Please check the filename.';
			}
		}
	}
	
	echo json_encode(array('level' => $level, 'msg' => $message));
	die();
		
	
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

?>;
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