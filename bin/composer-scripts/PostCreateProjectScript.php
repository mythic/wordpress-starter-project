<?php
/**
 * Perform some post-create-project actions with Composer.
 */

namespace MythicDigital\ComposerScripts;

use Composer\Script\Event;

/**
 * Post Create Project Composer Script
 */
class PostCreateProjectScript extends Script {

	/**
	 * Perform actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		echo 'Running composer "post-create-project-cmd" scripts' . PHP_EOL;

		self::updateProjectSlug( $event );
	}

	/**
	 * Change wordpress-starter-project to match new project
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function updateProjectSlug( Event $event ): void {

	}
}
