<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory;

class TimeEntrySave extends FormRequest
{
    public function __construct(Factory $factory)
    {
        $this->additionalValidation($factory);
    }

    public function additionalValidation(Factory $factory)
    {
        $factory->extend('project_access', function($attribute, $value, $parameters) {
            return Auth::user()
                ->projects()
                ->where('id', request('project_id'))
                ->exists();
        });

        $factory->extend('requirement_access', function($attribute, $value, $parameters) {
            $project = Auth::user()
                ->projects()
                ->find(request('project_id'));

            if ($project) {
                return $project->requirements()
                    ->where('id', request('requirement_id'))
                    ->exists();
            }

            return false;
        });
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'project_id' => 'required|project_access',
            'requirement_id' => 'required|requirement_access',
            'description' => 'required|min:3|max:65534',
        ];

        if (request()->method() == 'PATCH') {
            $rules['time'] = 'required|numeric';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'project_id' => __('form.project'),
            'requirement_id' => __('form.requirement'),
            'description' => __('form.description'),
            'time' => __('form.time'),
        ];
    }

    public function messages()
    {
        return [
            'project_id.project_access' => __('form.no_project_access'),
            'requirement_id.requirement_access' => __('form.no_requirement_access'),
        ];
    }
}
