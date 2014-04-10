<?php
/**
* Move Message [English]
*
* @package phpBB.de Move Message
* @copyright (c) 2014 phpBB.de
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
	'MOVED_MESSAGE'				=> 'Moved from <strong>%1$s</strong> to <strong>%2$s</strong> by %3$s on <strong>%4$s</strong>',
));
