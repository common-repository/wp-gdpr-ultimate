<?php
use Xbox\Includes\Metabox as Metabox;
use Xbox\Includes\AdminPage as AdminPage;


/*
|---------------------------------------------------------------------------------------------------
| Obtiene todas las instancias de Xbox
|---------------------------------------------------------------------------------------------------
*/
function xbox_get_all(){
  return Xbox::get_all_xboxs();
}

/*
|---------------------------------------------------------------------------------------------------
| Obtiene una instancia de Xbox
|---------------------------------------------------------------------------------------------------
*/
function xbox_get( $xbox_id ){
  return Xbox::get( $xbox_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Nuevo metabox
|---------------------------------------------------------------------------------------------------
*/
function xbox_new_metabox( $options = array() ){
  return new Metabox( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Nueva página de opciones
|---------------------------------------------------------------------------------------------------
*/
function xbox_new_admin_page( $options = array() ){
  return new AdminPage( $options );
}

/*
|---------------------------------------------------------------------------------------------------
| Retorna el valor de una opción
|---------------------------------------------------------------------------------------------------
*/
function xbox_get_field_value( $xbox_id, $field_id = '', $default = '', $post_id = '' ){
  return Xbox::get_field_value( $xbox_id, $field_id, $default, $post_id );
}

/*
|---------------------------------------------------------------------------------------------------
| Nuevo formulario basado en Xbox
|---------------------------------------------------------------------------------------------------
*/
// function xbox_new_form( $xbox_id = '', $form_args = array(), $echo = false ){
//   return AdminPage::get_form( $xbox_id, $form_args, $echo );
// }

