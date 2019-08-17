<?php

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

function get_menus() {
    $menus = array();
    $data = wp_get_nav_menu_items( 'Menu' );
    foreach( $data as $value ) {
        if( $value->menu_item_parent == '0' ) {
            array_push( $menus, $value );
        }
    }
    return $menus;
}

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

function post_menus() {
    $request = file_get_contents( 'php://input' );
    $menu = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->connect_error;
        $response->data->status = 500;
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $sql = "insert into wp_posts ( ID, post_author, post_date, post_date_gmt, post_content,";
    $sql = $sql." post_title, post_excerpt, post_status, comment_status, ping_status, post_password,";
    $sql = $sql." post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered,";
    $sql = $sql." post_parent, guid, menu_order, post_mime_type, comment_count ) ";
    $sql = $sql."values ( null, '".$menu->post_author."', '".$date_time->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$date_time_gmt->format( 'Y-m-d H:i:s' )."', '".$menu->post_content."', ";
    $sql = $sql."'".$menu->post_title."', '".$menu->post_excerpt."', '".$menu->post_status."', ";
    $sql = $sql."'".$menu->comment_status."', '".$menu->ping_status."', '".$menu->post_password."', ";
    $sql = $sql."'".$menu->post_name."', '".$menu->to_ping."', '".$menu->pinged."', ";
    $sql = $sql."'".$date_time->format( 'Y-m-d H:i:s' )."', '".$date_time_gmt->format( 'Y-m-d H:i:s' )."', ";
    $sql = $sql."'".$menu->post_content_filtered."', '".$menu->post_parent."', '".$menu->guid."', ";
    $sql = $sql."'".$menu->menu_order."', '".$menu->post_mime_type."', '".$menu->comment_count."' );";
    if( !$connection->query( $sql )) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->error;
        $response->data->status = 500;
        return $response;
    }
    $connection->close();
    $response = new ResponseError();
    $response->code = 'ok';
    $response->message = 'Zapytanie poprawnie wykonane';
    $response->data->status = 200;
    return $response;
}
function put_menu() {
    $request = file_get_contents( 'php://input' );
    $menu = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->connect_error;
        $response->data->status = 500;
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $sql = "update wp_posts set post_title='".$menu->post_title."', post_modified='".$date_time;
    $sql = $sql."', post_modified_gmt='".$date_time_gmt."' where ID=".$menu->ID.";";
    if( !$connection->query( $sql )) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->error;
        $response->data->status = 500;
        return $response;
    }
    $connection->close();
    $response = new ResponseError();
    $response->code = 'ok';
    $response->message = 'Zapytanie poprawnie wykonane';
    $response->data->status = 200;
    return $response;
}
function delete_menu() {
    $request = file_get_contents( 'php://input' );
    $menu = json_decode( $request );
    $connection = @new mysqli( DB_HOST, DB_USER, DB_PASSWORD );
    if( $connection->connect_errno ) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->connect_error;
        $response->data->status = 500;
        return $response;
    }
    $connection->select_db( DB_NAME );
    $date_time = new DateTime( 'now', new DateTimeZone( 'Europe/Warsaw' ) );
    $date_time_gmt = new DateTime( 'now', new DateTimeZone( 'GMT' ) );
    $sql = 'delete from wp_posts where ID='.$menu->ID.';';
    if( !$connection->query( $sql )) {
        $response = new ResponseError();
        $response->code = 'no_server_response';
        $response->message = 'Error: '.$connection->error;
        $response->data->status = 500;
        return $response;
    }
    $connection->close();
    $response = new ResponseError();
    $response->code = 'ok';
    $response->message = 'Zapytanie poprawnie wykonane';
    $response->data->status = 200;
    return $response;
}

add_action('rest_api_init', function () {
    register_rest_route( 'api/v1', '/menus', array(
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
        'callback' => 'delete_menus'
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
        'callback' => 'delete_menus',
    ) );
} );