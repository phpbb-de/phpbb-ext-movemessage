<?php
/**
* Move Message [Deutsch — Sie]
*
* @package phpBB.de Move Message
* @copyright (c) 2015 phpBB.de
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'MOVED_MESSAGE'				=> 'Verschoben von <strong>%1$s</strong> nach <strong>%2$s</strong> am %3$s durch <strong>%4$s</strong>',
));
