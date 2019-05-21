<?php

namespace App\Admin\Modules\Sites;

use App\Models\AirConnect\Site;

class SiteObserver
{
    /**
     * Listen to the Site created event and update cache
     *
     * @param  Site $site
     * @return bool
     */
    public function created(Site $site)
    {
        cached_site_service($site)->destroyCachedPath();

        return true;
    }

    /**
     * Listen to the Site updated event and update cache
     *
     * @param  Site $site
     * @return bool
     */
    public function updated(Site $site)
    {
        cached_site_service($site)->destroyCachedPath();
        return true;
    }
}