<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TimeEntrySave extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::check()) {
            $project = Auth::user()
                ->projects()
                ->find(request('project_id'));

            if ($project) {
                return $project->requirements()
                    ->where('id', request('requirement_id'))
                    ->exists();
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'requirement_id' => 'required|exists:requirements,id',
            'description' => 'required|min:3|max:65534',
        ];
    }

    public function attributes()
    {
        return [
            'project_id' => __('form.project'),
            'requirement_id' => __('form.requirement'),
            'description' => __('form.description'),
        ];
    }
}
