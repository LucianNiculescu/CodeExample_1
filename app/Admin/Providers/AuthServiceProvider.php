<?php

namespace App\Admin\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */

	private $userPermissions = [];

    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // registering a new gate called access
        Gate::define('access', function ($user , $permission) {

			if (empty($this->userPermissions))
				$this->userPermissions = $user->getPermissions();

			// return true if $permission is ''
			if($permission == '')
				return true;

			// Return true if the permission is in the userPermissions or false if it is not
			return in_array($permission, $this->userPermissions) or in_array('all-' . $permission, $this->userPermissions);
        });

        // checking if the user is Dev then ignore the gate checking
        Gate::before(function ($user)
        {
            if($user->role_id == 0)	// was adminId
            {
                return true;
            }

        });

        /*
        ////////How to use this in PHP

        $permission = 'zzz';
                if (Gate::denies('access' ,$permission ))
        { // or allows
            abort(403,$permission . ' is needed here, sorry!');
        }


	    // another way
        $user = Auth::user();
        if($user->can('access' ,$permission ))
        { // or cannot
            abort(403,$permission . ' is needed here, sorry!');
        }

		////////How to use this in Blade
        @can('access', 'role.edit')
            You can access this now
        @endcan
        */
    }
}
