<?php

/**
*
* @package phpBB.de Move Message
* @copyright (c) 2015 phpBB.de
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace phpbbde\movemessage\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	/**
	* Subscribe to the Event on viewtopic.php
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_get_post_data'		=> 'load_move_message_data',
			'core.viewtopic_post_row_after'		=> 'assign_move_messages_to_template',
		);
	}

	/** @var string */
	protected $sort_key;

	/** @var string */
	protected $sort_dir;

	/** @var int */
	protected $sort_days;

	/** @var int */
	protected $topic_id;

	/** @var int */
	protected $forum_id;

	/** @var array */
	protected $move_messages = array();

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Construct
	*
	* @param \phpbb\auth\auth			$auth
	* @param \phpbb\content_visibility	$content_visibility
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	* @param string				$phpbb_root_path
	* @param string				$phpEx
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\content_visibility $content_visibility, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, $phpbb_root_path, $phpEx)
	{
		$this->auth = $auth;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;
	}

	/**
	* Load the move messages
	*
	* Event: core.viewtopic_get_post_data
	*
	* @param array $event
	* @return null
	*/
	public function load_move_message_data($event)
	{
		$this->move_messages = array();

		$this->forum_id = $event['forum_id'];
		$this->topic_id = $event['topic_id'];

		$this->sort_key = $event['sort_key'];
		$this->sort_days = $event['sort_days'];
		$this->sort_dir = $event['sort_dir'];

		// Get Information about topic moves
		$limit_log_time = ($event['sort_days']) ? 'AND l.log_time >= ' . (time() - ($this->sort_days * 86400)) . ' ' : '';

		$sql = 'SELECT l.*, u.username, user_colour
			FROM ' . LOG_TABLE . ' l, ' . USERS_TABLE . ' u
			WHERE l.log_type = ' . LOG_MOD . "
				AND l.topic_id =  {$event['topic_id']}
				AND l.log_operation = 'LOG_MOVE'
				$limit_log_time
				AND l.user_id = u.user_id
			ORDER BY l.log_time " . (($this->sort_key == 't' && $this->sort_dir == 'd') ? 'DESC' : 'ASC');
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$log_data = @unserialize($row['log_data']);

			// We only can display the info, when the forum ids are in the log,
			// otherwise we do not know, whether the user should know the forum.
			// PHPBB3-12373
			if (!empty($log_data) && sizeof($log_data) >= 4)
			{
				// Check f_read permissions
				if ($this->auth->acl_get('f_read', $log_data[2]) && $this->auth->acl_get('f_read', $log_data[3]))
				{
					$this->move_messages[] = $row;
				}
			}
		}
		$this->db->sql_freeresult($result);

		if (!empty($this->move_messages))
		{
			$this->user->add_lang_ext('phpbbde/movemessage', 'movemessage');
		}
	}

	/**
	* Display the move messages
	*
	* Event: core.viewtopic_post_row_after
	*
	* @param array $event
	* @return null
	*/
	public function assign_move_messages_to_template($event)
	{
		$i = (int) $event['current_row_number'];
		$end = (int) $event['end'];
		$start = (int) $event['start'];
		$total_posts = (int) $event['total_posts'];

		// Display Move Message information
		if (!empty($this->move_messages))
		{
			if ($this->sort_key == 't')
			{
				// Ordered by time - we can display directly after/before the post
				if ($this->sort_dir == 'a')
				{
					// If we are not on the first page, get rid of all data before the first post
					if ($i == 0 && $start != 0)
					{
						while (!empty($this->move_messages) && $this->move_messages[0]['log_time'] <= $event['row']['post_time'])
						{
							array_shift($this->move_messages);
						}
					}

					// Output all moves before this post
					while (!empty($this->move_messages) && $this->move_messages[0]['log_time'] <= $event['row']['post_time'])
					{
						$move_message = array_shift($this->move_messages);
						$this->assign_move_message('postrow.post_message_before', $move_message);
					}

					if ($i + 1 == $end)
					{
						// We've reached the last row. Get the timestamp of the next record
						$next_post_time = 0;
						if ($start + $i + 1 != $total_posts)
						{
							$next_post_time = $this->get_next_post_time($event['row']['post_time']);
						}

						// Output all rows before the next one
						while (!empty($this->move_messages) && (!$next_post_time || $this->move_messages[0]['log_time'] <= $next_post_time))
						{
							$move_message = array_shift($this->move_messages);
							$this->assign_move_message('postrow.post_message_after', $move_message);
						}
					}
				}
				else
				{
					// Descending order

					if ($i == 0)
					{
						// We are in the first row. Get the timestamp of the previous record
						$next_post_time = 0;
						if ($start != 0)
						{
							$next_post_time = $this->get_next_post_time($event['row']['post_time']);
						}

						// Get rid of all rows before the previous post
						while (!empty($this->move_messages) && $next_post_time && $this->move_messages[0]['log_time'] >= $next_post_time)
						{
							array_shift($this->move_messages);
						}
					}

					while (!empty($this->move_messages) && $this->move_messages[0]['log_time'] >= $event['row']['post_time'])
					{
						$move_message = array_shift($this->move_messages);
						$this->assign_move_message('postrow.post_message_before', $move_message);
					}

					// If we are on the last page, output the rest
					if ($start + $i + 1 == $total_posts)
					{
						while (!empty($this->move_messages))
						{
							$move_message = array_shift($this->move_messages);
							$this->assign_move_message('postrow.post_message_after', $move_message);
						}
					}
				}
			}
			// Not ordered by time - we have to display all messages at the beginning
			else if ($i == 0 && $start == 0)
			{
				// First Page, First Post, ... display all messages
				foreach ($this->move_messages as $move_message)
				{
					$this->assign_move_message('postrow.post_message_before', $move_message);
				}

				// There is nothing more to display - reset array
				$this->move_messages = array();
			}
		}
	}

	/**
	* Get next post time
	*
	* @param	int	$last_post_time		Post time of the last displayed post
	* @return	int		post_time of the next post
	*/
	protected function get_next_post_time($last_post_time)
	{
		$min_post_time = time() - ($this->sort_days * 86400);
		$min_post_time = max((int) $last_post_time, $min_post_time);

		$sql = 'SELECT MIN(post_time) as post_time
			FROM ' . POSTS_TABLE . '
			WHERE topic_id = ' . (int) $this->topic_id . '
				AND post_time > ' . (int) $min_post_time . '
				AND ' . $this->content_visibility->get_visibility_sql('post', $this->forum_id);
		$result2 = $this->db->sql_query($sql);
		$post_time = $this->db->sql_fetchfield('post_time');
		$this->db->sql_freeresult($result2);

		return (int) $post_time;
	}
	/**
	* Assign Move Message to the template
	*
	* @param	string	$template_block	Name of the template block we assign to
	* @param	array	$data			Row of the log-entry
	* @return	null
	*/
	protected function assign_move_message($template_block, $data)
	{
		$log_data_ary = unserialize($data['log_data']);

		$u_from_forum = append_sid($this->root_path . 'viewforum.' . $this->php_ext, 'f=' . $log_data_ary[2]);
		$l_from_forum = '<a href="' . $u_from_forum . '">' . $log_data_ary[0] . '</a>';
		$u_to_forum = append_sid($this->root_path . 'viewforum.' . $this->php_ext, 'f=' . $log_data_ary[3]);
		$l_to_forum = '<a href="' . $u_to_forum . '">' . $log_data_ary[1] . '</a>';

		$l_move_message = $this->user->lang(
			'MOVED_MESSAGE',
			$l_from_forum,
			$l_to_forum,
			$this->user->format_date($data['log_time']),
			get_username_string('full', $data['user_id'], $data['username'], $data['user_colour'])
		);

		$this->template->assign_block_vars($template_block, array(
			'MESSAGE'	=> $l_move_message,
		));
	}
}
