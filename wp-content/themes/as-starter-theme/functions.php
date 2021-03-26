<?php

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Timber not installed. Make sure you run a composer install in the theme directory</p></div>';
    });
    return;
}
else {
    require_once(__DIR__ . '/vendor/autoload.php');
    $timber = new Timber\Timber();
}

// Load Timber from composer


// Create our version of the TimberSite object
class StarterSite extends Timber\Site {

    // This function applies some fundamental WordPress setup, as well as our functions to include custom post types and taxonomies.
    public function __construct() {
        add_action('after_setup_theme', array($this, 'theme_supports'));
        add_filter('timber/context', array($this, 'add_to_context'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'register_menus'));
        add_action('init', array($this, 'add_image_size'));
        /*
         * Configure mail address
         *
         */
        add_filter('wp_mail_from', 'noreply@domain.com');
        add_filter('wp_mail_from_name', 'Administrateur');
        /*
         * Register assets
         *
         */
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        parent::__construct();
    }

    public function theme_supports() {
        add_theme_support('menus');
        /*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
        add_theme_support('post-thumbnails');

        /*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
        add_theme_support( 'title-tag' );

        /*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
        add_theme_support('html5');
    }

    public function register_assets(){
        // Use jQuery from a CDN
        wp_deregister_script('jquery');
        // wp_register_script('jquery', '//code.jquery.com/jquery-2.2.4.min.js', array(), null, true);

        // Enqueue our stylesheet and JS file with a jQuery dependency.
        // Note that we aren't using WordPress' default style.css, and instead enqueueing the file of compiled Sass.
        wp_enqueue_style('my-styles', get_template_directory_uri() . '/dist/main.css', array(), 1.0);
        wp_enqueue_script('my-js', get_template_directory_uri() . '/dist/main.js', '1.0.0', true );
    }

    // Abstracting long chunks of code.

    // The following included files only need to contain the arguments and register_whatever functions. They are applied to WordPress in these functions that are hooked to init above.

    // The point of having separate files is solely to save space in this file. Think of them as a standard PHP include or require.

    public function register_post_types(){
        require('lib/custom-post-types.php');
    }

    public function register_taxonomies(){
        require('lib/taxonomies.php');
    }

    public function register_menus(){
        require('lib/menus.php');
    }

    public function add_image_size(){
        add_image_size('post_home_thumbnail', 400, 300, true );
    }

    // Access data site-wide.

    // This function adds data to the global context of your site. In less-jargon-y terms, any values in this function are available on any view of your website. Anything that occurs on every page should be added here.

    public function add_to_context($context) {

        // Our menu occurs on every page, so we add it to the global context.
        $context['menu'] = array(
            'main' => new Timber\Menu('main'),
            'footer' => new Timber\Menu('footer')
        );

        $context['post_type'] = get_post_type();

        // This 'site' context below allows you to access main site information like the site title or description.
        $context['site'] = $this;
        return $context;
    }
}

new StarterSite();
