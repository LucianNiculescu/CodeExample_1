<?php
namespace App\Admin\Modules\Whitelist;

use Illuminate\Routing\Controller as BaseController;
use App\Models\AirConnect\Whitelist;
use App\Admin\Modules\Whitelist\Requests\StoreRequest;
use App\Admin\Modules\Whitelist\Requests\DeleteRequest;
use App\Admin\Helpers\Messages;

/**
 * Class Controller - Whitelist
 */
class Controller extends BaseController
{
	/**
     * Display a listing of the Whitelist for the Site (and children)
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
		return view('admin.modules.whitelist.index',
			SetupViewData::clientSideDatatable()
		);
    }

	/**
	 * Create a new Whitelist record
	 */
	public function store(StoreRequest $request)
	{
		// Create new Whitelist MAC record(s)
		Logic::storeFromRequest($request);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.whitelist-saved'));

		return redirect()->route('networking.whitelist.index');
	}

	/**
	 * Hard delete the whitelist record
	 *
	 * @TODO   Should be in API Controller
	 * @param  DeleteRequest $request
	 * @param  int $whitelistId
	 * @return int
	 */
    public function destroy(DeleteRequest $request, $whitelistId)
    {
		Whitelist::findOrFail($whitelistId)->delete();
        return 1;
    }
}