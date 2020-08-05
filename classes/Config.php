<?php
class Config
{


    protected static $instance = NULL;

    public static function get_instance()
    {

        NULL === self::$instance and self::$instance = new self;

        return self::$instance;
    }

    public function __construct()
    {
        $page_associate =  get_option("_crb_select_field_listing");
        if(empty($page_associate)){
            $my_post = array(
                'post_title'    => 'Course Listing',
                'post_content'  => '[AZLISTING]',
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type'     => 'page'
            );

            // Insert the post into the database
            $page_id = wp_insert_post( $my_post );
            add_option("_crb_select_field_listing",$page_id);

        }

        add_shortcode( 'AZLISTING', array($this,'course_func_sr') );

        add_filter('the_content', array($this,'short_code_change'));

    }

    function course_func_sr( $atts ){
        $args = array(
        'post_type'      => 'azcourse',
                    'posts_per_page' => '10',
                    'publish_status' => 'published',
                 );

    $query = new WP_Query($args);

    if($query->have_posts()) :

        while($query->have_posts()) :

            $query->the_post() ;
            $a = "https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=3289&q=80";
            if(!empty(get_the_post_thumbnail_url())){
                $a = get_the_post_thumbnail_url();
            }
            $content = get_the_content();

            $length = 100;

            if(strlen($content) > $length) {
                $content = substr($content, 0, $length);
            }
            $result .= '<div class="course-item" style="width:100%;min-height:150px;">';
            $result.= '<div style="float: left;width:20%;"><img src=" ' . $a . '" style="    "/></div>';
            $result.= '<div style="float: left;width:75%;padding-left:5px;">';
                        $result.= '<h5 class="course-entry ti"><a href="'.get_permalink().'">' . get_the_title() . '</a></h5>';
                        $result.='<div style="">'.$content.' <a target="_blank" style="font-size:14px;text-decoration:none;color:red;" href="'.get_permalink().'">Know More</a> </div>';
            $result.= '</div>';
            $result .= '</div><div style="clear:both;"></div>';

        endwhile;

        wp_reset_postdata();

    endif;

    return $result;


    }
    function short_code_change($content) {
        global $post;

        if ($post->post_type == 'azcourse') {
            $content = "<div style='width:100%;'>";
            $a = "https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=3289&q=80";
            if(!empty(get_the_post_thumbnail_url())){
                $a = get_the_post_thumbnail_url();
            }
            $content = $content."<div><img src='".$a."'/></div>";
            $content = $content."<div>".get_the_content()."</div>";

            $content = $content ."<table border='0' id='table_test'>";

            $venues = get_post_meta($post->ID);
           // return json_encode($venues);

            foreach ($venues as $k=>$ven) {
                if (strpos($k, "ven_") !== false) {
                    //echo "<pre>";print_r(array(unserialize($ven[0]),$k));die;
                    $temp_values = @unserialize($ven[0]);
                    //$temp_values = (object) $temp_values;
                    //echo "<pre>";print_r($temp_values)  ;
                    $start_date = $temp_values["'start_date'"];
                    $end_date = $temp_values["'end_date'"];
                    $ven_data = $temp_values["'venue'"];
                    $fees = $temp_values["'fees'"];
                    $t_status = 0;
                    $content = $content."<tr>";
                    $content = $content."<td>".get_term_by('id', $ven_data, 'venues')->name."</td>";
                    $content = $content."<td>".$start_date."</td>";
                    $content = $content."<td>".$end_date."</td>";
                    $content = $content."<td>".$fees." AED</td>";
                    $content = $content."<td><a href='#'>Book Now</a></td>";



                    $content = $content."</tr>";

                }
            }
            $content = $content ."</table>";
            $content = $content ."</div>";
        }
        return $content;
    }

}
?>