<?php
/**
 * Class for converting text to image
 *
 */

// no direct access allowed
if ( ! defined('ABSPATH') )  exit;

/**
* Class for converting text to image
*/
class Averta_Image_Text {
	
	public $font = '';
	public $width;
	public $height;
	public $start_x;
	public $start_y;
	public $font_size;
	public $text_color;
	public $bg_color;
	public $wrap_length;

	function __construct( $font = '', $width = 600, $height = 400, $X = 0, $Y = 0, $font_size = 11, $text_color = array( 35, 35, 35 ), $bg_color = array( 250, 250, 250 ) ){
		$this->font    		= $font;
		$this->width   		= $width;
		$this->height  		= $height;
		$this->start_x 		= $X;
		$this->start_y 		= $Y;
		$this->font_size 	= $font_size;
		$this->text_color 	= $text_color;
		$this->bg_color 	= $bg_color;
	}


	function generate( $text, $file_name = null, $wrap_length = 0 ){
    	
	    $image = $this->get_image_font_object( $text, $wrap_length );
	    
	    imagepng( $image, $file_name ); 

	    imagedestroy( $image );

	    return $file_name;
	}


	function generate_display( $text, $file_name = null, $wrap_length = 0 ){
    	
	    $image = $this->get_image_font_object( $text, $wrap_length );
	    
	    imagepng( $image, $file_name ); 
	    imagedestroy( $image );
	    echo "<img src='$file_name' />";

	    return $file_name;
	}


	function render_image( $text, $wrap_length = 0 ){
    	
	    $image = $this->get_image_font_object( $text, $wrap_length );
	    header( "Content-type: image/png" );
	    imagepng( $image ); 

	    imagedestroy( $image );

	    return $image;
	}



	function get_image( $text, $wrap_length = 0 ){
    	
	    $image = $this->get_image_font_object( $text, $wrap_length );
	    ob_start();
	    imagepng( $image ); 
	    $image_content = ob_get_clean();
	    imagedestroy( $image );

	    return $image_content;
	}



	function get_image_font_object( $text, $wrap_length = 0 ){
    
	    if( ! isset( $text ) ) 
	    	return null;
	    
	    if( $wrap_length )  
	    	$text = $this->wrap_text( $text, $wrap_length );
	    
	    $height = $this->height;

	    if( 'auto' == $height ){
		    $box = @imageTTFBbox( $this->font_size, 0, $this->font, $text );
			$height = abs( $box[5] - $box[1] ) + $this->start_y + 10;
		}

		$width = $this->width;

		if( 'auto' == $width ){
		    $box = @imageTTFBbox( $this->font_size, 0, $this->font, $text );
			$width = abs( $box[4] - $box[0] ) + $this->start_x;
		}

	    $image = @imagecreate( $width, $height )
	        or die( 'Cannot Initialize new GD image stream' );

	    $background_color = imagecolorallocate( $image, $this->bg_color[0], $this->bg_color[1], $this->bg_color[2] );        // RGB color background.
	    $text_color = imagecolorallocate( $image, $this->text_color[0], $this->text_color[1], $this->text_color[2] );        // RGB color text.
	    
	    
	    imagettftext( $image, $this->font_size, 0, $this->start_x, $this->start_y, $text_color, $this->font, $text );

	    return $image;
	}



	function wrap_text( $text, $wrap_length = 0 ){

		if( ! $wrap_length ) return $text; 

		$wrapped_text = '';
		$lines = explode( "\n", $text );
		
		foreach ( $lines as $line ) {
			$wrapped_text .= wordwrap( $line, $wrap_length, "\n", true ) . "\n";
		}

	    return $wrapped_text;
	}

}







function generate_update_log_image( $post_id ) {

	$term_list = get_the_terms( $post_id, 'changelog-cat' );
	
	if( is_wp_error( $term_list ) || ! isset( $term_list[0] ) ) return;
	

	if( isset( $term_list[0]->slug ) ) {

		$term_name = $term_list[0]->slug;

		$upload_dir     = wp_upload_dir();

		$filename       = $term_name . '.png';

		$log_image_url  = $upload_dir['baseurl'] . '/changelog' . '/' . $filename;

		$log_image_path = $upload_dir['basedir'] . '/changelog';
		
		if ( wp_mkdir_p( $log_image_path ) ) {
			
			$log = file_get_contents( 'http://support.averta.net/envato/api/?log=' . $term_name . '&limit=-1&flush_log' );
			
			$font = dirname( __FILE__ ) . '/DroidSansMono.ttf';
			$img = new Averta_Image_Text( $font, 614, 'auto', 15, 23, 8.5, array( 30, 30, 30 ), array( 230, 230, 230 )  );

			$image_content = $img->get_image( $log, 83 );
			file_put_contents( $log_image_path . '/'. $filename, $image_content );
		}
		
	}

}