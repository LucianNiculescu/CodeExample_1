<?php
namespace app\Traits;

/**
 * Class TranslatableAttributes
 *
 * Add trait to model to quickly get translated versions of attributes. After adding the trait,
 * add a `$translatable` property as an array who's values are the attribute keys for the model which
 * has a translation. Then, when getting the attribute `$model->attribute_name` under the hood would
 * actually return `trans('admin.{value_of_attribute}')`.
 *
 * You can use the trait passively by adding the `$translateOnGet` property to your model and
 * setting to `false`. Then `$model->attribute_name` would return the raw model value (from the
 * database or after any mutators). You can still translate the field by calling
 * `$model->translation('attribute_name`)`, or by appending "_translated" to the attribute
 * `$model->attribute_name_translated`
 *
 * @package App\Traits
 */
trait TranslatableAttributes
{
	/**
	 * The type to use as the prefix in the translator interface (the type column in the
	 * translations table). Defaults to admin
	 * @var string
	 */
	public $translationType = 'admin';

	/**
	 * Overrides Eloquent\Model::__get() to return the translated value if the $translationOnGet
	 * property is set to true in the model, and the model has an array property $translatable
	 * with the attribute's key set as a value
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		$attr = parent::__get($key);

		// If we've nothing to translate, return the attribute
		if(!$this->hasTranslatableAttributes())
			return $attr;

		// Translate the attribute if we're translating every attribute
		// set in the $translatable
		if($this->shouldTranslateOnGet())
		{
			return $this->isTranslatableAttribute($key)
				? $this->translation($key)
				: $attr;
		} else {
			// Only translate if the $key passed ends with _translated
			if(ends_with($key, '_translated'))
			{
				$strParts = explode('_', $key);
				array_pop($strParts);
				$key  = implode('_', $strParts);
				$attr = parent::__get($key);

				return $this->isTranslatableAttribute($key)
					? $this->translation($attr)
					: $attr;
			}

			return $attr;
		}
	}

	/**
	 * Whether the $translatable array property is set on the class
	 *
	 * @return bool
	 */
	private function hasTranslatableAttributes()
	{
		return property_exists($this, 'translatable') && is_array($this->translatable);
	}

	/**
	 * Whether the trait should translate on _get() of the attributes
	 *
	 * @return bool
	 */
	private function shouldTranslateOnGet()
	{
		return isset($this->translateOnGet) ? $this->translateOnGet : false;
	}

	/**
	 * Whether the given key is in the $translatable property
	 *
	 * @param $key
	 * @return bool
	 */
	private function isTranslatableAttribute($key)
	{
		return in_array($key, $this->translatable);
	}

	/**
	 * Returns the translated attribute
	 *
	 * @param string $attrKey
	 * @param string $val
	 * @return string|\Symfony\Component\Translation\TranslatorInterface
	 */
	public function translation($attrKey, $val = false)
	{
		return trans(implode('.', [$this->translationType, $attrKey ]));
	}
}