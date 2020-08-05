<?php
class Course
{


    protected static $instance = NULL;

    public static function get_instance()
    {

        NULL === self::$instance and self::$instance = new self;

        return self::$instance;
    }

    public function __construct()
    {
        add_action( 'init', array($this, 'courses_type'));
        add_action( 'add_meta_boxes', array($this,'register_course_meta') );
        add_action('admin_enqueue_scripts', array($this,'enqueue_date_picker'));
        add_action('admin_menu', array($this,'remove_boxes_taxanomy'), 20);
        add_action('save_post',array($this,"save_course_meta"));
        add_theme_support( 'post-thumbnails' );
        add_action('wp_enqueue_scripts', array($this,'theme_styles'));



    }

    function theme_styles()
    {



            wp_enqueue_style( 'custom_table', plugins_url('table.css',dirname(__FILE__) ));


    }
    function enqueue_date_picker(){
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_register_style('course-namespace', plugins_url('style.css',dirname(__FILE__) ));
        wp_enqueue_style('course-namespace');
        wp_enqueue_script( 'course-js-file', plugins_url('js/admin.js',dirname(__FILE__) ),array( 'jquery' ));



    }

    function courses_type() {
        $labels = array(
            'name'               => _x( 'Courses', 'post type general name' ),
            'singular_name'      => _x( 'Course', 'post type singular name' ),
            'add_new'            => _x( 'Add New Course', 'course' ),
            'add_new_item'       => __( 'Add New Course' ),
            'edit_item'          => __( 'Edit Course' ),
            'new_item'           => __( 'New Course' ),
            'all_items'          => __( 'All Course' ),
            'view_item'          => __( 'View Courses' ),
            'search_items'       => __( 'Search Courses' ),
            'not_found'          => __( 'No courses found' ),
            'not_found_in_trash' => __( 'No courses found in the Trash' ),
            'menu_name'          => 'Courses'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Course Information',
            'public'        => true,
            'menu_position' => 5,
            'menu_icon'     => esc_url( plugins_url('img/course.png',dirname(__FILE__) ) ),
            'supports'      => array( 'title', 'editor', 'thumbnail' ),
            'has_archive'   => true,
        );
        register_post_type( 'azcourse', $args );
        unset($labels);
        unset($args);

        $labels = array(
            'name' => 'Venues',
            'singular_name' => 'Venue',
            'search_items' => 'Search Venue',
            'all_items' => 'All Venues',
            'parent_item' => 'Parent Venue',
            'parent_item_colon' => 'Parent Venue :',
            'edit_item' => 'Edit Venue ',
            'update_item' => 'Update Venue ',
            'add_new_item' => 'Add New Venue ',
            'new_item_name' => 'New Venue ',
        );

        $args = array(
            'hierarchical' => true,
            'rewrite' => array('hierarchical' => true, 'slug' => 'venues', 'with_front' => true),
            'show_in_nav_menus' => true,
            'labels' => $labels,
            'show-admin-column' => true,
        );
        register_taxonomy('venues', 'azcourse', $args);

        unset($labels);
        unset($args);
    }

    function register_course_meta(){
        add_meta_box(
            'course-meta',
            __( 'Course Info', 'coursemeta' ),
            array($this, 'course_meta'),
            'azcourse','normal',"high"
        );
    }


    function remove_boxes_taxanomy() {
        remove_meta_box('venuesdiv', 'azcourse', 'side');
    }


    function course_meta($post,$args){
        wp_nonce_field( 'intel_cc_metabox_nonce', 'intel_cc_metabox_nonce_data' );
        $terms = get_terms([
            'taxonomy' => "venues",
            'hide_empty' => false,
        ]);
        //echo "<pre>"; print_r($terms);
        $venues = get_post_meta($post->ID);

        $mark_up = '<table style="width:100%;" id="course_table">
        <tr style="background-color:#a7dda7;font-weight:bold;font-size: 14px;;">
        <td> Start Date</td>
        <td> End Date</td>
        <td>Venue</td>
        <td> Fee</td>
        <td>Action</td>
        </tr>';
        //plugins_url('img/course.png',dirname(__FILE__) )
        $add = 'img/add.png';
        $mark_up = $mark_up.'<tr id="add_slot" style="background-color:#d0c6c6;"><td colspan="5" style="text-align: center;"><a href="javascript:void(0)" class="add_venue"><img style="width:40px;padding: 10px;" src="'.esc_url( plugins_url($add,dirname(__FILE__))).'"/></a> </td></tr>';
        //Template Start
        $mark_up = $mark_up . '<tr class="course_template cssglobal">';
        $mark_up = $mark_up.'<td><input type="text" class="str start_date"></td>';
        $mark_up = $mark_up.'<td><input type="text" class="end end_date"></td>';
        $mark_up = $mark_up.'<td><select class="all_venues">';
        $option = "";
        $option_array = array();
        foreach ($terms as $t){
            $option_array[$t->term_taxonomy_id] = $t->name;
            $option = $option.'<option value="'.$t->term_taxonomy_id.'">';
            $option = $option.$t->name;
            $option  = $option.'</option>';
        }
        $mark_up = $mark_up.$option;
        $mark_up = $mark_up.'</select></td>';
        $mark_up = $mark_up.'<td><input  type="text" class="fees"><input type="hidden" class="t_status" value="0"  ></td>';

        $mark_up = $mark_up.'<td><a href="javascript:void(0)" class="to_delete" data-id=""><img style="width:30px;" src="'.esc_url( plugins_url( 'img/delete.png', dirname(__FILE__) ) ).'"/> </a></td>';



        $mark_up = $mark_up.'</tr>';
        //Template End

        foreach ($venues as $k=>$ven){

            if(strpos($k, "ven_") !== false){
                //echo "<pre>";print_r(array(unserialize($ven[0]),$k));die;
                $temp_values = @unserialize($ven[0]);
                //$temp_values = (object) $temp_values;
                //echo "<pre>";print_r($temp_values)  ;
                $start_date = $temp_values["'start_date'"];
                $end_date = $temp_values["'end_date'"];
                $ven_data = $temp_values["'venue'"];
                $fees = $temp_values["'fees'"];
                $t_status = 0;



                $mark_up = $mark_up . '<tr class="cssglobal '.$k.'" style="display:table-row;">';
                $mark_up = $mark_up.'<td><input type="text" class="str start_date" value="'.$start_date.'" name="'.$k.'[\'start_date\']"></td>';
                $mark_up = $mark_up.'<td><input type="text" class="end end_date" value="'.$end_date.'" name="'.$k.'[\'end_date\']"></td>';
                $mark_up = $mark_up.'<td><select class="all_venues" name="'.$k.'[\'venue\']">';
                foreach ($option_array as $ok=>$ovalue){


                    if($ven_data==$ok){
                        $mark_up = $mark_up.'<option value="'.$ok.'" selected>';
                    }else{
                        $mark_up = $mark_up.'<option value="'.$ok.'">';
                    }
                    $mark_up = $mark_up.$ovalue;
                    $mark_up  = $mark_up.'</option>';
                }
                $mark_up = $mark_up . '</select></td>';
                $mark_up = $mark_up.'<td><input  type="text" class="fees" value="'.$fees.'" name="'.$k.'[\'fees\']"><input type="hidden" class="t_status" value="'.$t_status.'" name="'.$k.'[\'t_status\']" ></td>';

                $mark_up = $mark_up.'<td><a href="javascript:void(0)" class="to_delete" data-id="'.$k.'"><img style="width:30px;" src="'.esc_url( plugins_url( 'img/delete.png', dirname(__FILE__) ) ).'"/> </a></td>';


            }
        }
        $mark_up = $mark_up.'</table>';
        echo $mark_up;
    }
        function save_course_meta($post_id){
            if ( ! isset( $_POST['intel_cc_metabox_nonce_data'] )
                || ! wp_verify_nonce( $_POST['intel_cc_metabox_nonce_data'], 'intel_cc_metabox_nonce' )
            ) {
                return;
            }

            if ( !current_user_can( 'edit_post', $post_id ))
                return;
           // echo "<pre>";


            foreach ($_POST as $k=>$v){
                if(strpos($k, "ven_") !== false){


                        if(isset($v["'start_date'"]) && isset($v["'end_date'"]) && isset($v["'venue'"]) && isset($v["'fees'"]) && isset($v["'t_status'"])){


                        if($v["'t_status'"]==0){
                            if (metadata_exists('post', $post_id,$k)) {
                                //echo "<pre>";print_r(array($post_id,$k,$v)); die;

                                update_post_meta($post_id, $k, $v);


                            } else {
                                add_post_meta($post_id, $k, $v, true);
                            }
                        }else{
                            delete_post_meta($post_id, $k);

                        }


                    }


                }
            }
            return;

        }

}
?>