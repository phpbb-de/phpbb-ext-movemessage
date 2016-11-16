<?php
/**
* Move Message [Portuguese-Brazilian]
*
* Portuguese-Brazilian translation by Leinad4Mind
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
	'MOVED_MESSAGE'				=> 'Movido de <strong>%1$s</strong> para <strong>%2$s</strong> em %3$s por <strong>%4$s</strong>',
));
