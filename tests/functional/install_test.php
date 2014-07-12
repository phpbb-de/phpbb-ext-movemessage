<?php

/**
*
* @package phpBB.de Move Message
* @copyright (c) 2014 phpBB.de
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbde\movemessage\tests\functional;

/**
* @group functional
*/
class install_test extends \phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return array('phpbbde/movemessage');
	}

	public function test_validate_viewtopic()
	{
		$crawler = self::request('GET', 'viewtopic.php?f=1&t=1');
		$this->assertContains('Welcome to phpBB3', $crawler->filter('h2')->text());
	}
}
