<?php

namespace App\Admin\Modules\Packages\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Admin\Modules\Packages\Events\PackageUpdated;
use App\Models\AirConnect\Gateway;


class UpdateActiveTransactionsWithPackage implements ShouldQueue
{
	/**
	 * Number of transactions updated
	 *
	 * @var int
	 */
	public $transactionsUpdated = 0;

    /**
     * Handle the PackageUpdated event by updating transactions with new attributes
	 * where the user has specified to
     *
     * @param  PackageUpdated  $event
     * @return bool
     */
    public function handle(PackageUpdated $event)
    {
    	// Should we continue? Event property set from the user's input on Package update
    	if(!$event->shouldUpdateTransactions)
    		return false;

    	// Get the transactions to update and set count property for external testing
		$transactions = $event->package->activeTransactions()->get();
		$this->transactionsUpdated = $transactions->count();

		// Resolve the Gateway Type Class to use to repopulate the radcheck and radreply tables
		$transactions->transform(function($transaction) use ($event) {

			// Get the Gateway selected by the user during the update request and resolve the type class
			$transaction->gateway = Gateway::findOrFail($event->gatewayId);
			$transaction->gateway_type_class_name = $transaction->gateway->resolveTypeClassName();

			return $transaction;
		});

		// Remove all existing radreply and radcheck entries for the transaction to replace
		$transactions->each(function($transaction) {
			$transaction->radcheck()->delete();
			$transaction->radreply()->delete();
		});

		// Create a new Gateway Type per transaction to create new radreply attrs
		$transactions->each(function($transaction) use ($event) {
			new $transaction->gateway_type_class_name($transaction, $event->package, $transaction->gateway->type);
		});

        return true;
    }
}
