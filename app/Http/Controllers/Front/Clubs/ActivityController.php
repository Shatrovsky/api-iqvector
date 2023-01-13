<?php

namespace App\Http\Controllers\Front\Clubs;

use App\Models\Clubs\ActivityGroup;
use App\Http\Controllers\Front\BaseController;

class ActivityController extends BaseController
{
    public function activities()
    {
        $model = ActivityGroup::query()->select(['id', 'name'])->with(['activities:id,activity_group_id,name'])->get();
        return $this->sendResponse($model->toArray());
    }

}
