<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @gitea      Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\Utilities;


use Joomla\CMS\Factory;


/**
 * Global Unique ID Helper
 * 
 * @since  3.0.9
 */
abstract class GuidHelper
{
	/**
	 * Returns a GUIDv4 string
	 * 
	 * Thanks to Dave Pearson (and other)
	 * https://www.php.net/manual/en/function.com-create-guid.php#119168 
	 *
	 * Uses the best cryptographically secure method
	 * for all supported platforms with fallback to an older,
	 * less secure version.
	 *
	 * @param bool $trim
	 *
	 * @return string
	 *
	 * @since  3.0.9
	 */
	public static function get($trim = true)
	{
		// Windows
		if (function_exists('com_create_guid') === true)
		{
			if ($trim === true)
			{
				return trim(com_create_guid(), '{}');
			}
			return com_create_guid();
		}

		// set the braces if needed
		$lbrace = $trim ? "" : chr(123);    // "{"
		$rbrace = $trim ? "" : chr(125);    // "}"

		// OSX/Linux
		if (function_exists('openssl_random_pseudo_bytes') === true)
		{
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
			return $lbrace . vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)) . $lbrace;
		}

		// Fallback (PHP 4.2+)
		mt_srand((double)microtime() * 10000);
		$charid = strtolower(md5(uniqid(rand(), true)));
		$hyphen = chr(45);                  // "-"
		$guidv4 = $lbrace.
			substr($charid,  0,  8).$hyphen.
			substr($charid,  8,  4).$hyphen.
			substr($charid, 12,  4).$hyphen.
			substr($charid, 16,  4).$hyphen.
			substr($charid, 20, 12).
			$rbrace;
		return $guidv4;
	}

	/**
	 * Validate the Globally Unique Identifier ( and check if table already has this identifier)
	 *
	 * @param string $guid
	 * @param string $table
	 * @param int      $id
	 *
	 * @return bool
	 *
	 * @since  3.0.9
	 */
	public static function valid($guid, $table = null, $id = 0)
	{
		// check if we have a string
		if (self::validate($guid))
		{
			// check if table already has this identifier
			if (StringHelper::check($table))
			{
				// Get the database object and a new query object.
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('COUNT(*)')
					->from('#__componentbuilder_' . (string) $table)
					->where($db->quoteName('guid') . ' = ' . $db->quote($guid));

				// remove this item from the list
				if ($id > 0)
				{
					$query->where($db->quoteName('id') . ' <> ' . (int) $id);
				}

				// Set and query the database.
				$db->setQuery($query);
				$duplicate = (bool) $db->loadResult();

				if ($duplicate)
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * get the item by guid in a table
	 *
	 * @param string $guid
	 * @param string $table
	 * @param string/array $what
	 *
	 * @return mix
	 *
	 * @since  3.0.9
	 */
	public static function item($guid, $table, $what = 'a.id')
	{
		// check if we have a string
		if (self::validate($guid))
		{
			// check if table already has this identifier
			if (StringHelper::check($table))
			{
				// Get the database object and a new query object.
				$db = Factory::getDbo();
				$query = $db->getQuery(true);

				if (ArrayHelper::check($what))
				{
					$query->select($db->quoteName($what));
				}
				else
				{
					$query->select($what);
				}

				$query->from($db->quoteName('#__componentbuilder_' . (string) $table, 'a'))
					->where($db->quoteName('a.guid') . ' = ' . $db->quote($guid));

				// Set and query the database.
				$db->setQuery($query);
				$db->execute();

				if ($db->getNumRows())
				{
					if (ArrayHelper::check($what) || $what === 'a.*')
					{
						return $db->loadObject();
					}
					else
					{
						return $db->loadResult();
					}
				}
			}
		}
		return false;
	}

	/**
	 * Validate the Globally Unique Identifier
	 *
	 * Thanks to Lewie
	 * https://stackoverflow.com/a/1515456/1429677
	 *
	 * @param string $guid
	 *
	 * @return bool
	 *
	 * @since  3.0.9
	 */
	protected static function validate($guid)
	{
		// check if we have a string
		if (StringHelper::check($guid))
		{
			return preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $guid);
		}
		return false;
	}

}

