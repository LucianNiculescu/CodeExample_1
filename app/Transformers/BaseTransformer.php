<?php
namespace App\Transformers;

/**
 * Class BaseTransformer
 *
 * Please refer to documentation on the wiki for usage
 *
 * @package App\Transformers
 * @link https://github.com/airangel/myairangel-v3/wiki/Transformer-Implementation-Guidelines
 */
class BaseTransformer
{
	/**
	 * The model or models to be transformed
	 *
	 * @var mixed
	 */
	protected $subject;

	/**
	 * The return type of transformed models (array or object)
	 *
	 * @var string
	 */
	protected $returnType = 'array';

	public function __construct($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Instantiates the class setting the subject which could be an individual
	 * model or collection of models to be transformed
	 *
	 * @param  mixed $subject
	 * @return BaseTransformer
	 */
	public static function transform($subject)
	{
		return new static($subject);
	}

	/**
	 * Specifies the format to transform models into (which must be set on
	 * the extending class as a method with `Format` prepended
	 *
	 * @param $format
	 * @return mixed
	 */
	public function into($format)
	{
		$method = $format . 'Format';
		$formatBy = $this->isMethodOrProperty($method);

		// If we have a collection of models, iterate over to build result
		if($this->subject instanceof \Traversable)
		{
			$result = [];
			foreach($this->subject as $model)
				$result[] = $this->transformSubject($model, $method, $formatBy);

			return $result;
		} else {
			// Return the subject transformed
			return $this->transformSubject($this->subject, $method, $formatBy);
		}
	}

	/**
	 * Dynamically set the type of the returned result items
	 *
	 * @param string $type  array or object
	 * @return $this
	 */
	public function as($type)
	{
		$this->returnType = $type;

		return $this;
	}

	/**
	 * Checks whether the requested formatter is set on the extending
	 * class as a property or a method
	 *
	 * @param $method
	 * @return string
	 */
	private function isMethodOrProperty($method)
	{
		if(method_exists($this, $method))
			return 'method';

		if(property_exists($this, $method))
			return 'property';

		return 'toArray';
	}

	/**
	 * Transform the $subject using either the $method (or property defined in $formatBy)
	 * set on the extending model
	 *
	 * @param  mixed  $subject  The model to be transformed
	 * @param  string $method   The method or property set on the extending model
	 * @param  string $formatBy method|property
	 * @return array|object
	 */
	private function transformSubject($subject, $method, $formatBy)
	{
		$transformed = null;

		switch($formatBy)
		{
			case 'property':

				$result = [];

				// Loop through property and use values as property on the subject
				foreach($this->$method as $key)
				{
					// Cater for alias set in the key names
					$keyParts = explode('|', $key);
					$propertyPath = $keyParts[0];
					$key = isset($keyParts[1]) ? $keyParts[1] : $propertyPath;

					$result[$key] = array_reduce(explode('.', $propertyPath), function($obj, $prop) {
						return $obj->$prop;
					}, $subject);
				}

				$transformed = $result;

				break;
			case 'method':

				$transformed = $this->$method($subject);

				break;
			case 'toArray':

				$transformed = $subject->toArray();

				break;
		}

		return  $this->returnType === 'object' ? (object) $transformed : $transformed;
	}
}