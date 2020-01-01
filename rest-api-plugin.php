<?php
/**
 * Plugin Name: REST API Plugin
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Mateusz Krasuski
 * Author URI: http://www.mywebsite.com
 */

defined( 'ABSPATH' );

class Data {
    public $status;
}

class ResponseError {
    public $date = Data::status;
    public $message;
    public $code;
}
require( ABSPATH.'wp-config.php');

// Metoda get do otrzymania wszystkich elementów Menu
function get_menus( $data ) {
    if( $data['id'] ) {
        $menus = array();
        $data = wp_get_nav_menu_items( 'Menu' );
        foreach( $data as $value ) {
            if( $value->ID == $data['id'] ) {
                array_push( $menus, $value );
            }
        }
        return $menus;
    } else {
        $menus = array();
        $data = wp_get_nav_menu_items( 'Menu' );
        foreach( $data as $value ) {
            if( $value->menu_item_parent == '0' ) {
                array_push( $menus, $value );
            }
        }
        return $menus;
    }
}

// Metoda get do otrzymania wszystkich elementów Podmenu
function get_sub_menus() {
    $data = wp_get_nav_menu_items( 'Menu' );
    $sub_menus = array();
    foreach( $data as $value ) {
        if( $value->menu_item_parent != '0' ) {
            array_push( $sub_menus, $value );
        }
    }
    return $sub_menus;
}

// Metoda post do dodania nowego elementu Menu i Podmenu
function post_menus() {
    $request = file_get_contents( 'php://input' );
    $menu = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $connection->set_charset( 'utf8' );
    $sql = "insert into wp_posts ( ID, post_author, post_date, post_date_gmt, post_content,";
    $sql = $sql." post_title, post_excerpt, post_status, comment_status, ping_status, post_password,";
    $sql = $sql." post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered,";
    $sql = $sql." post_parent, guid, menu_order, post_mime_type, comment_count, post_type ) ";
    $sql = $sql."values ( null, '".$menu->post_author."', '".$date_time->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$date_time_gmt->format( 'Y-m-d H:i:s' )."', '".$menu->post_content."', ";
    $sql = $sql."'".$menu->post_title."', '".$menu->post_excerpt."', '".$menu->post_status."', ";
    $sql = $sql."'".$menu->comment_status."', '".$menu->ping_status."', '".$menu->post_password."', ";
    $sql = $sql."'".$menu->post_name."', '".$menu->to_ping."', '".$menu->pinged."', ";
    $sql = $sql."'".$date_time->format( 'Y-m-d H:i:s' )."', '".$date_time_gmt->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$menu->post_content_filtered."', '".$menu->post_parent."', '".$menu->guid."', ";
    $sql = $sql."'".$menu->menu_order."', '".$menu->post_mime_type."', '".$menu->comment_count;
    $sql = $sql."', '".$menu->post_type."' );";
    if( !$connection->query( $sql )) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $sql = "select term_id from wp_terms where name='Menu';";
    if( $result = $connection->query( $sql ) ) {
        $raw = $result->fetch_assoc();
        $idMenu = $raw['term_id'];
        $result->free();
        $sql = "select ID from wp_posts where post_title='".$menu->post_title."' and post_type='".$menu->post_type."'";
        $result = $connection->query( $sql );
        $raw = $result->fetch_assoc();
        $result->free();
        $idItem = $raw['ID'];
        $sql = "insert into wp_term_relationships (object_id, term_taxonomy_id, term_order) values (".$idItem.",";
        $sql = $sql." ".$idMenu.", 0);";
        $connection->query( $sql );
    } else {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        $connection->close();
        return $response;
    }
    $connection->close();
    $response = array(
        'code' => 'ok',
        'message' => 'Zapis wykonany z powodzeniem',
        'data' => array( 'status' => 200 ) );
    return $response;
}

// Metoda put do aktualizacji elementu Menu i Podmenu
function put_menus() {
    $request = file_get_contents( 'php://input' );
    $menu = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $connection->set_charset( 'utf8' );
    $sql = "update wp_posts set post_title='".$menu->post_title."', post_name='".$menu->post_name;
    $sql = $sql."', post_modified='".$date_time->format( 'Y-m-d H:i:s' );
    $sql = $sql."', post_modified_gmt='".$date_time_gmt->format( 'Y-m-d H:i:s' )."' where ID=".$menu->ID.";";
    if( !$connection->query( $sql )) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $connection->close();
    $response = array(
        'code' => 'ok',
        'message' => 'Aktualizacja wykonana z powodzeniem',
        'data' => array( 'status' => 200 ) );
    return $response;
}

// Usuwanie dowolnego elementu
function delete_elements() {
    $request = file_get_contents( 'php://input' );
    $data = json_decode( $request );
    $id = $data->ID;
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $connection->select_db( DB_NAME );
    $sql = 'delete from wp_posts where ID='.$id.';';
    if( !$connection->query( $sql )) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    if( $data->post_type === 'nav_menu_item' ) {
        $sql = "delete from wp_term_relationships where object_id=".$id.";";
        if( !$connection->query( $sql )) {
            $response = array(
                'code' => 'no_server_response',
                'message' => 'Error: '.$connection->error,
                'data' => array( 'status' => 500 ) );
            return $response;
        }
        $sql = "select term_id from wp_terms where name='Menu';";
        $result = $connection->query( $sql );
        $raw = $result->fetch_assoc();
        $id = $raw['term_id'];
        $result->free();
        $sql = "select count from wp_term_taxonomy where term_id=".$id.";";
        $result = $connection->query( $sql );
        $raw = $result->fetch_assoc();
        $count = $raw['count'];
        $result->free();
        $count = intval( $count );
        $count -= 1;
        $sql = "update wp_term_taxonomy set count=".$count." where term_id=".$id.";";
        $connection->query( $sql );
    }
    $connection->close();
    $response = array(
        'code' => 'ok',
        'message' => 'Usunięcie wykonane z powodzeniem',
        'data' => array( 'status' => 200 ) );
    return $response;
}

// Pobieranie strony z bazy danych
function get_pagess( $data ) {
    if( $data['id'] ) {
        $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
        if( $connection->connect_errno ) {
            $response = array(
                'code' => 'no_server_response',
                'message' => 'Error: '.$connection->error,
                'data' => array( 'status' => 500 ) );
            return $response;
        }
        $connection->select_db( DB_NAME );
        $connection->set_charset( 'utf8' );
        $sql = 'select * from wp_posts where ID = '.$data['id'].';';
        if( $result = $connection->query( $sql )) {
            $response = $result->fetch_assoc();
            $connection->close();
            $result->free();
            return $response;
        } else {
            $response = array(
                'code' => 'no_server_response',
                'message' => 'Error: '.$connection->error,
                'data' => array( 'status' => 500 ) );
            return $response;
        }
    } else {
        $response = array();
        $pages = get_pages();
        foreach( $pages as $page ) {
            if( $page->post_type == 'page' ) {
                array_push( $response, $page );
            }
        }
        return $response;
    }
}

// Dodanie nowej strony
function post_pagess() {
    $request = file_get_contents( 'php://input' );
    $data = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $connection->set_charset( 'utf8' );
    $sql = "insert into wp_posts ( ID, post_author, post_date, post_date_gmt, post_content,";
    $sql = $sql." post_title, post_excerpt, post_status, comment_status, ping_status, post_password,";
    $sql = $sql." post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered,";
    $sql = $sql." post_parent, guid, menu_order, post_mime_type, comment_count, post_type ) ";
    $sql = $sql."values ( null, '".$data->post_author."', '".$date_time->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$date_time_gmt->format( 'Y-m-d H:i:s' )."', '".$data->post_content."', ";
    $sql = $sql."'".$data->post_title."', '".$data->post_excerpt."', '".$data->post_status."', ";
    $sql = $sql."'".$data->comment_status."', '".$data->ping_status."', '".$data->post_password."', ";
    $sql = $sql."'".$data->post_name."', '".$data->to_ping."', '".$data->pinged."', ";
    $sql = $sql."'".$date_time->format( 'Y-m-d H:i:s' )."', '".$date_time_gmt->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$data->post_content_filtered."', '".$data->post_parent."', '".$data->guid."', ";
    $sql = $sql."'".$data->menu_order."', '".$data->post_mime_type."', '".$data->comment_count;
    $sql = $sql."', '".$data->post_type."' );";
    if( !$connection->query( $sql ) ) {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        $connection->close();
        return $response;
    }
    if( $result = $connection->query( "select ID from wp_posts where post_title='".$data->post_title."' and post_type='".$data->post_type."';" ) ) {
        $raw = $result->fetch_assoc();
        $response = get_pagess( array( 'id' => $raw['ID'] ) );
        $connection->close();
        $result ->free();
        return $response;
    } else {
        $response = array(
            'code' => 'no_server_response',
            'message' => 'Error: '.$connection->error,
            'data' => array( 'status' => 500 ) );
        $connection->close();
        return $response;
    }
}

// Aktualizacja strony
function put_pagess( $data ) {
    if( $data['id'] ) {
        $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
        if( $connection->connect_errno ) {
            $response = array(
                'code' => 'no_server_response',
                'message' => 'Error: '.$connection->error,
                'data' => array( 'status' => 500 ) );
            return $response;
        }
        $connection->select_db( DB_NAME );
        $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
        $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
        $connection->set_charset( 'utf8' );
        $sql = "update wp_posts set post_author='".$data->post_author."', post_content='".$data->post_content."',";
        $sql = $sql." post_title='".$data->post_title."', post_excerpt='".$data->post_excerpt."',";
        $sql = $sql." post_status='".$data->post_status."', comment_status='".$data->comment_status."',";
        $sql = $sql." ping_status='".$data->ping_status."', post_password='".$data->post_password."',";
        $sql = $sql." post_name='".$data->post_name."', to_ping='".$data->to_ping."',";
        $sql = $sql." pinged='".$data->pinged."', post_modified='".$date_time->format( 'Y-m-d H:i:s' )."',";
        $sql = $sql." post_modified_gmt='".$date_time_gmt->format( 'Y-m-d H:i:s' )."',";
        $sql = $sql." post_content_filtered='".$data->post_content_filtered."', post_parent='".$data->post_parent."',";
        $sql = $sql." guid='".$data->guid."', menu_order='".$data->menu_order."',";
        $sql = $sql." post_mime_type='".$data->post_mime_type."', comment_count='".$data->comment_count."',";
        $sql = $sql." post_type='".$data->post_type."' where ID=".$data['id'].";";
        if( !$connection->query( $sql )) {
            $response = array(
                'code' => 'no_server_response',
                'message' => 'Error: '.$connection->error,
                'data' => array( 'status' => 500 ) );
            return $response;
        }
        $connection->close();
        $response = get_pagess( array( 'id' => $data['id'] ) );
        return $response;
    } else {}
}

// Dodanie nowego elementu media
function post_media() {
    $media = $_FILES['file'];
    $file_name = $media['name'];
    $file_type = $media['type'];
    $file_size = $media['size'];
    $file_tmp_name = $media['tmp_name'];
    $file_error = $media['error'];
    $file_name = str_replace( '【', '', $file_name );
    $file_name = str_replace( '】', '', $file_name );
    $file_name = str_replace( '🌹', '', $file_name );
    $file_name = str_replace( ' ', '-', $file_name );
    $source = str_replace('plugins/rest-api-plugin', 'media', __DIR__);
    if(move_uploaded_file( $file_tmp_name, $source.'/'.$file_name )) {
        header( 'Content-Type: application/json' );
        return array( 'done' => true, 'url' => '/wp-content/media/'.$file_name );
    } else {
        header( 'Content-Type: application/json' );
        return array( 'done' => false, 'url' => '' );
    }
}

function test() {
    $widget = new WP_Widget();
    //echo print_r( $widget->id );
    return array( 'name' => $widget );
}

add_action('rest_api_init', function () {
    register_rest_route( 'api/v1', '/menus', array(
        'methods' => 'GET',
        'callback' => 'get_menus',
    ) );

    register_rest_route( 'api/v1', '/menus/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_menus',
    ) );

    register_rest_route( 'api/v1', '/menus', array(
        'methods' => 'POST',
        'callback' => 'post_menus',
    ) );

    register_rest_route( 'api/v1', '/menus', array(
        'methods' => 'PUT',
        'callback' => 'put_menus'
    ) );

    register_rest_route( 'api/v1', '/menus', array(
        'methods' => 'DELETE',
        'callback' => 'delete_elements'
    ) );

    register_rest_route( 'api/v1', '/sub-menus', array(
        'methods' => 'GET',
        'callback' => 'get_sub_menus',
    ) );

    register_rest_route( 'api/v1', '/sub-menus', array(
        'methods' => 'POST',
        'callback' => 'post_menus',
    ) );
    
    register_rest_route( 'api/v1', '/sub-menus', array(
        'methods' => 'PUT',
        'callback' => 'put_menus',
    ) );

    register_rest_route( 'api/v1', '/sub-menus', array(
        'methods' => 'DELETE',
        'callback' => 'delete_elements',
    ) );

    register_rest_route( 'api/v1', '/pages', array(
        'methods' => 'GET',
        'callback' => 'get_pagess',
    ) );

    register_rest_route( 'api/v1', '/pages/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_pagess',
    ) );

    register_rest_route( 'api/v1', '/pages', array(
        'methods' => 'POST',
        'callback' => 'post_pagess',
    ) );

    register_rest_route( 'api/v1', '/pages/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'put_pagess',
    ) );

    register_rest_route( 'api/v1', '/pages', array(
        'methods' => 'PUT',
        'callback' => 'put_pagess'
    ) );

    register_rest_route( 'api/v1', '/pages', array(
        'methods' => 'DELETE',
        'callback' => 'delete_elements'
    ) );

    register_rest_route( 'api/v1', '/media', array(
        'methods' => 'POST',
        'callback' => 'post_media'
    ) );

    register_rest_route( 'api/v1', '/test', array(
        'methods' => 'GET',
        'callback' => 'test'
    ) );

} );


?>