<?php
// phpcs:ignoreFile
if (!defined('ABSPATH')) exit;

GFForms::include_addon_framework();

class GF_Offload_File_Uploads extends GFAddOn {
	protected $s3_results = array();
	protected $_version = GF_SIMPLE_ADDON_VERSION;
	protected $_min_gravityforms_version = '2.4';
	protected $_slug = 'offload_file_uploads';
	protected $_path = 'gravityforms-offload-file-uploads/gravityforms-offload-file-uploads.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Offload File Uploads';
	protected $_short_title = 'Offload File Uploads';

	private static $_instance = null;

	private $awsaccesskey;
	private $awssecretkey;
	private $s3bucketname;
	private $region;
	private $s3filepermissions;
	/**
	 * Get an instance of this class.
	 *
	 * @since 1.0.0
	 * @return GF_Offload_File_Uploads
	 */
	public static function get_instance() {
		if (self::$_instance == null) {
			self::$_instance = new GF_Offload_File_Uploads();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 *
	 * @since 1.0.0
	 */
	public function init() {
	  parent::init();
      $this->s3_results = array();
	  $this->awsaccesskey = "AKIAY5S5RI75JWYLKRFR";
	  $this->awssecretkey = "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";
	  $this->s3bucketname = "credello-locals";
	  $this->region = "us-west-2";

      add_action('gform_pre_submission', [$this, 'pre_submission_handler'], 10, 2 );
	  add_action('gform_field_standard_settings', [$this, 'upload_field_setting'], 10, 2);
	  add_action('gform_editor_js', [$this, 'upload_field_setting_js']);
	  add_action('gform_after_submission', [$this, 'after_submission'], 10, 2);
	  add_action( 'gform_post_submission', [$this, 'set_post_content'], 10, 2 );
	  add_filter( 'gform_notification', [$this, 'change_autoresponder_email'], 10, 3 );
	}


				
	/**
	 * Prints custom setting to field.
	 *
	 * @since 1.0.0
	 * @param integer $index Specify the position that the settings should be displayed.
	 * @param integer $form_id The ID of the form from which the entry value was submitted.
	 */
	public function upload_field_setting($index, $form_id) {
		if ($index === 1600) {

			printf(
				'<li class="offload-file-uploads-setting field_setting">
					<label for="field_admin_label" class="section_label">
						%1$s
						<a href="#" onclick="return false;" onkeypress="return false;" class="gf_tooltip tooltip tooltip_form_field_offloads3" title="<h6>%1$s</h6>%2$s"><i class="fa fa-question-circle"></i></a>
					</label>
					<input class="offload-file-uploads-checkbox" name="offloads3_setting" type="checkbox" id="offloads3_setting" onclick="SetFieldProperty(\'offloadS3\', this.checked);">
					<label for="offloads3_setting" class="inline">%3$s</label>
				</li>',
				esc_html__('Offload files to Amazon S3', 'vital'),
				esc_html__('Enable this option to offload uploaded files in this field to Amazon S3. Local copies of files will be deleted after successful transfer', 'vital'),
				esc_html__('Enable', 'vital')
			);
		}
	}

	/**
	 * Prints JavaScript that handles custom setting on file upload fields.
	 *
	 * @since 1.0.0
	 */
	public function upload_field_setting_js() {
		?>
		<script type='text/javascript'>
		(function($) {
			$(document).on('gform_load_field_settings', function(event, field, form) {

				$('#offloads3_setting').attr('checked', field['offloadS3'] === true);
				$('#offloadftp_setting').attr('checked', field['offloadFtp'] === true);

				if (GetInputType(field) === 'fileupload' ) {
					$('.offload-file-uploads-setting').show();
				} else {
					$('.offload-file-uploads-setting').hide();
				}
			} );
		})(jQuery);
		</script>
		<?php
	}

	
   	/**
	 * Returns absolute path of file from URL.
	 *
	 * @since 1.0.0
	 * @param  string $file File URL.
	 * @param  integer $field_id Field ID that owns this file (so we can update the URL later)
	 * @return string File path
	 */
	public function get_file_info($file) {
		$file_url = parse_url($file);
		$file_path = untrailingslashit(ABSPATH) . $file_url['path'];
		$file_name = basename($file_url['path']);
		return [
			'path'     => $file_path,
			'name'     => $file_name,
		];
	}


	function pre_submission_handler( $form ) {
		
      

    	$s3 = new Aws\S3\S3Client([
		'region'  => $this->region,
		'version' => 'latest',
		'credentials' => [
		'key'    => $this->awsaccesskey,
		'secret' => $this->awssecretkey,
		]
		]);	

        $filearray = $_POST['gform_uploaded_files'];
        $files_array = array();

        if(!empty($filearray)){

	        $unique_id = $_POST['gform_unique_id'];
			$upload_path = GFFormsModel::get_upload_path( $form['id'] );
			//$upload_url = GFFormsModel::get_upload_url( $form['id'] );
	        
	        $filearray = str_replace("\\","",$filearray);

			foreach(json_decode($filearray) as $keys => $tempvalue){
				$ext = pathinfo($tempvalue, PATHINFO_EXTENSION);
				$file_name = $upload_path.'/tmp/'.$unique_id.'_'.$keys.'.'.$ext;
				if(file_exists($file_name)){
	                if($ext == 'mov' || $ext == 'mp4' || $ext == 'm4v' || $ext == 'avi' ){
					$fname = $_POST['input_1_3'].'-'.date('Y-m-d').'-'.time().'.'.$ext;
	                }else{
	                  $fname = $tempvalue;
	                }
	                $files_array[] = array('SourceFile'=> $file_name,'Key'=>$fname); 
               }
			}
        }
		
		
		foreach ($_FILES as $key => $value) {
			$file_name = $value['name'];   
			$temp_file_location = $value['tmp_name']; 
	        if(!empty($temp_file_location)){
	        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
	        if($ext == 'mov' || $ext == 'mp4' || $ext == 'm4v' || $ext == 'avi' ){
			  $fname = $_POST['input_1_3'].'-'.date('Y-m-d').'-'.time().'.'.$ext;
	        }else{
	         $fname = $file_name;
	        }
	        $files_array[] = array('SourceFile'=> $temp_file_location,'Key'=>$fname); 
		   }
		}

	   
        
        if(!empty($files_array)){
        	  foreach($files_array as $file_data){
                
				$results = $s3->putObject([
				'Bucket' => $this->s3bucketname,
				'Key'    => $file_data['Key'],
				'SourceFile' => $file_data['SourceFile']	
				]);
                $f_name = $results->get('ObjectURL');
                $f_name = basename($f_name);

				$ext = pathinfo($f_name, PATHINFO_EXTENSION);
				if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'pdf' ) {
				  $this->s3_results[0] =  $results->get('ObjectURL');
				}
				if ($ext == 'mov' || $ext == 'mp4' || $ext == 'm4v' || $ext == 'avi' ) {
				  $this->s3_results[1] =  $results->get('ObjectURL');
				}

			}
        }


	}



	/**
	 * Processes file upload fields.
	 *
	 * @since 1.0.0
	 * @param object $entry The entry that was just created.
	 * @param object $form The current form.
	 */
		public function after_submission($entry, $form) {
          
		  global $wpdb;

		  $key = 0; 
		  $entry_id = $entry['id'];
				foreach ($form['fields'] as $field) {
					if ($field->type === 'fileupload') {
							if ($files = rgar($entry, $field->id)) {
			          $field_id = $field->id;
					if (isset($field->offloadS3) && $field->offloadS3 === true) {
                       if(!empty($this->s3_results)){
			           $remote_files = $this->s3_results;
			           $temp = $remote_files[$key];
                       


							$file_info = $this->get_file_info($files);
							if (file_exists($file_info['path'])) {
								chmod($file_info['path'], 0777 );
								unlink($file_info['path']);
							}
			           $wpdb->query($wpdb->prepare("UPDATE wp_rg_lead_detail SET value='$temp' WHERE lead_id='$entry_id' AND field_number = '$field_id'"));
							   $key++; 
                             }
						   }
						}
						  
				 }
			}
		}




	  function change_autoresponder_email( $notification, $form, $entry ) {
                $key =0;
				foreach ($form['fields'] as $field) {
					if ($field->type === 'fileupload') {
							if ($files = rgar($entry, $field->id)) {
								if (isset($field->offloadS3) && $field->offloadS3 === true) {
								 $field_id = $field->id;
								if(!empty($this->s3_results)){
									$remote_files = $this->s3_results;
									$temp = $remote_files[$key];
									$entry[$field_id]	= $temp;
									$key++; 
								}
							}
						}
					}
				}
			return $notification;
		}

}
