<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class MyModel extends Model
{
    protected $rules = array();

    public function validate($data)
    {
        if ($data === null) {
            return false;
        }

        if ($data && ! is_array($data)) {
            $data = $data->toArray();
        }

        $validator = Validator::make($data, $this->rules);

        return ! $validator->fails();
    }
}
