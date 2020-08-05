<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;



class PluginOption
{

    public function __construct()
    {
        require_once( 'vendor/autoload.php' );
        \Carbon_Fields\Carbon_Fields::boot();
        add_action( 'carbon_fields_register_fields', array( $this, 'crb_attach_theme_options') );
    }

    public function crb_attach_theme_options()
    {
        Container::make( 'theme_options', __( 'Plugin Option', 'crb' ) )
            ->add_fields( array(
                Field::make( 'text', 'crb_text', 'Text Field' ),
            ) );
    }

}

$wpTest = new PluginOption();