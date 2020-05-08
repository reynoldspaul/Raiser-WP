<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function rw_bootstrap_pagination( \WP_Query $wp_query = null, $echo = true ) {
    if ( null === $wp_query ) {
        global $wp_query;
    }
    $pages = paginate_links( [
            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'       => '?paged=%#%',
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'total'        => $wp_query->max_num_pages,
            'type'         => 'array',
            'show_all'     => false,
            'end_size'     => 3,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => __( '« Prev' ),
            'next_text'    => __( 'Next »' ),
            'add_args'     => false,
            'add_fragment' => ''
        ]
    );
    if ( is_array( $pages ) ) {
        $pagination = '<div class="pagination d-inline-block"><ul class="pagination">';
        foreach ( $pages as $page ) {
            $pagination .= '<li class="page-item"> ' . str_replace( 'page-numbers', 'page-link', $page ) . '</li>';
        }
        $pagination .= '</ul></div>';
        if ( $echo ) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }
    return null;
}

function rw_config($attribute){
    global $Raiser_WP;
    if($Raiser_WP == null){
        return;
    }
    return $Raiser_WP->config->get($attribute);
}