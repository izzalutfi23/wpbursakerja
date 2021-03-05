<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'A_n~n5AM#.ZB[o0<]p]4,7MQq&jy1K-;TG`(=X9>}K=}r1zB0|BPaXX<_-#`v FH' );
define( 'SECURE_AUTH_KEY',  'P.>Lgl;.j<aARk~t.obh7sTa0e2SV:OoBpTZk;IGld?nsy[(wJ9^=E]x {jS0:|i' );
define( 'LOGGED_IN_KEY',    'aK^y{0%tRGT9GPgq7j!JEd_nq+xtGel.N#frzl$o=yqSb2(4[R;[V4iJlH2*5am^' );
define( 'NONCE_KEY',        ')D!6WTA:Ix,`f|6M&3t-a:wH 1Dq<1*8y: %B88|~iGI%&NW_8#!4(33bu8nk)tR' );
define( 'AUTH_SALT',        'q}@a%^A;-uu[x/%GP8F7-&aQGq08kpiHuIO_^4tQUy|/O>r.:d^ti>A;_@GvsyqF' );
define( 'SECURE_AUTH_SALT', '+icg%Bl >Gi@NAGd,2eb^(6+EBSM<Yh!&N::?e4pC=$[XU|i`#BOU{k+ci-llj#~' );
define( 'LOGGED_IN_SALT',   'PA<oIgZhSFKZWLF3,]0o1^KL8h>mp%`/3|:F-q_,64e(WG<B^.6Ak~|AidtaDpU{' );
define( 'NONCE_SALT',       'mL|l8Oq4xdE&&M26V,8d#`8_#[ttGX^heTz#t//-VLa-gQ<@BLe;UyWGd(ALBHS>' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
