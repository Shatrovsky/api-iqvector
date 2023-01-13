<?php

namespace App\Http\Controllers\Front\Clubs;

use App\Models\Clubs\Organization;
use App\Http\Controllers\Front\BaseController;
use App\Models\Clubs\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;

class OrganizationController extends BaseController
{
    /**
     * Get full college information
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $model = Organization::query()
            ->with([
                'city:id,name_ru,lat,lng,type',
                'clubs:id,name,organization_id',
                'clubs.activities:id,name,activity_group_id',
                'clubs.activities.activityGroup:id,name',
                'contacts' => function (MorphMany $query) {
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
                'clubs.groups:id,club_id,name,gender,age_from,age_to,paid,cost_per_month,cost_per_lesson,exam,schedule',
                'clubs.groups.teachers:group_id,first_name,last_name,middle_name,description,photo,position,achievements',
                'partner'
            ])->select([
                'id',
                'city_id',
                'address',
                'name',
                'short_name',
                'logo',
                'description',
                'ownership',
                'postal_code',
                'address',
                'street',
                'house',
                'corps',
                'building',
                'letter',
                'partner_type',
                'partner_id'
            ])
            ->findOrFail($id);
        return $this->sendResponse($model->toArray());
    }
}
