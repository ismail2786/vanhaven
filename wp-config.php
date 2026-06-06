<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'vanheaven' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '#H9xa}MDaR~I[K<qABos;3f_5}4Sv0q(q*e3`_%Ho0Fw-GwQbUn![xqyTP/KDNA}' );
define( 'SECURE_AUTH_KEY',  '_{9b,+-sO{ZbZ~g5H^%tEB]uOO/I@3u1f,G_7PAdtXb}daLFC5g!w805}{?~pJv=' );
define( 'LOGGED_IN_KEY',    'C=3t&b0}_Mf%l{!]w,?WqBFfDkJG;oJ,47&s)E`UbY/QByk jdS2N9/}cBpo~<.:' );
define( 'NONCE_KEY',        '+&VpQ{af8K$XU9QtM9D&Qs3c5O!ahrwaG8ri|oH0q-9:K|Wb648l6w+&C)y(v*WS' );
define( 'AUTH_SALT',        '4y`s?IX2w[c644R#PCaxfP3HdbNYbcRWQ{2WvC<xp&b3|d.7YiZ&)V]D-0ZD+QuG' );
define( 'SECURE_AUTH_SALT', 'YY? XLs n[p(`Oo^^<(gwH5gD!Rvx=fvmZl-+.fJ#5d$YDw8c%BSd],?`Y)]zsA,' );
define( 'LOGGED_IN_SALT',   'esEQ5piT-+sr7BNO=S1IP$KGM~_n&Ys[@r$+>VlN}Wg,~eJBW%<}MpydS}Co<X5@' );
define( 'NONCE_SALT',       '>]Z<StsQ4N +jT5U~oRg,>W;0g$k$xxEJEDa3/*%UE/cDU%Lc/f!p`/~^Pc3VlP<' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
