<?php
/**
 * Perform some post-install actions with Composer.
 */

namespace MythicDigital\ComposerScripts;

use Composer\Script\Event;

/**
 * Post Install Composer Script
 */
class PostInstallScript extends Script {

	/**
	 * Perform the actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		echo 'Running composer "post-install-cmd" scripts' . PHP_EOL;

		self::landoConfig( $event );
		self::setKeySalts( $event );
	}

	/**
	 * Add Lando include to wp-config.php
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function landoConfig( Event $event ): void {
		$app_dir        = parent::getProjectFolder( $event );
		$wp_config_path = $app_dir . '/wp-config.php';

		if ( ! file_exists( $wp_config_path ) ) {
			return;
		}

		$lando_config = 'wp-config.lando.php';

		if ( ! file_exists( $app_dir . '/' . $lando_config ) ) {
			return;
		}

		$wp_config = file_get_contents( $wp_config_path );

		if ( str_contains( $wp_config, $lando_config ) ) {
			// Config file already updated.
			return;
		}

		$marker = '// ** Database settings';

		if ( ! str_contains( $wp_config, $marker ) ) {
			// Config file missing marker.
			return;
		}

		$insert = sprintf(
			'if ( file_exists( \'%1$s\' ) ) {
	require \'%1$s\';
} else {',
			$lando_config
		);

		$lines  = file( $wp_config_path );
		$update = '';
		$indent = false;

		foreach ( $lines as $line ) {
			if ( str_contains( $line, $marker ) ) {
				$update .= $insert . PHP_EOL;
				$indent  = true;
			}

			if ( $indent ) {
				$update .= "\t";
			}

			$update .= $line;

			if ( str_contains( $line, 'define( \'DB_HOST\'' ) ) {
				$update .= '}' . PHP_EOL;
				$indent  = false;
			}
		}

		file_put_contents( $wp_config_path, $update );
	}

	/**
	 * Update keys & salts.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function setKeySalts( Event $event ): void {
		$app_dir        = parent::getProjectFolder( $event );
		$wp_config_path = $app_dir . '/wp-config.php';

		if ( ! file_exists( $wp_config_path ) ) {
			return;
		}

		$salts = file_get_contents( 'https://api.wordpress.org/secret-key/1.1/salt/' );

		if ( ! $salts ) {
			return;
		}

		// Clean up salts.
		$salts = str_replace( '(\'', '( \'', $salts );
		$salts = str_replace( '\')', '\' )', $salts );

		$wp_config = file_get_contents( $wp_config_path );

		if ( ! str_contains( $wp_config, 'put your unique phrase here' ) ) {
			// Config file already updated.
			return;
		}

		$start = 'define( \'AUTH_KEY\'';
		$end   = 'define( \'NONCE_SALT\'';

		if ( ! str_contains( $wp_config, $start ) || ! str_contains( $wp_config, $end ) ) {
			// Config file missing markers.
			return;
		}

		$lines  = file( $wp_config_path );
		$update = '';
		$remove = false;

		foreach ( $lines as $line ) {
			if ( str_contains( $line, $start ) ) {
				$update .= $salts . PHP_EOL;
				$remove  = true;
			} elseif ( str_contains( $line, $end ) ) {
				$remove = false;
			} elseif ( ! $remove ) {
				$update .= $line;
			}
		}

		file_put_contents( $wp_config_path, $update );
	}
}
