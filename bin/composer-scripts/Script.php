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
	 * Get the project folder.
	 *
	 * @param Event $event
	 *
	 * @return string
	 */
	protected static function getProjectFolder( Event $event ): string {
		$vendorDir = $event->getComposer()->getConfig()->get( 'vendor-dir' );
		return realpath($vendorDir . '/../');
	}

}
