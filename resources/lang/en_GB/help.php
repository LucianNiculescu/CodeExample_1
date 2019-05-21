<?php
/*
 * Returns an array of translations as Key=>translation depending on the local and type
 */
use App\Admin\Modules\Translations\Logic as Translations;

return Translations::getTranslationCache( 'en', 'help');
