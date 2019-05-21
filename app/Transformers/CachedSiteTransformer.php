<?php

namespace App\Transformers;


class CachedSiteTransformer extends BaseTransformer
{
	/**
	 * Transform cached Site data to the format to set in the User's Session
	 *
	 * @param  array $cacheData
	 * @return array
	 */
	public function sessionFormat( array $cacheData )
	{
		return [
			'children' => ''
		];
	}
}