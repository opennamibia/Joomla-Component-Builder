<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    4th September, 2022
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\Componentbuilder\Compiler\Service;


use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use VDM\Joomla\Componentbuilder\Compiler\Joomlaplugin\Data;
use VDM\Joomla\Componentbuilder\Compiler\Joomlaplugin\Structure;


/**
 * Joomla Plugin Service Provider
 * 
 * @since 3.2.0
 */
class Joomlaplugin implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 * @since 3.2.0
	 */
	public function register(Container $container)
	{
		$container->alias(Data::class, 'Joomlaplugin.Data')
			->share('Joomlaplugin.Data', [$this, 'getData'], true);

		$container->alias(Structure::class, 'Joomlaplugin.Structure')
			->share('Joomlaplugin.Structure', [$this, 'getStructure'], true);
	}

	/**
	 * Get the Joomla Plugin Data
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Data
	 * @since 3.2.0
	 */
	public function getData(Container $container): Data
	{
		return new Data(
			$container->get('Config'),
			$container->get('Customcode'),
			$container->get('Customcode.Gui'),
			$container->get('Placeholder'),
			$container->get('Language'),
			$container->get('Field'),
			$container->get('Field.Name'),
			$container->get('Model.Filesfolders')
		);
	}

	/**
	 * Get the Joomla Plugin Structure Builder
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Structure
	 * @since 3.2.0
	 */
	public function getStructure(Container $container): Structure
	{
		return new Structure(
			$container->get('Joomlaplugin.Data'),
			$container->get('Component'),
			$container->get('Config'),
			$container->get('Registry'),
			$container->get('Customcode.Dispenser'),
			$container->get('Event'),
			$container->get('Utilities.Counter'),
			$container->get('Utilities.Folder'),
			$container->get('Utilities.File'),
			$container->get('Utilities.Files')
		);
	}

}

