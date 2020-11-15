<?php
/**
 * The admin-specific functionality of the plugin
 * 
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for registering a new settings page under Settings.
 * 
 * @since   1.0
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/admin
 * @author  Myanmar Web Designer (MWD) Co., Ltd. 
 */
class Myanmar_Exchange_Rates_Admin
{
   /**
    * The ID of the plugin.
    * 
    * @since 1.0
    * @access private
    * @var  string $plugin_name The ID of this plugin.
    */
   private $plugin_name;

   /**
    * The version of the plugin
    * 
    * @since   1.0
    * @access  private
    * @var string $version The current version of this plugin.
    */
   private $version;
 
   /**
    * Initialize the class and set its properties
    * 
    * @since  1.0
    * @param  string $plugin_name  The name of this plugin.
    * @param  string   $version The version of this plugin.
    */
   public function __construct( $plugin_name, $version )
   {
      $this->plugin_name = $plugin_name;
      $this->version = $version;
   }
 
   /**
    * Registers a new settings page under Settings.
    */
   function mwd_mcer_option_page() {
      // Add plugin option page
      $hookname = add_options_page(
         __('Myanmar Currency Exchange Rates Options', 'myanmar-exchange-rates'),
         __('Myanmar Exchange Rates', 'myanmar-exchange-rates'),
         'manage_options',
         'mwd_mcer',
         [$this, 'mwd_mcer_options_page_html']  
      );
   }
 
   /**
    * Settings page display callback.
    * 
    * @since  1.0
    */
   function mwd_mcer_options_page_html()
   {
      // check user capabilities
      if ( ! current_user_can( 'manage_options' ) ) {
         return;
      }

      // add error/update messages
      // check if the user have submitted the settings
      // Wordpress will add the 'settings-updated' $_GET parameter to the url
      if ( isset( $_GET['settings-updated'] ) ) {
         // add settings saved message with the class of `updated`
         add_settings_error( 'mwd_mcer_messages', 'mwd_mcer_message', __( 'Settings Saved', 'myanmar-exchange-rates' ), 'updated' );
      }
      ?>

      <div class="wrap">
         <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
         <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "mwd_mcer_options"
            settings_fields( 'mwd_mcer' );
            // output setting sections and their fields
            // (sections are registered for "mwd_mcer", each field is registered to a specific section)
            do_settings_sections( 'mwd_mcer' );
            // output save settings button
            submit_button( __( 'Save Settings', 'myanmar-exchange-rates' ) );
            ?>
         </form>
      </div>
   
   <?php
   }

   /**
    * Custom setting and options
    * 
    * @since   1.0
    */
   public function mwd_mcer_settings_init()
   {
      // register a new setting for 'mwd_mcer' page.
      register_setting( 'mwd_mcer', 'mwd_mcer_options' );

      // Register a new section in the 'mwd_mcer' page.
      add_settings_section(
         'mwd_mcer_section_choose_currency',
         __( 'Select the currency to show', 'myanmar-exchange-rates'),
         array( $this, 'mwd_mcer_section_choose_currency_callback' ),
         'mwd_mcer'
      );

      // Add new field to the section of 'mwd_mcer' page.
      add_settings_field(
         'mwd_mcer_field_currencies',
         esc_html( 'Select Currencies', 'myanmar-exchange-rates' ),
         [ $this, 'mwd_mcer_field_currencies_callback' ],
         'mwd_mcer',
         'mwd_mcer_section_choose_currency',
         array(
            'label_for' => 'mwd_mcer_field_currencies',
            'type'      => 'checkbox',
            'option_group' => '',
            'class'  => 'mwd-mcer-field-currencies',
         ),
      );
   }

   /**
    * Currencies field callback of `mwd_mcer` page 
    *
    * @since   1.0
    */
   public function mwd_mcer_field_currencies_callback( $args )
   {
      // Get the value of the setting we've registered with register_setting()
      $options = get_option( 'mwd_mcer_options' );
      ?>

      <select name="mwd_mcer_options[<?php echo esc_attr( $args['label_for'] ); ?>][]"
       id="<?php echo esc_attr( $args['label_for'] ); ?>" multiple>

        <?php foreach ( MWD_MCER()->get_currencies() as $curr ) : ?>

            <option
               value="<?php esc_attr_e( $curr ); ?>"
               <?php echo ( in_array( $curr, $options[ $args['label_for'] ], TRUE ) ) ? ' selected ' : ''; ?>
            ><?php esc_html_e( $curr, 'myanmar-currency-rates' ) ?></option>

         <?php endforeach; ?>

      </select>
      
      <?php
   }

   /**
    * Setting section callback of 'mwd_mcer' page
    *
    * @since   1.0
    */
   public function mwd_mcer_section_choose_currency_callback( $arg )
   {
      echo '';
   }

   /**
    * Register the stylesheets for the admin area.
    *
    * @since   1.0
    */
   public function enqueue_styles()
   {
      wp_enqueue_style( 'mwd-mcer-admin-styles', plugin_dir_url( __FILE__ ) . 'css/mwd-mcer-admin.css', array(), $this->version );
   }

   /**
    * Register the JavaScript for the admin area.
    *
    * @since   1.0
    */
   public function enqueue_scripts()
   {
      wp_enqueue_script( 'mwd-mcer-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/mwd-mcer-admin.js', array( 'jquery' ), $this->version, true );
   }
}