<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\ApiController;
use App\Section;
use Illuminate\Http\Request;

class SectionController extends ApiController
{
    // get all sections
    public function index()
    {
        $sections = Section::all();
        return $this->showAll($sections);
    }

    // show a specific section
    public function show(Section $section)
    {
        return $this->showOne($section);
    }
}
