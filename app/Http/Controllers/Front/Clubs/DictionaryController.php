<?php

namespace App\Http\Controllers\Front\Clubs;

use App\Models\Clubs\Club;
use App\Http\Controllers\Front\BaseController;
use App\Models\Clubs\Group;

class DictionaryController extends BaseController
{
    public function __invoke()
    {
        $results = [
            'types' => Club::$types,
            'genders' => Group::$genderDictionary,
        ];
        return $this->sendResponse($results);
    }
}
