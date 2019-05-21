<?php

namespace App\Admin\Modules\Packages\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\AirConnect\Package;

class PackageUpdated
{
    use SerializesModels;

	/**
	 * @var Package $package
	 */
    public $package;

	/**
	 * Whether the Package's active transactions should be updated
	 *
	 * @var bool
	 */
    public $shouldUpdateTransactions;

	/**
	 * @var int|bool
	 */
    public $gatewayId;

	/**
	 * Create a new event instance.
	 *
	 * @param Package $package
	 * @param bool $shouldUpdateTransactions
	 * @param bool $gatewayId
	 */
    public function __construct(Package $package, $shouldUpdateTransactions = false, $gatewayId = false)
    {
        $this->package = $package;
        $this->shouldUpdateTransactions = $shouldUpdateTransactions;
        $this->gatewayId = $gatewayId;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
