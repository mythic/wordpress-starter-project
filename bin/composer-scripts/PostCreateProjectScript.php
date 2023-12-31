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
	 * @var array
	 */
	private static array $info = [];

	/**
	 * @var string
	 */
	private static string $default_slug = 'wordpress-starter-project';

	/**
	 * Perform actions within this file.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function execute( Event $event ): void {
		self::setEvent( $event );

		if ( ! self::needsSetup() ) {
			return;
		}

		// Gather project info.
		self::getProjectInfo();

		// Perform project string replacements
		self::updateProjectFiles();

		// Modify the description in the composer.json file.
		self::updateComposerDescription();
	}

	/**
	 * Check to see if we should run setup.
	 *
	 * @return bool
	 */
	public static function needsSetup(): bool {
		$package = self::$event->getComposer()->getPackage()->getName();

		if ( ! str_contains( $package, self::$default_slug ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Gather project info.
	 *
	 * @param Event $event
	 *
	 * @return void
	 */
	public static function getProjectInfo(): void {
		$name = ! empty( self::$info['name'] ) ? self::$info['name'] : 'My Project';

		self::$info['name'] = self::ask( 'What is the name of your project?', $name );
		self::$info['slug'] = self::slugify( self::$info['name'] );

		self::$info['slug'] = self::ask( 'Do you want to use a custom project slug?', self::$info['slug'] );

		$summary  = PHP_EOL . ' - Name: ' . self::$info['name'];
		$summary .= PHP_EOL . ' - Slug: ' . self::$info['slug'];

		self::writeOutput( '<info>Summary:</info>' . $summary );

		if ( ! self::confirm( 'Does everything look good?' ) ) {
			self::getProjectInfo();
		}
	}

	/**
	 * Slugify some text.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	private static function slugify( string $text ): string {
		$separator = '-';

		// replace non letter or digits by separator
		$text = preg_replace( '~[^\pL\d]+~u', $separator, $text );

		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );

		// trim
		$text = trim( $text, $separator );

		// remove duplicate separator
		$text = preg_replace( '~-+~', $separator, $text );

		// lowercase
		$text = strtolower( $text );

		if ( empty( $text ) ) {
			return '';
		}

		return $text;
	}

	/**
	 * Change wordpress-starter-project to match new project
	 *
	 * @return void
	 */
	public static function updateProjectFiles(): void {
		if ( empty( self::$info['slug'] ) ) {
			self::writeError( 'Missing project slug.' );
			return;
		}

		$files = [
			'.lando.yml',
			'composer.json',
			'phpcs.xml',
		];

		$search  = self::$default_slug;
		$replace = self::$info['slug'];

		foreach ( $files as $file ) {
			self::searchReplaceFile( $search, $replace, $file );
		}

		self::writeInfo( 'All set!' );
	}

	/**
	 * Modify the composer.json project description
	 *
	 * @return void
	 */
	public static function updateComposerDescription() {
		if ( empty( self::$info['name'] ) ) {
			self::writeError( 'Missing project name.' );
			return;
		}

		$search  = self::$event->getComposer()->getPackage()->getDescription();
		$replace = sprintf( 'A custom WordPress project for %s by Mythic Digital', self::$info['name'] );

		self::searchReplaceFile( $search, $replace, 'composer.json' );
	}
}
