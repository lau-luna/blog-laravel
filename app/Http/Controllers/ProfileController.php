<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{

    public function edit(Profile $profile)
    {
        $this->authorize('view', $profile);
        
        return view('subscriber.profiles.edit', compact('profile'));
    }


    public function update(ProfileRequest $request, Profile $profile)
    {
        $this->authorize('update', $profile);

        $user = Auth::user();

        if($request->hasFile('photo')){
            //Eliminar foto anterior
            File::delete(public_path('storage/'.$profile->photo));
            //Asignar nueva foto
            $photo = $request['photo']->store('profiles');
        }else{
            $photo = $user->profile->photo;
        }

        //Asignar nombre y correo
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        //Asignar foto
        $user->profile->photo = $photo;

        //Guardar campos de usuario
        $user->save();
        //Guardar campos de perfil
        $user->profile->save();

        return redirect()->route('profiles.edit', $user->profile->id);
    }


}
