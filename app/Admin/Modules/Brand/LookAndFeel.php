<?php
namespace App\Admin\Modules\Brand;

use \App\Admin\Helpers\Messages;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Admin\Helpers\Rules;

class LookAndFeel
{
	/**
	 * Setting up the data to pass for the view
	 * Result is an array of 'logoFileName', 'backgroundFileName', 'backgroundColor', 'borderColor', 'extraColor'
	 * @return array
	 */
	public static function setupView()
	{

		$backgroundColor = $borderColor = $extraColor = '';

		$logoFileName 		= config('app.logoFileName');
		$backgroundFileName = config('app.backgroundFileName');
		$defaultLogo		= config('app.defaultLogo');
		$defaultBackground	= config('app.defaultBackground');

		$siteId = session('admin.site.loggedin');

		// Getting the existing attributes from the DB
		$siteAttributes = self::getLookAndFeelAttributes($siteId)->toArray();

		// Filling the variables from the DB
		if(!empty($siteAttributes))
		{
			foreach($siteAttributes as $attribute)
			{
				if ($attribute['name'] == 'background_color')
					$backgroundColor = $attribute['value'];
				elseif ($attribute['name'] == 'border_color')
					$borderColor = $attribute['value'];
				elseif ($attribute['name'] == 'extra_color')
					$extraColor = $attribute['value'];
			}
		}

		// Setting up logo file name, fall-back is airangel logo
		$logo 				= self::checkPath($logoFileName);
		$logoFileName 		= ($logo != '')? $logo : $defaultLogo;

		// Setting up Background file name , fall back is airangel background
		$background 		= self::checkPath($backgroundFileName);
		$backgroundFileName = ($background != '')? $background: $defaultBackground;

		// Setting up the 3 colors, fall-back is the config
		$backgroundColor 	= ($backgroundColor != '') ? $backgroundColor : config('app.background_color');

		$borderColor 		= ($borderColor != '') ? $borderColor : config('app.border_color');

		$extraColor 		= ($extraColor != '') ? $extraColor : config('app.extra_color');

		return [
			'logoFileName' 			=> $logoFileName,
			'backgroundFileName' 	=> $backgroundFileName,
			'backgroundColor'		=> $backgroundColor,
			'borderColor' 			=> $borderColor,
			'extraColor' 			=> $extraColor,
		];
	}


	/**
	 * Saving the look and feel section
	 * @return mixed
	 */
	public static function save($siteId)
	{
		$requestData = \Request::all();

		$validator = \Validator::make($requestData,         [
			'logo-file'		    =>	Rules::PNG,
			'background-file'  	=>  Rules::JPG,
		]);

		// If validation fails, return back and refill all fields
		if ($validator->fails())
			return \Redirect::back()
				->withErrors($validator)->withInput();


		$existingAttributes = LookAndFeel::getLookAndFeelAttributes($siteId);

		LookAndFeel::deleteSiteAttributes($existingAttributes);

		LookAndFeel::insertLookAndFeelAttributes($siteId, $requestData);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.brand-saved'));

		return \Redirect::to( route('manage.brand.index') );
	}

	/**
	 * Checking the path if the file exists in the uploads/sites/{siteId} or not
	 * @param $fileName
	 * @return string
	 */
	public static function checkPath($fileName)
	{
		$file = '';
		foreach (session('admin.site.path') as $site)
		{
			$tempFile = '/uploads/sites/'. $site. '/'. $fileName;
			if (file_exists(public_path($tempFile)))
			{
				$file = $tempFile;
				break;
			}
		}
		return $file;
	}

	/**
	 * Getting the site attributes for the look and feel from the DB
	 * @param $siteId
	 * @return mixed
	 */
	public static function getLookAndFeelAttributes($siteId)
	{
		return SiteAttributeModel::where('ids', $siteId)
			->where(function($q) {
				$q->where('name', 'background_color')
					->orWhere('name', 'border_color')
					->orWhere('name', 'extra_color');
			})
			->get();
	}


	/**
	 * Deleting the site attributes in order to recreate them
	 * @param $attributes
	 */
	public static function deleteSiteAttributes($attributes)
	{
		$siteAttribute = new SiteAttributeModel();
		$siteAttribute::whereIn('id', $attributes)->delete();
	}


	/**
	 * Inserting new site attributes for the look and feel
	 * Also saving the logo and background files into /uploads/sites/{siteId}/ folder
	 * @param $siteId
	 * @param $data
	 */
	public static function insertLookAndFeelAttributes($siteId, $data)
	{
		$logoFileName 		= config('app.logoFileName');
		$backgroundFileName = config('app.backgroundFileName');

		// Common data for each row, then will add it an array of the name and value for each site attribute
		$comomAttributes = [
			'ids'		=> $siteId,
			'type'		=> 'portal',
			'status'	=> 'active',
		];


		$colorsToInsert = [$comomAttributes + ['name' => 'background_color', 'value' => $data['background_color']],
			$comomAttributes + ['name' => 'border_color', 'value' => $data['border_color']],
			$comomAttributes + ['name' => 'extra_color', 'value' => $data['extra_color']]];

		// Inserting the site attributes
		$siteAttribute = new SiteAttributeModel();

		$siteAttribute::insert( $colorsToInsert	);

		// Saving logo file either from the upload or the default
		if (isset($data['logo-file']) and isset($_FILES["logo-file"]))
			self::copyFileIntoSites($siteId, $data['logo-file'], $logoFileName);
		elseif(!isset($data['logo-file-exists']))
			self::copyFileIntoSites($siteId, $data['logo-file-default'], $logoFileName);

		// Saving Background file either from the upload or the default
		if (isset($data['background-file']) and isset($_FILES["background-file"]))
			self::copyFileIntoSites($siteId, $data['background-file'], $backgroundFileName);
		elseif(!isset($data['background-file-exists']))
			self::copyFileIntoSites($siteId, $data['background-file-default'], $backgroundFileName);

	}


	// ToDo make a helper to copy files
	/**
	 * Coping the files over from the file upload or the default location into the upload sites
	 * @param $id
	 * @param $file
	 * @param $fileName
	 */
	public static function copyFileIntoSites($id, $file, $fileName)
	{
		$destinationDir =  public_path().'/uploads/sites/' . $id . '/';

		// Checking if the directory existing or not if not create it.
		if (!is_dir( $destinationDir))
			mkdir($destinationDir, 0777, true);

		if(gettype($file) == 'string')
			$sourceFile = public_path($file);
		else
			$sourceFile = $file;

		\File::copy($sourceFile, $destinationDir . '/' . $fileName);
	}
}