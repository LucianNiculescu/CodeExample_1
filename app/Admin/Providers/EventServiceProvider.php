<?php

namespace App\Admin\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Admin\Modules\Packages\Events\PackageUpdated' => [
            'App\Admin\Modules\Packages\Listeners\UpdateActiveTransactionsWithPackage',
        ],

		'App\Admin\Modules\Vouchers\Events\VoucherUpdated' => [
			'App\Admin\Modules\Vouchers\Listeners\UpdateActiveVoucherTransactionsWithPackages',
		],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
