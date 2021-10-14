<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        helper('form');

        $userfile = $this->request->getFile('userfile');
        if ($userfile) {
            $qty = service('jyusyojp')->upload($userfile);
            if ($qty) {
                return redirect()
                    ->to('/')
                    ->with('success', $qty . ' data were imported.')
                    ;
            }
        }

        return view('home.html');
    }

    public function upload1()
    {
        $userfile = $this->request->getFile('userfile');
        if ($userfile) {
            $qty = service('jyusyojp')->upload($userfile);
            if ($qty) {
                return redirect()
                    ->to('/')
                    ->with('success', $qty . ' data were imported.')
                    ;
            }
        }
        return redirect()->back();
    }

    public function upload2()
    {
        $userfile = $this->request->getFile('userfile');
        if ($userfile) {
            $qty = service('jyusyojp')->upload2($userfile);
            if ($qty) {
                return redirect()
                    ->to('/')
                    ->with('success', $qty . ' data were imported.')
                    ;
            }
        }
        return redirect()->back();
    }
}
