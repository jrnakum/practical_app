<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkExperience;
use App\Models\TechnicalExperience;
use App\Models\LanguageKnown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FormApplicationController extends Controller
{
    public function showApplicationForm()
    {
        return view('web.create');
    }

    public function showApplicationFormPost(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:users|email',
            'address' => 'required',
            'gender' => 'required',
            'contact' => 'required',
            'education_detail' => 'required',
            'preferred_location' => 'required',
            'expected_ctc' => 'required',
            'current_ctc' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }
        $user = $this->saveUsersData($request->all());

        if ($user) {
            $message = config('params.msg_success') . ' Success to submit application' . config('params.msg_success');
            $request->session()->flash('message', $message);
            return view('web.acknowledgement');
        }
    }

    public function addMoreDiv()
    {
        return view('web.newappend')->render();
    }

    public function saveUsersData($data)
    {
        $userdetail = $this->userDetail($data);
        $we_array = $this->workExperience($data);
        $te_array = $this->technicalExperience($data);
        $lang = $this->langaugeKnown($data);
        $user =  User::create($userdetail);
        if ($lang) {
            $user->rel_language()->saveMany($lang);
        }
        if ($te_array) {
            $user->rel_technicalex()->saveMany($te_array);
        }
        $user->rel_workex()->saveMany($we_array);
        return $user;
    }

    public function userDetail($data)
    {
        return $array =
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'address' => $data['address'],
                'gender' => $data['gender'],
                'contact' => $data['contact'],
                'preferred_location' => $data['preferred_location'],
                'education_detail' => $data['education_detail'],
                'current_ctc' => $data['current_ctc'],
                'expected_ctc' => $data['expected_ctc'],
                'notice_period' => $data['notice_period'],
            ];
    }

    public function langaugeKnown($data)
    {
        $lang_array = array();
        if (isset($data['english'])) {
            $lang = new LanguageKnown;
            $lang->language = 'english';
            if (isset($data['english']['read'])) {
                $lang->read = 1;
            }
            if (isset($data['english']['write'])) {
                $lang->write = 1;
            }
            if (isset($data['english']['speak'])) {
                $lang->speak = 1;
            }
            $lang_array[]  = $lang;
        }
        if (isset($data['hindi'])) {
            $lang = new LanguageKnown;
            $lang->language = 'hindi';
            if (isset($data['hindi']['read'])) {
                $lang->read = 1;
            }
            if (isset($data['hindi']['write'])) {
                $lang->write = 1;
            }
            if (isset($data['hindi']['speak'])) {
                $lang->speak = 1;
            }
            $lang_array[]  = $lang;
        }
        if (isset($data['gujarati'])) {
            $lang = new LanguageKnown;
            $lang->language = 'gujarati';
            if (isset($data['gujarati']['read'])) {
                $lang->read = 1;
            }
            if (isset($data['gujarati']['write'])) {
                $lang->write = 1;
            }
            if (isset($data['gujarati']['speak'])) {
                $lang->speak = 1;
            }
            $lang_array[]  = $lang;
        }
        return $lang_array;
    }

    public function workExperience($data)
    {
        $we_array = array();
        if ($data['company_name']) {
            foreach ($data['company_name']  as $k => $d) {
                $we = new WorkExperience;
                $we->company_name = $data['company_name'][$k] ?? null;
                $we->duration = $data['duration'][$k] ?? null;
                $we->location = $data['location'][$k] ?? null;
                $we->designation = $data['designation'][$k] ?? null;
                $we_array[] = $we;
            }
        }
        return $we_array;
    }

    public function technicalExperience($data)
    {
        $te_array = array();
        if (isset($data['skill']) && $data['skill']) {
            foreach ($data['skill']  as $k => $d) {
                $te = new TechnicalExperience;
                $te->skill = $k ?? null;
                $te->stage = isset($d) ? $d : null;
                $te_array[] = $te;
            }
        }
        return $te_array;
    }
}
