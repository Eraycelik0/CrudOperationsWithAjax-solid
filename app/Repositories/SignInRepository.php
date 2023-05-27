<?php

namespace App\Repositories;

use App\Models\SignIn;

class SignInRepository
{
    public function getAll()
    {
        return SignIn::all();
    }

    public function getById($id)
    {
        return SignIn::find($id);
    }

    public function create($data)
    {
        return SignIn::create($data);
    }

    public function update($id, $data)
    {
        $signIn = SignIn::find($id);
        if ($signIn) {
            $signIn->update($data);
            return $signIn;
        }
        return null;
    }

    public function delete($id)
    {
        $signIn = SignIn::find($id);
        if ($signIn) {
            $signIn->delete();
            return true;
        }
        return false;
    }
}
