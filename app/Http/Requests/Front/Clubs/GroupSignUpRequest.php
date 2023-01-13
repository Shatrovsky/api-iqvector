<?php

namespace App\Http\Requests\Front\Clubs;

use App\Http\Requests\SafeFormRequest;
use App\Models\Common\UserEducationProfile;
use App\Models\Schools\Infrastructure;

class GroupSignUpRequest extends SafeFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url' => 'required|string',
            'group_id' => 'nullable|integer|exists:' . config('database.default') . '.clubs.groups,id',
            'teacher_id' => 'nullable|integer|exists:' . config('database.default') . '.clubs.teachers,id',
            'name' => 'required|string',
            'phone' => 'required|string'
        ];
    }
}
