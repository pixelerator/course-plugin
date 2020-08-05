<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
//\Carbon_Fields\Carbon_Fields::boot();

add_action( 'carbon_fields_register_fields','crb_attach_plugin_options');


     function crb_attach_plugin_options()
    {
        ///edit.php?post_type=azcourse
        ///

        Container::make( 'theme_options', __( 'Course Setting', 'crb' ) )
            ->set_page_parent( 'edit.php?post_type=azcourse' )
            ->add_fields( array(

                Field::make( 'html', 'shortcode' )
                    ->set_html( '<h2 style="font-weight: bold;">Short Code -Course Listing Page</h2><h1 style="background-color:#b1e6cb;padding: 10px;">[AZLISTING]</h1>' ),
                    Field::make( 'select', 'crb_select_field_listing','Select Page for course listing' )
                        ->add_options('my_computation_heavy_getter_function'),

            )

            );
    }

    function my_computation_heavy_getter_function()
    {
        $pages = get_pages();
        $option = array();
        foreach ($pages as $page) {
            $option[$page->ID] = $page->post_title;
        }
        return $option;

    }

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
    require_once( 'vendor/autoload.php' );

    //require_once( ABSPATH . '/vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}