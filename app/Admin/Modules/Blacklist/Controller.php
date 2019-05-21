<?php
namespace App\Admin\Modules\Blacklist;

use Illuminate\Routing\Controller as BaseController;
use App\Models\AirConnect\Blocked;
use App\Admin\Modules\Blacklist\Requests\StoreRequest;
use App\Admin\Modules\Blacklist\Requests\DeleteRequest;
use App\Admin\Helpers\Messages;

/**
 * Class Controller - Blacklist
 */
class Controller extends BaseController
{
	/**
     * Display a listing of the Blacklist for the Site (and children)
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
		return view('admin.modules.blacklist.index',
			SetupViewData::clientSideDatatable()
		);
    }

	/**
	 * Create a new Blocked MAC record
	 */
	public function store(StoreRequest $request)
	{
		// Create new Blocked MAC record(s)
		Logic::storeFromRequest($request);

		Messages::create(Messages::SUCCESS_MSG, trans('admin.blacklist-saved'));

		return redirect()->route('manage.blacklist.index');
	}

	/**
	 * Hard delete the blocked record
	 *
	 * @TODO   Should be in API Controller
	 * @param DeleteRequest $request
	 * @param  int $blockedId
	 * @return int
	 */
    public function destroy(DeleteRequest $request, $blockedId)
    {
		Blocked::findOrFail($blockedId)->delete();
        return 1;
    }
}