<?php
/**
 * Created by PhpStorm.
 * User: sheri
 * Date: 8/11/2016
 * Time: 05:15 PM
 */

namespace App\Admin\Modules\Templates;

use App\Models\AirConnect\AdminTemplate as TemplateModel;


class Logic
{

    public static function getTemplates()
    {
        $templates = TemplateModel::select('id', 'name')->where('status', 'active')->get()->toArray();

        return $templates;
    }
}