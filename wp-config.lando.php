<?php

/** This will ensure these are only loaded on Lando */
if (getenv('LANDO_INFO')) {
	/**  Parse the LANDO INFO  */
	$lando_info = json_decode(getenv('LANDO_INFO'));

	/** Get the database config */
	$database = $lando_info->database;
	/** The name of the database for WordPress */
	define('DB_NAME', $database->creds->database);
	/** MySQL database username */
	define('DB_USER', $database->creds->user);
	/** MySQL database password */
	define('DB_PASSWORD', $database->creds->password);
	/** MySQL hostname */
	define('DB_HOST', $database->internal_connection->host);

	/** URL routing (Optional, may not be necessary) */
	// define('WP_HOME','https://wordpress-starter-project.lndo.site');
	// define('WP_SITEURL','https://wordpress-starter-project.lndo.site');

	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'SCRIPT_DEBUG', true );
} elseif (getenv('LANDO')) {
	/** The name of the database for WordPress */
	define('DB_NAME', 'wordpress');
	/** MySQL database username */
	define('DB_USER', 'wordpress');
	/** MySQL database password */
	define('DB_PASSWORD', 'wordpress');
	/** MySQL hostname */
	define('DB_HOST', 'database');

	/** URL routing (Optional, may not be necessary) */
	// define('WP_HOME','https://wordpress-starter-project.lndo.site');
	// define('WP_SITEURL','https://wordpress-starter-project.lndo.site');

	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'SCRIPT_DEBUG', true );
}
