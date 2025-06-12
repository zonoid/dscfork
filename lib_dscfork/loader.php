<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined( '_JEXEC' ) or die ;

class StratumLoader extends JLoader
{
	public function __construct( )
	{
		// Register StratumLoader::load as an autoload class handler.
		spl_autoload_register( array( $this, 'load' ) );
	}

	/**
	 * Method to recursively discover classes of a given type in a given path.
	 *
	 * @param   string   $classPrefix  The class name prefix to use for discovery.
	 * @param   string   $parentPath   Full path to the parent folder for the classes to discover.
	 * @param   boolean  $force        True to overwrite the autoload path value for the class if it already exists.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function discover( $classPrefix, $parentPath, $force = true, $recurse = false )
	{
		$excluded_dirs = array( '.', '..' );

		// Ignore the operation if the folder doesn't exist.
		if ( is_dir( $parentPath ) )
		{

			// Open the folder.
			$d = dir( $parentPath );

			// Iterate through the folder contents to search for input classes.
			while ( false !== ($entry = $d->read( )) )
			{
				if ( !in_array( $entry, $excluded_dirs ) && is_dir( $parentPath . DIRECTORY_SEPARATOR . $entry ) )
				{
					self::discover( $classPrefix . $entry, $parentPath . DIRECTORY_SEPARATOR . $entry, $force, $recurse );
				} else
				{
					// Only load for php files.
					if ( file_exists( $parentPath . DIRECTORY_SEPARATOR . $entry ) && (substr( $entry, strrpos( $entry, '.' ) + 1 ) == 'php') )
					{

						// Get the class name and full path for each file.
						$class = strtolower( $classPrefix . preg_replace( '#\.[^.]*$#', '', $entry ) );
						$path = $parentPath . DIRECTORY_SEPARATOR . $entry;

						// Register the class with the autoloader if not already registered or the force flag is set.
						self::register( $class, $path, $force );
					}
				}
			}

			// Close the folder.
			$d->close( );
		}
	}

}
