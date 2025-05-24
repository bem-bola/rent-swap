<?php

namespace App\Mapping;

use App\Entity\Category;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Device;
class DeviceMapping
{

    public function createMapping(): ResultSetMapping
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Device::class, 'd');
        $rsm->addFieldResult('d', 'id', 'id');
        $rsm->addFieldResult('d', 'slug', 'slug');
        $rsm->addFieldResult('d', 'body', 'body');
        $rsm->addFieldResult('d', 'price', 'price');
        $rsm->addFieldResult('d', 'show_phone', 'showPhone');
        $rsm->addFieldResult('d', 'created', 'created');
        $rsm->addFieldResult('d', 'deleted', 'deleted');
        $rsm->addFieldResult('d', 'title', 'title');
        $rsm->addFieldResult('d', 'status', 'status');
        $rsm->addFieldResult('d', 'location', 'location');
        $rsm->addFieldResult('d', 'phone_number', 'phoneNumber');
        $rsm->addFieldResult('d', 'quantity', 'quantity');
        $rsm->addMetaResult('d', 'parent_id', 'parent_id');
        $rsm->addMetaResult('d', 'user_id', 'user_id');
        $rsm->addMetaResult('d', 'user_id', 'user_id');
        $rsm->addMetaResult('d', 'type_id', 'type_id');

        return $rsm;
    }
}