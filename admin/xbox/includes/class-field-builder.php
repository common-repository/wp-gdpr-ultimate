<?php namespace Xbox\Includes;


class FieldBuilder {
	private $field = null;

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
	| Construye el campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build(){
		$return = '';

		switch( $this->field->arg( 'type' ) ){
			case 'private':
				break;

			case 'tab':
				if( $this->field->arg( 'action' ) == 'open' ){
					$return .= $this->build_tab_menu();
				} else {
					$return .= "</div></div><div class='xbox-separator xbox-separator-tab'></div>";//.xbox-tab-body .xbox-tab
				}
				break;

			case 'tab_item':
				$return .= $this->build_tab_item();
				break;

			case 'group':
				$return .= $this->build_group();
				break;

			case 'section':
				$return .= $this->build_section();
				break;

			default:
				$return .= $this->build_field();
				break;
		}

		return $this->field->xbox->get_object_id() ? $return : '';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contendor de campos mixtos
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_mixed(){
		$return = "";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el inicio de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_open_row(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$options = $this->field->arg( 'options' );
		$show_if = json_encode( (array) $options['show_if'] );
		$hide_if = json_encode( (array) $options['hide_if'] );
		$row_class = $this->get_row_class();
		$row_id = Functions::get_id_attribute_by_name( $this->field->get_name() );
		$content_class = "xbox-content";

		if( $this->field->in_mixed ){
			$content_class .= "-mixed";
		}
		$data_show_hide = json_encode(array(
			'show_if' => (array) $options['show_if'],
			'hide_if' => (array) $options['hide_if'],
			'effect' => $options['show_hide_effect'],
			'delay' => $options['show_hide_delay'],
		));

		$return .= "<div id='{$this->field->arg( 'row_id' )}' class='$row_class' data-row-level='{$this->field->get_row_level()}' data-field-id='{$this->field->id}' data-field-type='$type' data-show-hide='$data_show_hide'>";
			$return .= $this->build_label();
			$return .= "<div class='$content_class xbox-clearfix'>";

		if( $type == 'mixed' ){
			$return .= "<div class='xbox-wrap-mixed xbox-clearfix'>";
		}

		return $this->field->arg( 'insert_before_row' ) . $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el final de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_close_row(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$options = $this->field->arg( 'options' );
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		if( $type == 'mixed' ){
			$return .= "</div>"; //.xbox-wrap-mixed
		}

		//Field description
		if( ! Functions::is_empty( $description )  ){
			if( ! $options['desc_tooltip'] ){
				$return .= "<div class='xbox-field-description'><strong class='xbox-field-description-title'>$description_title</strong>$description</div>";
			} else if( ! $this->field->in_mixed ){
				$return .= "<div class='xbox-tooltip-handler xbox-icon xbox-icon-question' data-tipso='$description' data-tipso-title='$description_title' data-tipso-position='left'></div>";
			}
		}

		$return .= "</div>";//.xbox-content
		$return .= "</div>";//.xbox-row
		return $return . $this->field->arg( 'insert_after_row' );
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el menú de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_menu(){
		$return = "";
		$items = $this->field->arg( 'items' );
		$name = $this->field->arg( 'name' );
		$options = $this->field->arg( 'options' );
		$fields_prefix = $this->field->xbox->arg('fields_prefix');
		if( ! is_array( $items ) || Functions::is_empty( $items ) ){
			return '';
		}
		$item_tab = '';
		$i = 0;

		foreach ( $items as $key => $display ){
			$active = $i == 0 ? ' active' : ''; $i++;
			$sub_items = '';
			$item_class = 'xbox-item xbox-item-parent';
			if( ! is_array( $display ) ){
				$text = $display;
			} else {
				$text = isset( $display['text'] ) ? $display['text'] : 'Tab item';
				if( ! empty( $display['items'] ) && is_array( $display['items'] ) ){
					foreach( $display['items'] as $item => $show ){
						$sub_items .= "<li class='xbox-item tab-item-{$item} xbox-item-child' data-parent='$key' data-tab='#{$fields_prefix}tab_item-{$item}'>";
							$sub_items .= "<a href='#{$fields_prefix}tab_item-{$item}'>$show</a>";
						$sub_items .= "</li>";
					}
				}
			}

			if( $sub_items != '' ){
				$item_class .= ' xbox-item-has-childs';
			}
			$item_tab .= "<li class='$item_class tab-item-{$key} $active' data-item='$key' data-tab='#{$fields_prefix}tab_item-{$key}'>";
				$item_tab .= $sub_items == '' ? '': "<span class='xbox-toggle-icon'><i class='xbox-icon xbox-icon-chevron-down'></i></span>";
				$item_tab .= "<a href='#{$fields_prefix}tab_item-{$key}'>$text</a>";
			$item_tab .= "</li>";
			$item_tab .= $sub_items;
		}


		$tab_class = 'xbox-tab';

		if( $options['main_tab'] ){
			$tab_class .= ' xbox-main-tab xbox-tab-left';
		}

		if( $this->field->xbox->arg( 'context' ) == 'side' ){
			$tab_class .= ' accordion';
		}
		$tab_class .= ' ' . str_replace( $fields_prefix.'open-', '', $this->field->id );
		$tab_class .= " xbox-tab-{$options['skin']}";
		$tab_class .= " {$this->field->arg( 'attributes', 'class' )}";

		$return .= "<div class='$tab_class' data-tab-id='{$this->field->id}'>";
			$return .= "<div class='xbox-tab-header'>";
				$return .= "<nav class='xbox-tab-nav'><ul class='xbox-tab-menu xbox-clearfix'>";
					$return .= $item_tab;
				$return .= "</ul></nav>";
			$return .= "</div>";
			$return .= "<div class='xbox-tab-body'>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el contenido de los tabs
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_tab_item(){
		$return = "";
		$options = $this->field->arg('options');
		$data_tab = str_replace( 'open-', '', $this->field->id );
		$class = str_replace( $this->field->xbox->arg('fields_prefix').'tab_item-', '', $data_tab );
		if( $this->field->arg( 'action' ) == 'open' ){
			$return .= "<div class='xbox-tab-content tab-content-{$class}' data-tab='#{$data_tab}'>";
		} else if( $this->field->arg( 'action' ) == 'close' ){
			$return .= "</div>";
		}
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Crea el label
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_label(){
		$options = $this->field->arg( 'options' );
		if( ! $options['show_name'] ){
			return '';
		}

		$return = "";
		$label = $this->field->arg( 'name' );
		if( $this->field->arg( 'attributes', 'required' ) ){
			$label .= '<span class="xbox-required-field">*</span>';
		}
		$for = Functions::get_id_attribute_by_name( $this->field->get_name() );
		$description = $this->field->arg( 'desc' );
		$description_title = $this->field->arg( 'desc_title' );

		$return .= "<div class='xbox-label'>";
			$return .= $this->field->arg( 'insert_before_name' );

			//Field description
			if( ! $this->field->in_mixed || Functions::is_empty( $description ) || ! $options['desc_tooltip'] ){
				$return .= "<label for='$for' class='xbox-element-label'>$label</label>";
			} else {
				$return .= "<label for='$for' class='xbox-element-label'>$label <i class='xbox-tooltip-handler xbox-icon xbox-icon-question-circle' data-tipso='$description' data-tipso-title='$description_title'></i></label>";
			}

			if( $this->field->arg( 'type' ) == 'group' ){
				$controls = $this->field->arg( 'controls' );
				$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal xbox-add-group-item {$options['add_item_class']}' title='{$options['add_item_text']}' data-item-type='{$controls['default_type']}'><i class='xbox-icon xbox-icon-plus'></i>{$options['add_item_text']}</a>";
			}
			$return .= $this->field->arg( 'insert_after_name' );
		$return .= "</div>";

		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un section
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_section(){
		$return = "";
		$description = $this->field->arg( 'desc' );
		$options = $this->field->arg( 'options' );
		$data_toggle = json_encode( array(
			'effect' => $options['toggle_effect'],
			'target' => $options['toggle_target'],
			'speed' => $options['toggle_speed'],
			'open_icon' => $options['toggle_open_icon'],
			'close_icon' => $options['toggle_close_icon'],
		));

		$return .= "<div class='xbox-section xbox-clearfix xbox-toggle-{$options['toggle']} xbox-toggle-{$options['toggle_default']} xbox-toggle-{$options['toggle_target']}' data-toggle='$data_toggle' >";
			$return .= "<div class='xbox-section-header'>";
				$return .= "<h3 class='xbox-section-title'>{$this->field->arg( 'name' )}</h3>";
				if( ! Functions::is_empty( $description )  ){
					$return .= "<div class='xbox-field-description'>$description</div>";
				}
				if( $options['toggle'] ){
					$icon = $options['toggle_default'] == 'open' ? $options['toggle_open_icon'] : $options['toggle_close_icon'];
					$return .= "<span class='xbox-toggle-icon'><i class='xbox-icon $icon'></i></span>";
				}
			$return .= "</div>";//.xbox-section-header
			$return .= "<div class='xbox-section-body'>";
				foreach ( $this->field->fields_objects as $field ){
					$field_builder = new FieldBuilder( $field );
					$return .= $field_builder->build();
				}
			$return .= "</div>";//.xbox-section-body
		$return .= "</div>";//.xbox-section
		$return .= "<div class='xbox-separator xbox-separator-section'></div>";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group(){
		return '';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control(){
		return '';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye cada item del control de un grupo
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_control_item( $index = 0 ){
		$return = "";
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo de un grupo, si este campo es un grupo, entonces se vuelve a llamar a build_group()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_group_item(){
		$return = "";
		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un campo, si es un campo repetible se llama a build_repeatable_items()
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_field(){
		$return = "";
		$return .= $this->build_open_row();
			if( $this->field->arg( 'repeatable' ) ){
				$return .= $this->build_repeatable_items();
			} else {
				$return .= $this->build_field_type();
			}
		$return .= $this->build_close_row();
		return $return;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un los campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_items(){
		$return = "";
		$value = $this->field->get_value( true, 'esc_html', null, true );//default, escape, index, all
		$options = $this->field->arg( 'options' );
		$grid = $this->field->arg( 'grid' );

		$wrap_class = "xbox-repeatable-wrap";
		if( $options['sortable'] ){
			$wrap_class .= " xbox-sortable";
		}

		if( ! $this->field->in_mixed && $this->field->is_valid_grid_value( $grid ) ){
			$wrap_class .= " xbox-grid xbox-col-$grid";
		}

		$return .= "<div class='$wrap_class'>";
			if( empty( $value ) ){
				$return .= $this->build_repeatable_item();
			} else {
				foreach ( $value as $key => $field_id ){
					$return .= $this->build_repeatable_item();
					$this->field->index++;
				}
				$this->field->index = 0;
			}
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-teal xbox-add-repeatable-item {$options['add_item_class']}' title='{$options['add_item_text']}'><i class='xbox-icon xbox-icon-plus'></i>{$options['add_item_text']}</a>";
		$return .= "</div>";//.xbox-repeatable-wrap

		return $this->field->arg( 'insert_before_repeatable' ) . $return . $this->field->arg( 'insert_after_repeatable' );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Construye un item de campos repetibles
	|---------------------------------------------------------------------------------------------------
	*/
	public function build_repeatable_item(){
		$return = "";
		$options = $this->field->arg( 'options' );
		$return .= "<div class='xbox-repeatable-item' data-index='{$this->field->index}'>";
			$return .= $this->build_field_type();
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-iconize xbox-sort-item {$options['sort_item_class']}' title='{$options['sort_item_text']}'><i class='xbox-icon xbox-icon-sort'></i></a>";
			$return .= "<a class='xbox-btn xbox-btn-small xbox-btn-red xbox-btn-iconize xbox-opacity-80 xbox-remove-repeatable-item {$options['remove_item_class']}' title='{$options['remove_item_text']}'><i class='xbox-icon xbox-icon-times-circle'></i></a>";
		$return .= "</div>";//.xbox-repeatable-item

		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Construye el campo en sí
	|---------------------------------------------------------------------------------------------------
	*/
	private function build_field_type(){
		$return = "";
		$type = $this->field->arg( 'type' );
		$field_type = new FieldTypes( $this->field );

		$field_class = $this->get_field_class();
		$default = $this->field->arg( 'default' );
		if( is_array( $default ) ){
			$default = implode( ',', $default );
		}

		$return .= "<div id='{$this->field->arg( 'field_id' )}' class='$field_class' data-default='$default'>";
			$return .= $this->field->arg( 'prepend_in_field' );
			$return .= $field_type->build();
			$return .= $this->field->arg( 'append_in_field' );
		$return .= "</div>";//.xbox-field

		$return = $this->field->arg( 'insert_before_field' ) . $return . $this->field->arg( 'insert_after_field' );

		return $return;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de un campo
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_field_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$options = $this->field->arg( 'options' );

		$field_class[] = "xbox-field xbox-field-id-{$this->field->id}";

		if( ! $this->field->in_mixed && ! $this->field->arg( 'repeatable' ) && $this->field->is_valid_grid_value( $grid ) ){
			$field_class[] = "xbox-grid xbox-col-$grid";
		}
		if( $type == 'number' ){
			if( $options['show_unit'] ){
				$field_class[] = "xbox-has-unit";
			}
			if( $options['show_spinner'] ){
				$field_class[] = "xbox-show-spinner";
			}
			if( ! $options['disable_spinner'] ){
				$field_class[] = "xbox-has-spinner";
			}
		} else if( $type == 'radio' ){
			$field_class[] = "xbox-has-icheck";
		} else if( $type == 'text' && ! empty( $options['helper'] ) ){
			$field_class[] = "xbox-has-helper";
			if( $options['helper'] == 'maxlength' ){
				$field_class[] = "xbox-helper-maxlength";
			}
		}

		$field_class[] = $this->field->arg( 'field_class' );

		$field_class = implode( ' ', $field_class );

		return apply_filters( 'xbox_field_class', $field_class, $this );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Obtiene las clases de la fila
	|---------------------------------------------------------------------------------------------------
	*/
	private function get_row_class(){
		$type = $this->field->arg( 'type' );
		$grid = $this->field->arg( 'grid' );
		$row_class[] = "xbox-row xbox-clearfix xbox-type-{$type}";

		$row_class[] = "xbox-row-id-{$this->field->id}";

		$row_class[] = $this->field->arg( 'row_class' );

		//Visibility
		$row_class[] = $this->visibility_row_class();

		$row_class = implode( ' ', $row_class );

		return apply_filters( 'xbox_row_class', $row_class, $this );
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna la clase de visibilidad del campo
	|---------------------------------------------------------------------------------------------------
	*/
	public function visibility_row_class(){
		$options = $this->field->arg( 'options' );
		$show_if = (array) $options['show_if'];
		$hide_if = (array) $options['hide_if'];
		$parent = $this->field->get_parent();
		$show = true;
		$hide = false;
		$show_class = 'xbox-show';
		$hide_class = 'xbox-hide';

		//Show
		if( empty( $show_if ) || empty( $show_if[0] ) ){
			$show = true;
		} else if( is_array( $show_if[0] ) ){

		} else {
			$field = $parent->get_field( $show_if[0] );
			if( $field ){
				$field_value = $field->get_value();
				$value = '';
				$operator = '==';
				if( count( $show_if ) == 2 ){
					$value = isset( $show_if[1] ) ? $show_if[1] : '';
				} else if( count( $show_if ) == 3 ){
					$value = isset( $show_if[2] ) ? $show_if[2] : '';
					$operator = ! empty( $show_if[1] ) ? $show_if[1] : $operator;
					$operator = $operator == '=' ? '==' : $operator;
				}
				if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
					$show = Functions::compare_values_by_operator( $field_value, $operator, $value );
				} else if( in_array( $operator,  array('in', 'not in' ) ) ){
					if( ! empty( $value ) && is_array( $value ) ){
						$show = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
					}
				}
			}
		}

		//Hide
		if( empty( $hide_if ) || empty( $hide_if[0] ) ){
			$hide = false;
		} else if( is_array( $hide_if[0] ) ) {

		} else {
			$field = $parent->get_field( $hide_if[0] );
			if( $field ){
				$field_value = $field->get_value();
				$value = '';
				$operator = '==';
				if( count( $hide_if ) == 2 ){
					$value = isset( $hide_if[1] ) ? $hide_if[1] : '';
				} else if( count( $hide_if ) == 3 ){
					$value = isset( $hide_if[2] ) ? $hide_if[2] : '';
					$operator = ! empty( $hide_if[1] ) ? $hide_if[1] : $operator;
					$operator = $operator == '=' ? '==' : $operator;
				}
				if( in_array( $operator,  array('==', '!=', '>', '>=', '<', '<=') ) ){
					$hide = Functions::compare_values_by_operator( $field_value, $operator, $value );
				} else if( in_array( $operator,  array('in', 'not in' ) ) ){
					if( ! empty( $value ) && is_array( $value ) ){
						$hide = $operator == 'in' ? in_array( $field_value, $value ) : ! in_array( $field_value, $value );
					}
				}
			}
		}

		if( $show ){
			if( $hide == true ){
				return $hide_class;
			} else {
				return $show_class;
			}
		} else {
			return $hide_class;
		}

	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba datos
	|---------------------------------------------------------------------------------------------------
	*/
	private function check_data(){
		return true;
	}




}