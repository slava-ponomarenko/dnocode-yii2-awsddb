<?php
namespace dnocode\awsddb\ddb\inputs;
use Aws\DynamoDb\Enum\Type;

/**
 * Class representing a DynamoDB item attribute. Contains helpers for building
 * attributes and arrays of attributes.
 *
 */
class Attribute extends \Aws\DynamoDb\Model\Attribute
{


    /**
     * Creates a DynamoDB attribute, validates it, and prepares the type and
     * value. Some objects can be used as values as well. If the object has a
     * __toString method or implements the Traversable interface, it can be
     * converted to a string or array, respectively.
     *
     * @param mixed $value The DynamoDB attribute value
     * @param int   $depth A variable used internally to keep track of recursion
     * depth of array processing
     *
     * @return Attribute
     *
     * @throws InvalidArgumentException
     */
    public static function factory($value, $depth = 0)
    {
        if ($depth > 1) {

            throw new InvalidArgumentException('Sets must be at most one level deep.');
        }

        if ($value instanceof Attribute) {

            return $value;

        } elseif ($value instanceof \Traversable) {

            $value = iterator_to_array($value);

        } elseif (is_object($value) && method_exists($value, '__toString')) {

            $value = (string) $value;
        }

        if ($value === null || $value === array() || $value === '') {

            throw new InvalidArgumentException('The value must not be empty.');

        } elseif (is_resource($value) || is_object($value)) {

            if(method_exists($value,"toArray")===false) throw new InvalidArgumentException('every object property must have to array method. use Arrayable Trait');
            $value=$value->toArray();

         }

        if (is_int($value) || is_float($value)) {

            $attribute = new Attribute((string) $value, Type::NUMBER);

        } elseif (is_bool($value)) {

            $attribute = new Attribute($value ? '1' : '0', Type::NUMBER);

        } elseif (is_array($value) || $value instanceof \Traversable ||  is_object($value) ) {


            if(is_array($value)==false&&is_object($value)){

                if(method_exists($value,"toArray")===false)
                {throw new InvalidArgumentException('the property');}
                $value=$value->toArray();
            }

            reset($value);
            $key=key($value);
            /**list or hash**/
            $setType = is_numeric($key)===false?"M":"L";
            $attribute = new Attribute(array());

            foreach ($value as $index=>$subValue) {

                $subAttribute = static::factory($subValue, $depth + 1);
                $setType = $setType === null?$subAttribute->type:$setType;
                $attribute->value[$index] =  $subAttribute;

            }
            $attribute->type = $setType;

        } else {

            $attribute = new Attribute((string) $value);
        }

        return $attribute;
    }

}
