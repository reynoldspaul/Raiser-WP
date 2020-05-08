=== Raiser WP ===
Tags:              raiser-wp, metaboxes, blocks, fields, options, settings, theme, framework
Requires at least: 3.8.0
Requires PHP:      7.0
Tested up to:      5.4.1
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Raiser WP is a framework to assist with developing custom WordPress themes.

== Description ==

Raiser WP aims to make developing custom WordPress themes an enjoyable experience. Raiser WP gives the developer a simple set of tools and structure. Written with OOP patterns and encapsulated logic, without losing flexibility or extensibility.

Raiser WP was born out of the frustration of creating custom WordPress themes. Seemingly simple requirements were hard to create. Large functions.php files. Spaghetti code, hooking into hooks inside hooks.

Gaining inspiration from the [https://laravel.com/](Laravel) framework and the WordPress plugin [https://en-gb.wordpress.org/plugins/cmb2/](CMB2), Raiser WP was created to abstract away some of the WordPress stuff, and offer clean ways of building custom themes without worrying about the overhead of 'the WordPress way'.

### Features:

Raiser WP's core features include:

* Array based theme config files - no more Googling for WordPress snippets to turn on/off WP features.
* Block based custom field functionality - manage custom fields within the source code and not the database.
* Block support the highly recommended [https://en-gb.wordpress.org/plugins/cmb2/](CMB2) custom field plugin, and [https://wordpress.org/plugins/advanced-custom-fields/](ACF) plugin.
* Laravel inspired content modeling, to define custom post types and taxonomies.
* Eager loading functionality, making custom block data available within the $post object inside theme template files.
* CLI for easy creation of elements, eg custom post types: ~/ php raiser make:cpt Projects.

== Documentation ==

## Configuration

Raiser WP uses array based configuration files, located within your-theme-folder/config. No more large sections of script just to define some basic aspects to your theme.

Init the admin.php and theme.php config files within your theme, by running the following command from inside the wp-content/plugins/raiser-wp directory.
```
php raiser init:config
```

**your-theme-folder/config/admin.php**
```
return [

    /*
    |--------------------------------------------------------------------------
    | WordPress Admin Bar
    |--------------------------------------------------------------------------
    |
    | Show/hide the WordPress admin bar
    | https://developer.wordpress.org/reference/hooks/show_admin_bar/
    |
    */  
    'show_admin_bar' => false,

    /*
    |--------------------------------------------------------------------------
    | WordPress Admin Dashboard
    |--------------------------------------------------------------------------
    |
    | Clean up the dashboard
    | https://developer.wordpress.org/reference/hooks/welcome_panel/
    | https://developer.wordpress.org/reference/hooks/admin_init/
    |
    */  
    'dashboard' => [
        'show_welcome_panel' => false,
        'clean_dashboard' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress Gutenberg Editor
    |--------------------------------------------------------------------------
    |
    | Use/don't use the editor
    | https://developer.wordpress.org/reference/hooks/use_block_editor_for_post/
    |
    */  
    'gutenberg_editor' => true,

    /*
    |--------------------------------------------------------------------------
    | WordPress Theme Support
    |--------------------------------------------------------------------------
    |
    | Set theme support
    | https://developer.wordpress.org/reference/functions/add_theme_support/
    |
    */  
    'theme_support' => [
        'menus',
        'post-thumbnails',
    ],

];
```

**your-theme-folder/config/theme.php**
```
return [

    /*
    |--------------------------------------------------------------------------
    | Theme Name
    |--------------------------------------------------------------------------
    |
    | Define your theme name
    |
    */  
    'theme_name' => 'my-custom-theme',

    /*
    |--------------------------------------------------------------------------
    | Clean up the html header
    |--------------------------------------------------------------------------
    |
    | Remove code that you probably don't need
    |
    */  
    'wp_head' => [
        'remove_wp_emoji'   => true,
        'remove_rest_api_header' => true,
        'remove_weblog_link' => true,
        'remove_wlwmanifest_link' => true,
        'remove_wp_generator'   => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permalink Structure
    |--------------------------------------------------------------------------
    |
    | https://wordpress.org/support/article/settings-permalinks-screen/
    |
    */      
    'permalink_structure' => '/%postname%/',


    /*
    |--------------------------------------------------------------------------
    | Include stylsheets
    |--------------------------------------------------------------------------
    |
    | https://developer.wordpress.org/reference/functions/wp_enqueue_style/
    |
    */  
    'stylesheets' => [
        // 'fa' => [
        //  'src' => get_template_directory_uri().'/dist/fonts/fa/all.min.css',
        //  'ver' => '1.0',
        // ],       
        'main' => [
            'src' => get_template_directory_uri().'/dist/css/app.css',
            'ver' => '1.0',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Include scripts
    |--------------------------------------------------------------------------
    |
    | https://developer.wordpress.org/reference/functions/wp_enqueue_script/
    | NB use '[name]-async-defer' to async and defer the script
    |
    */  
    'scripts' => [
        'main' => [
            'src' => get_template_directory_uri().'/dist/js/app.js',
            'ver' => '1.0',
            'localize' => [
                'theme' => [
                    'ajaxurl' => admin_url('admin-ajax.php')
                ]
            ]
        ],      
        // 'maps-async-defer' => [
        //  'src' => 'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY.'&callback=initMap',
        //  'in_footer' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Register Theme Menus
    |--------------------------------------------------------------------------
    |
    | https://developer.wordpress.org/reference/functions/register_nav_menus/
    |
    */  
    'menus' => [
        'main' => 'Main Menu',
        'footer' => 'Footer Menu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Login Screen Custom Logo
    |--------------------------------------------------------------------------
    |
    */  
    // 'custom_login_image' => [
    //  'src' => get_template_directory_uri().'/dist/images/logo_black.png',
    //  'background-color' => '#FFF',
    //  'size' => '200px auto'
    // ],

    /*
    |--------------------------------------------------------------------------
    | Custom Editor Colours
    |--------------------------------------------------------------------------
    |
    */  
    // 'editor_custom_colors' => [
    //  'body-color' => '#2B2755',
    // ],

];
```

You can add your own config files inside your-theme/config/config-file-name.php

Config settings are accessible via the rw_config() helper function:
```
rw_config('admin.show_admin_bar');
rw_config('config-file-name.show_admin_bar');
```

### Custom Post Types

Custom post types can be registered inside the /raiser-wp/cpts folder within your theme.

```
$ php raiser make:cpt cpt_name
```

This command will create a file called cpt_name.php inside /raiser-wp/cpts folder. It will also create 2 template files for this custom post type - single-post_type.php and archive-post_type.php

Alternatively, you can create the files yourself. The basic structure is:

```
class Product_CPT extends Raiser_CPT_Base {

    public $post_type_name = 'product';

    public function post_type_setup(){

        $this->labels = [
            'name'                  => 'Product',
        ];

        $this->rewrite = [
            'slug'                  => 'product',
        ];

        $this->args = [
            'label'                 => 'product',
            'description'           => 'A product',
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
        ];       

    }   
}
new Product_CPT;
```

### Custom Taxonomies

Custom Taxonomies are registered inside the /raiser-wp/tax folder within your theme.
```
$ php raiser make:tax tax_name
```

This command will create a file called tax_name.php inside /raiser-wp/tax folder. The basic structure is:
```
class Product_Type_Tax extends Raiser_Taxonomy_Base {

    public $tax_name = 'product-type';

    public $object_types = ['product'];

    public function tax_setup(){

        $this->labels = [
            'name'                       => 'Product Type',
        ];

        $this->rewrite = false;

        $this->args = [
            'hierarchical'               => true,
        ];           
    }   

}
new Product_Type_Tax;
```

### Custom Field Blocks

Raiser WP uses the concept of blocks to encapsulate custom fields. Blocks can be defined in the PHP, and assigned to posts/pages via assignment in Raiser_CPT_Base.

Block classes can be either of type [https://en-gb.wordpress.org/plugins/cmb2/](CMB2), or [https://wordpress.org/plugins/advanced-custom-fields/](ACF).

# Blocks

Blocks are created within your-theme-folder/raiser-wp/blocks
```
$ php raiser make:block block_name --type=cmb2
```
or
```
$ php raiser make:block block_name --type=acf
```

Basic block structure:
```
class Team_Block extends Raiser_CMB2_Block {

    public $block_name = 'team';

    public function init(){

        $this->box = [
            'title'         => 'Team',
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ];

        $this->fields = [
            [
                'name' => 'Team Name',
                'id'   => 'team_name',
                'type' => 'text',
            ],  
            /// etc
        ]

    }
}
```

Blocks can be assigned to posts, pages, taxonomies by setting a public $block array on the Raiser_CPT_Base class:
```
class Product extends Raiser_CPT_Base {

    public $post_type_name = 'product';

    // attach blocks
    public $blocks = [
        'team'      => team_block::class,
        'q-and-a'   => q_and_a_block::class,
    ];
    
    //

}    
```
# Eager Loading Blocks

Blocks can be eager loaded into the $post variable by setting a public $with array. The $with values must match the keys used in the $block array declaration:
```
class Product extends Raiser_CPT_Base {

    public $post_type_name = 'product';

    // attach blocks
    public $blocks = [
        'team'      => team_block::class,
        'q-and-a'   => q_and_a_block::class,
    ];
    
    // eager load
    public $with = [
        'terms'     => ['product-type'],
        'blocks'    => ['team', 'q-and-a'],
    ];

    //

}    
```

Then on a single-post page, the following will return saved data for the block
```
print_r( $post->team );
print_r( $post->{q-and-q} );
```

# Theme Options Page

Add a theme options page within your-theme-folder/raiser-wp/site-options.php by assigning a block class to an option:
```
new team_block([
    'id'           => 'rw_contact_options_page',
    'object_types' => array('options-page'),
    'option_key'   => 'rw_team_options',
    //'parent_slug'  => 'rw_main_options',
]);
```

Access option data within your theme:
```
echo cmb2_get_option( 'rw_team_options', '{field_id}' );
```

# Load More

Turn on load more feature by adding following to config/theme.php

```
/*
|--------------------------------------------------------------------------
| Raiser-WP features
|--------------------------------------------------------------------------
|
| https://developer.wordpress.org/reference/functions/register_nav_menus/
|
*/  
'raiser-wp' => [
    'load-more' => true,
],
```

Define a cpt template inside the cpt class file
```
public $load_more_card_template = 'parts/post-card';
```

Add the load more button to the archive page
```
<?php Raiser_Load_More::load_more_button(['class'=>'archive']); ?>
```

== Changelog ==

* 1.0.0
Initial version