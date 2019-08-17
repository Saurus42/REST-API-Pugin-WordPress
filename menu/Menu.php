<?php

class Menu {
    __construct() {
        $menus = array();
        $data = wp_get_nav_menu_items( 'Menu' );
        foreach( $data as $value ) {
            if( $value->menu_item_parent == '0' ) {
                array_push( $menus, $value );
            }
        }
        $data = wp_get_nav_menu_items( 'Menu' );
        $sub_menus = array();
        foreach( $data as $value ) {
            if( $value->menu_item_parent != '0' ) {
                array_push( $sub_menus, $value );
            }
        }
    }
}

?>