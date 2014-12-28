<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:06 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Enum\Select;
use Aws\DynamoDb\Model\Item;
use dnocode\awsddb\ddb\enums\Filter;
use yii\base\Object;

class PutInput extends AWSInput {

    public function buildModel($attributes=array()){

        $this->_modelItem=Item::fromArray($attributes);
        $this->_modelItem->setTableName($this->_tablename);
    }


    public function toUpdateAttributes($attributes=array()){

        $this->_to_update_attributes=Item::fromArray($attributes);

    }

    /**
     * @return AWSFilter
     */
    public function filter()
    {
        return null;
    }


    /**
     * @param $type  put | delete | add
     * @return array
     */
    public function toArray($type){


        $output=parent::toArray($type);
        unset($output["ConsistentRead"]);
        switch($type){
            case AttributeAction::DELETE:

                $item=$output["Item"];
                unset($output["Item"]);
                $output["Key"]=$item;
                /**same code right now
                 * let this  switch for future edit**/
                break;

            case AttributeAction::ADD:

                $item=$output["Item"];
                unset($output["Item"]);
                 $output["Key"]=$item;

                 break;
         }

        return $output;
    }


}