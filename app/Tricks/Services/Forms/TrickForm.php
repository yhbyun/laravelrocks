<?php

namespace Tricks\Services\Forms;

class TrickForm extends AbstractForm
{
    /**
     * The validation rules to validate the input data against.
     *
     * @var array
     */
    protected $rules = [
        'title'         => 'required|min:4|unique:tricks,title',
        'slug'          => 'unique:tricks,slug',
        'description'   => 'required|min:10',
        'tags'          => 'required',
        'categories'    => 'required'
    ];

    /**
     * Get the prepared input data.
     *
     * @return array
     */
    public function getInputData()
    {
        return array_only($this->inputData, [
            'title', 'slug', 'description', 'tags', 'categories', 'code', 'draft'
        ]);
    }
}
