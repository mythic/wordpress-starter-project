<?php
/**
 * Parent class for Composer Scripts
 */

namespace MythicDigital\ComposerScripts;

use Composer\Script\Event;

/**
 * Extendable class for composer scripts
 */
class Script {

	/**
	 * @var ?Event
	 */
	public static ?Event $event = null;

	/**
	 * Set the event object.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	protected static function setEvent( Event $event ) {
		self::$event = $event;
	}

	/**
	 * Get the project folder.
	 *
	 * @return ?string
	 */
	protected static function getProjectFolder(): ?string {
		if ( ! self::$event ) {
			echo 'Missing event object.' . PHP_EOL;
			return null;
		}

		$vendor_dir = self::$event->getComposer()->getConfig()->get( 'vendor-dir' );

		return realpath( $vendor_dir . '/../' );
	}

	/**
	 * Replace a string in a file.
	 *
	 * @param string $search
	 * @param string $replace
	 * @param string $file
	 *
	 * @return void
	 */
	protected static function searchReplaceFile( string $search, string $replace, string $file ): void {
		$path     = self::getProjectFolder() . '/' . $file;
		$contents = file_get_contents( $path );
		$contents = str_replace( $search, $replace, $contents );
		file_put_contents( $path, $contents );
	}

	/**
	 * Output content to the terminal window.
	 *
	 * @param string $content
	 * @param string $type
	 *
	 * @return void
	 */
	protected static function writeOutput( string $content, string $type = '' ): void {
		if ( ! self::$event ) {
			echo 'Missing event object.' . PHP_EOL;
			return;
		}

		if ( ! $type ) {
			self::$event->getIO()->write( $content . PHP_EOL );
			return;
		}

		self::$event->getIO()->write( sprintf( '<%1$s>%2$s</%1$s>' . PHP_EOL, $type, $content ) );
	}

	/**
	 * Output an info message to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeInfo( string $content ): void {
		self::writeOutput( $content, 'info' );
	}

	/**
	 * Output an error to the terminal window.
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected static function writeError( string $content ): void {
		self::writeOutput( $content, 'error' );
	}

	/**
	 * Get a response from user input.
	 *
	 * @param string $content
	 * @param string $default
	 *
	 * @return string
	 */
	protected static function ask( string $question, string $default = '' ): string {
		$default_text = $default ? sprintf( ' [%s]', $default ) : '';
		return self::$event->getIO()->ask( sprintf( '<info>%s</info>%s ', trim( $question ), $default_text ), $default );
	}

	/**
	 * Get confirmation from user input.
	 *
	 * @param string $content
	 * @param string $default
	 *
	 * @return string
	 */
	protected static function confirm( string $question ): string {
		return self::$event->getIO()->askConfirmation( sprintf( '<info>%s</info> [Y/n] ', trim( $question ) ) );
	}
}
