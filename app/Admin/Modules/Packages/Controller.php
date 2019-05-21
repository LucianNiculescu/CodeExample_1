<?php
namespace App\Admin\Modules\Packages;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Admin\Modules\Packages\Requests\StoreRequest;
use App\Admin\Modules\Sites\Logic as SitesLogic;
use App\Admin\Helpers\Messages;
use App\Models\AirConnect\SiteAttribute as SiteAttributeModel;
use App\Admin\Middleware\Access;
use App\Models\AirConnect\Package;
use App\Admin\Modules\Pms\Logic as Pms;

/**
 * Class Controller - Packages
 *
 * Validation logic found in StoreRequest.
 *
 * @link https://github.com/airangel/myairangel-v3/wiki/Packages
 */
class Controller extends BaseController
{
	/**
	 * Display a listing of the Packages
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index()
	{
		return view('admin.modules.packages.client-side-index',
			SetupViewData::clientSideDatatable()
		);
	}

	/**
	 * Show the form for creating a new record.
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function create()
	{
		// Open the create view
		return view('admin.modules.packages.form',
			SetupViewData::create()
		);
	}

	/**
	 * Create a new Package. Request validation makes sure only one free email package
	 * can be stored per site
	 *
	 * @param \App\Admin\Modules\Packages\Requests\StoreRequest $request
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
	public function store(StoreRequest $request)
	{
		// Create package from request data, including parsing various attributes
		$package = Logic::setPackageFromRequest($request);
		// Deleting existing site attributes
		Logic::siteAttributesData()->delete();
		$siteAttributes = Logic::setSiteAttributesFromRequest($request);
		SiteAttributeModel::insert($siteAttributes);

		// Refresh the Site session to pick up the new package type
        logged_in_site()->cachedData()->destroyCachedData();
		SitesLogic::setupSession($package->site);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.package-saved'));

		// If user doesn't have access to edit go to index page
		if(\Gate::denies('access', Access::permissionFromPath(\Request::path() . '/edit')))
			return redirect(\Request::path());
		else
			return redirect()
					->route('manage.packages.edit', [$package]);
	}

	/**
	 * Show the form for editing an existing record
	 *
	 * @param  int $id
	 * @return \Response
	 */
	public function edit($id)
	{
		$package = Package::with('packageAttributes', 'site.gateways', 'site.attributes')->findOrFail($id);

		// Ensure package is part of the loaded site
		Logic::validateSiteRelationship($package);

		// Open the create view
		return view('admin.modules.packages.form',
			SetupViewData::edit($package)
		);
	}

	/**
	 * Update the package
	 *
	 * @param \App\Admin\Modules\Packages\Requests\StoreRequest $request
	 * @param $id
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|integer
	 */
	public function update(StoreRequest $request, $id)
	{
		$package = Package::with('packageAttributes')->findOrFail($id);

		// For AJAX requests, update the package status (validated in the Request)
		if($request->ajax())
		{
			if($package->type == 'pms' and !Pms::pmsCheck())
				return trans('admin.no-pms-found');

			$package->update(['status' => $request->status]);
			return 1;
		}

		// Update package from request data, including parsing various attributes
		$package = Logic::setPackageFromRequest($request, $package);

		// Deleteing existing siteattributes
		Logic::siteAttributesData()->delete();
		// Inserting the siteattributes
		$siteAttributes = Logic::setSiteAttributesFromRequest($request);
		SiteAttributeModel::insert($siteAttributes);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.package-saved'));

		return redirect()
					->route('manage.packages.edit', [$package]);
	}

	/**
	 * Soft delete the package
	 *
	 * @TODO   Should be in API Controller
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return int
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 */
	public function destroy(Request $request, $id)
	{
		$package = Package::findOrFail($id);

		// Only allow AJAX requests
		if($request->ajax())
		{
			$package->update(['status' => 'delete']);
            logged_in_site()->cachedData()->destroyCachedData();
            SitesLogic::setupSession($package->site);
			return 1;
		} else {
			abort('401', trans('error.not-authorized'));
		}
	}

	/**
	 * Returns the package attributes values in a human readable form
	 * @param $packageId
	 * @param bool $join
	 * @return mixed
	 */
	public function getHumanReadableByPackage($packageId, $join = false) {
		return Logic::getHumanReadableByPackage($packageId, $join);
	}
}