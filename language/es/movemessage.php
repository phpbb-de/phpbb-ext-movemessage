<?php
/**
* Move Message [Spanish]
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
	'MOVED_MESSAGE'				=> 'Movido desde <strong>%1$s</strong> a <strong>%2$s</strong> el %4$s por <strong>%3$s</strong>',
));
