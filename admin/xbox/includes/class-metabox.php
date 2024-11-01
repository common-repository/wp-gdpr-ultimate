<?php namespace Xbox\Includes;

class Metabox extends XboxCore {

	public function __construct( $args = array() ) {

		if( ! is_array( $args ) || Functions::is_empty( $args ) || empty( $args['id'] ) ){
			return;
		}

		$args['id'] = sanitize_title( $args['id'] );

		$this->args = wp_parse_args( $args, array(
			'id'         => '',
			'title'      => __( 'Xbox Metabox', 'xbox' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'post_types' => 'post',
			'closed'     => false,
		));

		$this->object_type = 'metabox';

		$this->args['post_types'] = (array) $this->args['post_types'];

		parent::__construct( $this->args );

		$this->hooks();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Acceso al id del objecto actual, post id o page id
	|---------------------------------------------------------------------------------------------------
	*/
	public function set_object_id( $object_id = 0 ){
		return 0;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Register Hooks
	|---------------------------------------------------------------------------------------------------
	*/
	private function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes') );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Add metaboxes
	|---------------------------------------------------------------------------------------------------
	*/
	public function add_meta_boxes(){
		if ( ! $this->should_show() ) {
			return;
		}

		foreach ( $this->arg( 'post_types' ) as $post_type ) {
			add_meta_box(
				$this->id,
				$this->args['title'],
				array( $this, 'build_metabox' ),
				$post_type,
				$this->args['context'],
				$this->args['priority']
			);
			add_filter( "postbox_classes_{$post_type}_{$this->id}", array( $this, "add_metabox_classes") );
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build metabox
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_metabox(){
		$display = "<div style='padding: 20px; font-family: Open Sans, arial;'>In the free version you can only create 'Admin pages'. To create metaboxes, you need the PRO Version. ".$this->get_pro()."</div>";
		echo $display;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Add a class 'xbox-postbox' to a Metabox
	|---------------------------------------------------------------------------------------------------
	*/
	public function add_metabox_classes( $classes = array() ){
		array_push( $classes,'xbox-postbox' );
		if( $this->arg( 'closed' ) && empty( $this->args[ 'header' ] ) ){
			array_push( $classes, 'closed' );
		}
    return $classes;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si se debe mostrar el metabox
	|---------------------------------------------------------------------------------------------------
	*/
	public function should_show(){
		return true;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Save metabox options
	|---------------------------------------------------------------------------------------------------
	*/
	public function save_metabox( $post_id, $post, $update ){
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Guarda un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function set_field_value( $field_id, $value = '', $post_id = '' ){
		return false;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene el valor de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_field_value( $field_id, $post_id = '', $default = '' ){
		return '';
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Verifica si el metabox puede ser guardado
	|---------------------------------------------------------------------------------------------------
	*/
	public function can_save_metabox( $post ) {
		//Verify nonce
	  return false;
	}






}