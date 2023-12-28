<?php
/**
 * Project Event Handler for Composer
 */

namespace MythicDigital\ComposerScripts;

use Composer\Script\Event;

/**
 * Handle Project Events
 */
class ProjectEventHandler {

	/**
	 * Post install event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function postInstall( Event $event ): void {
		PostInstallScript::execute( $event );
	}

	/**
	 * Post create project event.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function postCreateProject( Event $event ): void {
		PostCreateProjectScript::execute( $event );
	}

}
