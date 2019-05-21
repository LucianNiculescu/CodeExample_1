<?php

namespace App\Transformers;

use App\Models\AirConnect\Attribute;

class AttributeTransformer extends BaseTransformer
{
    /**
     * Transform an attribute record for use in blades
     *
     * @param Attribute $attribute
     * @return array
     */
    public function bladeFormat(Attribute $attribute)
    {
        return [
            'id' => $attribute->id,
            'name' => $attribute->attribute,
            'type' => $attribute->type,
            'input_type' => $this->htmlInputType($attribute->type),
            'description' => $attribute->description,
            'mode' => $attribute->mode,
        ];
    }

    /**
     * Get an HTML input type from the attribute type
     *
     * @param string $type
     * @return string
     */
    private function htmlInputType($type)
    {
        if(str_contains($type, 'int'))
            return 'number';

        if(str_contains($type, 'bool'))
            return 'bool';

        return 'text';
    }
}