<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        $data['title'] = 'User List';
        return view('admin/index');
    }

    //--------------------------------------------------------------------

}
