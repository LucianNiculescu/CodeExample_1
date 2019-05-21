<?php

namespace App\Admin\Helpers\Datatables;

/**
 * Will show the action icons in the actions column depending on the permission
 * Class ActionsColumn
 * @package App\Admin\Helpers\Datatables
 */
class ActionsColumn
{
	public static function renderData($actionName, $action, $row, $gatewayMac)
	{
		$link 		= '';
		$id 		= $row['id'] ?? $row['.id'] ?? '';


		if($actionName == 'edit' and !empty($id) and substr( $id, 0, 1 ) !== "*")
		{
			$link = '	<a title="'. $action['title'] . '" class="action" href="' . $action['route'] . '/' . $id. '/edit">
							<i class="fa ' . $action['icon'] . ' action ' . $action['color']  . '"></i>
						</a>';
		}
		elseif($actionName == 'sign-out')
		{
			if((isset($row['authorized']) and $row['authorized'] === 'true') or (isset($row['bypassed']) and $row['bypassed'] === 'true') or isset($row['session_id']))
			{
				$session = $row['session_id'] ?? '';
				$id 	= $row['.id'] ?? '';
				$user = $row['user'] ?? '';
				$mac = $row['mac'] ?? '';

				$link = '	<a title="' . $action['title'] . '" class="action action_signout" href="' . $action['route'] . '"  
								data-session="' . $session . '" data-gateway-mac="' . $gatewayMac . '" data-guest-mac="' . $mac . '" data-id="' . $id . '" data-name="' . $user . '">
								<i class="fa ' . $action['icon'] . ' action ' . $action['color'] . '"></i>
							</a>';
			}
		}
		elseif($actionName == 'sign-in')
		{
			if((isset($row['authorized']) and $row['authorized'] === 'false') and (isset($row['bypassed']) and $row['bypassed'] === 'false') and !isset($row['session_id']))
			{
				$session = $row['session_id'] ?? '';
				$id 	= $row['.id'] ?? '';
				$user = $row['user'] ?? '';
				$mac = $row['mac'] ?? '';

				$link = '	<a title="' . $action['title'] . '" class="action action_signin" href="' . $action['route'] . '"  
								data-session="' . $session . '" data-gateway-mac="' . $gatewayMac . '" data-guest-mac="' . $mac . '" data-id="' . $id . '" data-name="' . $user . '">
								<i class="fa ' . $action['icon'] . ' action ' . $action['color'] . '"></i>
							</a>';
			}
		}
		elseif($actionName == 'delete' and !empty($id))
		{
			$link = '	<a title="'. $action['title'] . '" class="action action_delete" href="' . $action['route'] . '/' . $id. '/delete" data-id="'.$id.'" data-name="'.$id.'">
							<i class="fa ' . $action['icon'] . ' action ' . $action['color']  . '"></i>
						</a>';
		}

		return $link;
	}
}