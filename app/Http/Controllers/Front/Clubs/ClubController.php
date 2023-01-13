<?php

namespace App\Http\Controllers\Front\Clubs;

use App\Models\Clubs\Club;
use App\Http\Controllers\Front\BaseController;
use App\Models\Clubs\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;

class ClubController extends BaseController
{
    /**
     * Get full college information
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $model = Club::query()
            ->with([
                'favorite',
                'activities:id,name,activity_group_id',
                'activities.activityGroup:id,name',
                'organization:id,name,address,city_id,logo,description,ownership,partner_type,partner_id,postal_code,address,street,house,corps,building,letter',
                'organization.partner',
                'organization.city:id,name_ru,lat,lng',
                'organization.contacts' => function (MorphMany $query) {
                    $query->whereHas('contact', function ($query) {
                        $query->whereIn('status', [Contact::STATUS_PUBLIC]);
                    }
                    )->with(['contact:id,type,content,person,description'])
                        ->select([
                            'contact_id',
                            'essence_type',
                            'essence_id'
                        ]);
                },
                'organization.partner',
                'groups:id,club_id,name,gender,age_from,age_to,paid,cost_per_month,cost_per_lesson,intro_lesson,exam,description,city_id,address,schedule,photos,postal_code,address,street,house,corps,building,letter',
                'groups.city:id,name_ru,lat,lng,type',
                'groups.teachers:id,first_name,last_name,middle_name,description,photo,position,achievements',
                'groups.contacts' => function (MorphMany $query) {
                    $query->whereHas('contact', function ($query) {
                        $query->whereIn('status', [Contact::STATUS_PUBLIC]);
                    }
                    )->with(['contact:id,type,content,person,description'])
                        ->select([
                            'contact_id',
                            'essence_type',
                            'essence_id'
                        ]);
                },
            ])->select([
                'id',
                'organization_id',
                'type',
                'name',
                'short_name',
                'description',
                'achievements',
                'photos',
            ])
            ->findOrFail($id);
        return $this->sendResponse($model->toArray());
    }
}
