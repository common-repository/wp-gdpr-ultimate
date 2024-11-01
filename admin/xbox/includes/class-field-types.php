<?php namespace Xbox\Includes;

class FieldTypes {
	protected $field = null;

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor de la clase
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $field = null ){
		$this->field = $field;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Función por defecto, permite contruir un tipo de campo inexsistente
	|---------------------------------------------------------------------------------------------------
	*/
	public function __call( $field_type, $arguments ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build(){
		$type = $this->field->arg( 'type' );
		return $this->{$type}( $type );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: button
	|---------------------------------------------------------------------------------------------------
	*/
	public function button( $type = '' ){
		$return = '';
		$options = $this->field->arg( 'options' );
		$attributes = $this->field->arg( 'attributes' );
		$content = $this->field->get_result_callback( 'content' );

		$default_attributes = array(
			'name' => $this->field->get_name(),
			'id' => Functions::get_id_attribute_by_name( $this->field->get_name() ),
			'class' => "xbox-element xbox-btn xbox-btn-{$options['size']} xbox-btn-{$options['color']}"
		);
		if( $options['tag'] != 'a' ){
			$default_attributes['type'] = 'button';
		}
		$attributes = Functions::nice_array_merge(
			$default_attributes,
			$attributes,
			array('name', 'id'),
			array('class' => ' ')
		);
		$attributes = $this->join_attributes( $attributes );
		$content = $options['icon'].$content;

		if( $options['tag'] == 'a' ){
			$return .= "<a {$attributes}>{$content}</a>";
		} else if( $options['tag'] == 'input' ){
			$return .= "<input {$attributes} value='{$content}'>";
		} else if( $options['tag'] == 'button' ){
			$return .= "<button {$attributes}>{$content}</button>";
		}
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: code_editor
	|---------------------------------------------------------------------------------------------------
	*/
	public function code_editor( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: colorpicker
	|---------------------------------------------------------------------------------------------------
	*/
	public function colorpicker( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: checkbox
	|---------------------------------------------------------------------------------------------------
	*/
	public function checkbox( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: file
	|---------------------------------------------------------------------------------------------------
	*/
	public function file( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build item file
	|---------------------------------------------------------------------------------------------------
	*/
	private function build_file_item( $preview_size, $value, $multiple, $attachment_field, $index = null ){
		$return = '';
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: hidden
	|---------------------------------------------------------------------------------------------------
	*/
	public function hidden( $type = '' ){
		return $this->build_input( 'hidden' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: html
	|---------------------------------------------------------------------------------------------------
	*/
	public function html( $type = '' ){
		return $this->field->get_result_callback( 'content' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: icon_seletor
	|---------------------------------------------------------------------------------------------------
	*/
	public function icon_selector( $type = '' ){
		$return = '';
		$items = $this->field->arg( 'items' );
		$options = $this->field->arg( 'options' );
		$value = $this->field->get_value();
		$return .= $this->build_input( 'hidden' );
		$return .= "<div class='xbox-icon-actions xbox-clearfix'>";
			$return .= "<div class='xbox-icon-active xbox-item-icon-selector'>";
				if( Functions::ends_with( '.svg', $value ) ){
					$return .= "<img src='$value'>";
				} else {
					$return .= "<i class='$value'></i>";
				}
			$return .= "</div>";
			if( ! $options['hide_search'] ){
				$return .= "<input type='text' class='xbox-search-icon' placeholder='Search icon...'>";
			}
			if( ! $options['hide_buttons'] ){
				$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='all'>All</a>";
				$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='font'>Icon font</a>";
				$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal' data-search='.svg'>SVG</a>";
			}
		$return .= "</div>";

		$data = json_encode( $options );
		$return .= "<div class='xbox-icons-wrap xbox-clearfix' data-options='{$data}' style='height:{$options['wrap_height']} '>";
			$icons_html = '';
			if( ! $options['load_with_ajax'] ){
				foreach ( $items as $value => $icon ){
					$key = 'font ' . $value;
					$type = 'icon font';
					if( Functions::ends_with( '.svg', $value ) ){
						$type = 'svg';
						$key = explode('/', $value );
						$key = end($key);
						$font_size = 'inherit';
					} else {
						$font_size = (intval($options['size'])-14) .'px';//14 = padding vertical + border vertical
						$icon = preg_replace('/(<i\b[^><]*)>/i', '$1 style="">', $icon);
					}
					$icons_html .= "<div class='xbox-item-icon-selector' data-value='$value' data-key='$key' data-type='$type' style='width: {$options['size']}; height: {$options['size']}; font-size: {$font_size}'>";
						$icons_html .= $icon;
					$icons_html .= "</div>";
				}
			}
			$return .= $icons_html;
		$return .= "</div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: image
	|---------------------------------------------------------------------------------------------------
	*/
	public function image( $type = '' ){
		$return = '';
		$value = $this->field->get_value();
		$image_class = 'xbox-element-image ' . $this->field->arg( 'options', 'image_class' );

		if( $this->field->arg( 'options', 'hide_input' ) ){
			$return .= $this->build_input( 'hidden' );
		} else {
			$return .= $this->build_input( 'text' );
			$return .= "<a class='xbox-btn-input xbox-btn xbox-btn-icon xbox-btn-small xbox-btn-teal xbox-get-image' title='Preview'><i class='xbox-icon xbox-icon-refresh'></i></a>";
		}

		$return .= "<ul class='xbox-wrap-preview xbox-wrap-image xbox-clearfix' data-image-class='{$image_class}'>";
			$return .= "<li class='xbox-preview-item xbox-preview-image'>";
				$return .= "<img src='{$value}' class='{$image_class}'";
				if( empty( $value ) ){
					$return .= " style='display: none;'";
				}
				$return .= ">";
				$return .= "<a class='xbox-btn xbox-btn-iconize xbox-btn-small xbox-btn-red xbox-remove-preview'";
				if( empty( $value ) ){
					$return .= " style='display: none;'";
				}
				$return .= "><i class='xbox-icon xbox-icon-times-circle'></i></a>";
			$return .= "</li>";
		$return .= "</ul>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: image_seletor
	|---------------------------------------------------------------------------------------------------
	*/
	public function import( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	public function export( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: image_seletor
	|---------------------------------------------------------------------------------------------------
	*/
	public function image_selector( $type = '' ){
		$items = $this->field->arg( 'items' );
		if( Functions::is_empty( $items ) ){
			return '';
		}
		$items_desc = $this->field->arg( 'items_desc' );
		$options = $this->field->arg( 'options' );
		$wrap_class = 'xbox-radiochecks init-image-selector';
		if( $this->field->arg( 'options', 'in_line' ) == false ){
			$wrap_class .= ' xbox-vertical';
		}
		$data_image_chooser = json_encode( $options );
		$return = "<div class='$wrap_class' data-image-selector='$data_image_chooser'>";
			$return .= "<select name='select-privacy-page'";
			$selectedvalue = xbox_get_field_value('wp-gdpr-ultimate','select-privacy-page');

			foreach ( $items as $key => $image ){
				if ($selectedvalue == $image) {
					$select = "selected='selected'";
				}
				else {
					$select = ' ';
				}
				$item_class = "xbox-item-image-selector item-key-{$key}";
				// if( ( $key == 'from_file' || $key == 'from_url' ) && ( $options['import_from_file'] || $options['import_from_url'] ) ){
				//	$item_class .= " xbox-block";
				//	}
					$label_class = "";
					if( ! Functions::get_file_extension( $image ) ){
						$label_class .= "no-image";
					}
						$return .= "<option  value='$image' $select>".$key."</option>";
			}
		$return .= "</select>";
		$return .= "</div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: number
	|---------------------------------------------------------------------------------------------------
	*/
	public function number( $type = '' ){
		$attributes = $this->field->arg( 'attributes' );
		$options = $this->field->arg( 'options' );
		if( ! Functions::is_empty( $attributes ) ){
			foreach( $attributes as $attr => $val ){
				if( in_array( $attr, array( 'min', 'max', 'step', 'precision') ) ){
					$this->field->args['attributes']['data-'.$attr] = $val;
				}
			}
		}
		$unit_picker = (array) $options['unit_picker'];
		$has_unit_picker = is_array($unit_picker) && count( $unit_picker ) > 0 ? true : false;
		$unit_field = $this->field->get_parent()->get_field( $this->field->id.'_unit' );
		$unit_field_name = $unit_field->get_name( $this->field->index );
		$unit_value = $unit_field->get_value( true, 'esc_attr', $this->field->index );
		if( ! $has_unit_picker ){
			$unit_value = $options['unit'];
		}
		$return = $this->build_input( 'text', '', array( 'data-unit' => $unit_value ), 'esc_attr', array( 'min', 'max', 'step', 'precision' ) );
		$return .= "<div class='xbox-unit xbox-noselect xbox-unit-has-picker-{$has_unit_picker}' data-unit='{$options['unit']}'>";

		$return .= "<input type='hidden' name='{$unit_field_name}' value='{$unit_value}' class='xbox-unit-number'>";
			if( $options['show_unit'] ){
				$unit_text = $has_unit_picker ? $unit_picker[$unit_value] : $unit_value;
				$title = $unit_text == '#' ? 'Without unit' : '';
				$return .= "<span title='$title'>{$unit_text}</span>";
			}
			if( $has_unit_picker && $options['show_unit'] ){
				$return .= "<i class='xbox-icon xbox-icon-caret-down xbox-unit-picker'></i>";
				$return .= "<div class='xbox-units-dropdown'>";
					foreach($unit_picker as $unit => $display){
						$title = $display == '#' ? 'Without unit' : '';
						$return .= "<div class='xbox-unit-item' data-value='$unit' title='$title'>$display</div>";
					}
				$return .= "</div>";
			}
			$return .= "<a href='javascript:;' class='xbox-spinner-control' data-spin='up'><i class='xbox-icon xbox-icon-caret-up xbox-spinner-handler'></i></a>";
			$return .= "<a href='javascript:;' class='xbox-spinner-control' data-spin='down'><i class='xbox-icon xbox-icon-caret-down xbox-spinner-handler'></i></a>";
		$return .= "</div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: oembed
	|---------------------------------------------------------------------------------------------------
	*/
	public function oembed( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: radio
	|---------------------------------------------------------------------------------------------------
	*/
	public function radio( $type = '' ){
		$items = $this->field->arg( 'items' );
		if( Functions::is_empty( $items ) ){
			return '';
		}
		$wrap_class = "xbox-radiochecks init-icheck";
		if( $this->field->arg( 'options', 'in_line' ) == false ){
			$wrap_class .= ' xbox-vertical';
		}
		if( $this->field->arg( 'options', 'sortable' ) ){
			$wrap_class .= ' xbox-sortable';
		}
		$return = "<div class='$wrap_class'>";
		$temp = array();

		foreach ( $items as $key => $display ){
			$key = (string) $key;//Permite 0 como clave
			$html_item = "<label>";
				$html_item .= $this->build_input( $type, $key ) . $display;
			$html_item .= "</label>";
			$temp[$key] = $html_item;
		}

		if( $type == 'checkbox' ){
			$value = $this->field->get_value( false );
			if( ! Functions::is_empty( $value ) ){
				foreach( $value as $key ){
					$return .= $temp[$key];
					unset( $temp[$key] );
				}
			}
		}
		foreach( $temp as $key => $html ){
			$return .= $html;
		}
		$return .= "</div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: select
	|---------------------------------------------------------------------------------------------------
	*/
	public function select( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: switcher
	|---------------------------------------------------------------------------------------------------
	*/
	public function switcher( $type = '' ){
		$attributes = $this->field->arg( 'attributes' );
		$attributes['data-switcher'] = json_encode( $this->field->arg( 'options' ) );
		$attributes = Functions::nice_array_merge(
			$attributes,
			array( 'class' => 'xbox-element-switcher' ),
			array(),
			array( 'class' => ' ')
		);
		return $this->build_input( 'hidden' , '', $attributes );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: text
	|---------------------------------------------------------------------------------------------------
	*/
	public function text( $type = '' ){
		$return = '';
		$return .= $this->build_input( 'text' );
		$options = $this->field->arg( 'options' );
		$value = $this->field->get_value( true );
		if( ! empty( $options['helper'] ) ){
			$helper = $options['helper'];
			if( $helper == 'maxlength' && $maxlength = $this->field->arg( 'attributes', 'maxlength' ) ){
				$helper = strlen( $value ) . '/'. $maxlength;
			}
			$return .= "<span class='xbox-field-helper'>$helper</span>";
		}
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: title
	|---------------------------------------------------------------------------------------------------
	*/
	public function title(){
		$title_class = $this->field->arg( 'attributes', 'class' );
		$title = $this->field->arg( 'name' );
		if( ! empty( $title ) ){
			return "<h3 class='xbox-field-title $title_class'>$title</h3>";
		}
		return '';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: textarea
	|---------------------------------------------------------------------------------------------------
	*/
	public function textarea( $type = '' ){
		return $this->build_textarea( 'textarea' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: textarea
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_textarea( $type = '' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build field type: wp_editor
	|---------------------------------------------------------------------------------------------------
	*/
	public function wp_editor( $type = '' ) {
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build input
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_input( $type = 'text', $value = '', $attributes = array(), $escaping_function = 'esc_attr', $exclude_attributes = array() ){
		$attributes = wp_parse_args( $attributes, $this->field->arg( 'attributes' ) );
		$field_value = $this->field->get_value( true, $escaping_function );
		$value = $value !== '' ? esc_attr( $value ) : $field_value;

		$element_attributes = array(
			'type' => $type,
			'name' => $this->field->get_name(),
			'id' => Functions::get_id_attribute_by_name( $this->field->get_name() ),
			'value' => $value,
			'data-value' => $value,
			'class' => "xbox-element xbox-element-{$type}"
		);

		if( $type == 'radio' && $value == $field_value ){
			$element_attributes['checked'] = 'checked';
		}
		if( $type == 'checkbox' && is_array( $field_value ) && in_array( $value, $field_value ) ){
			$element_attributes['checked'] = 'checked';
		}
		if( $type == 'radio' || $type == 'checkbox' ){
			unset( $element_attributes['id'] );
			unset( $attributes['id'] );
			if( isset( $attributes['disabled'] ) ){
				if( is_array( $attributes['disabled'] ) && ! Functions::is_empty( $attributes['disabled'] ) ) {
					if( in_array( $value, $attributes['disabled'] ) ){
						$attributes['disabled'] = 'disabled';
					} else {
						unset( $attributes['disabled'] );
					}
				} else if( $attributes['disabled'] === true || $attributes['disabled'] == $value ){
					$attributes['disabled'] = 'disabled';
				} else {
					unset( $attributes['disabled'] );
				}
			}
		}

		// Une todos los atributos. Evita el reemplazo de ('name', 'id', 'value', 'checked')
		// y une los valores del atributo 'class'
		$attributes = Functions::nice_array_merge(
			$element_attributes,
			$attributes,
			array('name', 'id', 'value', 'checked'),
			array('class' => ' ')
		);

		//Remove invalid attributes
		foreach ( $attributes as $attr => $val ){
			if( is_array( $val ) ){
				unset( $attributes[$attr] );
			}
		}
		//Exclude attributes
		foreach ( $attributes as $attr => $val ){
			if( in_array( $attr, $exclude_attributes ) ){
				unset( $attributes[$attr] );
			}
		}

		$input = sprintf( '<input %s>', $this->join_attributes( $attributes ) );
		return $input;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Build select
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_select( $type = 'select', $value = '', $attributes = array(), $escaping_function = 'esc_attr' ){
		return $this->field->xbox->get_pro();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si la extensión de una imagen es válida
	|---------------------------------------------------------------------------------------------------
	*/
	public function is_image_file( $file_path = '' ){
		$extension = Functions::get_file_extension( $file_path );
		if( $extension && in_array( $extension, array( 'png', 'jpg', 'jpeg', 'gif', 'ico' ) ) ){
			return true;
		}
		return false;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si la extensión de una video válido
	|---------------------------------------------------------------------------------------------------
	*/
	public function is_video_file( $file_path = '' ){
		$extension = Functions::get_file_extension( $file_path );
		if( $extension && in_array( $extension, array( 'mp4', 'webm', 'ogv', 'ogg', 'vp8' ) ) ){
			return true;
		}
		return false;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Une los atributos de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function join_attributes( $attrs = array() ){
		$attributes = '';
		foreach ( $attrs as $attr => $value ){
			$quotes = '"';
			if( stripos( $attr, 'data-' ) !== false ){
				$quotes =  "'";
			}
			$attributes .= sprintf( ' %1$s=%3$s%2$s%3$s', $attr, $value, $quotes );
		}
		return $attributes;
	}
}
