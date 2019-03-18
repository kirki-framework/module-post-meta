<?php
/**
 * Customize_Queried_Post_Info class.
 *
 * @package CustomizeQueriedPostInfo
 */

/**
 * Class Customize_Queried_Post_Info.
 */
class Module {

	/**
	 * The object instance.
	 *
	 * @static
	 * @access private
	 * @since 3.0.0
	 * @var object
	 */
	private static $instance;

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @since 3.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access protected
	 * @since 3.1.0
	 */
	protected function __construct() {
		add_action( 'customize_preview_init', [ $this, 'customize_preview_init' ] );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_control_scripts' ] );
	}

	/**
	 * Enqueue Customizer control scripts.
	 *
	 * @access public
	 * @since 3.1.0
	 */
	public function enqueue_control_scripts() {
		$url = apply_filters(
			'kirki_package_url_module_post_meta',
			trailingslashit( Kirki::$url ) . 'vendor/kirki-framework/module-post-meta/src'
		);
		wp_enqueue_script( 'kirki_post_meta_previewed_controls', $url . '/customize-controls.js', [ 'jquery', 'customize-controls' ], KIRKI_VERSION, true );
	}

	/**
	 * Initialize Customizer preview.
	 *
	 * @access public
	 * @since 3.1.0
	 */
	public function customize_preview_init() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_preview_scripts' ] );
	}

	/**
	 * Enqueue script for Customizer preview.
	 *
	 * @access public
	 * @since 3.1.0
	 */
	public function enqueue_preview_scripts() {
		$url = apply_filters(
			'kirki_package_url_module_post_meta',
			trailingslashit( Kirki::$url ) . 'vendor/kirki-framework/module-post-meta/src'
		);
		wp_enqueue_script( 'kirki_post_meta_previewed_preview', $url . '/customize-preview.js', [ 'jquery', 'customize-preview' ], KIRKI_VERSION, true );

		$wp_scripts   = wp_scripts();
		$queried_post = null;
		if ( is_singular() && get_queried_object() ) {
			$queried_post       = get_queried_object();
			$queried_post->meta = get_post_custom( $queried_post->id );
		}
		$wp_scripts->add_data( 'kirki_post_meta_previewed_preview', 'data', sprintf( 'var _customizePostPreviewedQueriedObject = %s;', wp_json_encode( $queried_post ) ) );
	}
}
