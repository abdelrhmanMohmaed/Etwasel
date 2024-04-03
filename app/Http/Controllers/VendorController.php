<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ContactsUser;
use App\Models\IndustriesField;
use App\Models\User;
use App\Models\UsersDetail;
use App\Models\UsersEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class VendorController extends Controller
{
    public function vendor_profile(Request $request)
    {
        $categories = Category::all();
        $fields = IndustriesField::all();

        return view('front.profile', compact('categories', 'fields'));
    }
    public function vendor_update_profile(Request $request)
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $userDetails = UsersDetail::where('user_id', $user->id)->first();
        if ($userDetails) {
            if ($request->file('logo')) {
                $oldLogo = 'users-details/logo/' . $userDetails->logo;
                File::delete($oldLogo);
                $fileName = $request->file('logo')->getClientOriginalName();
                $newName = time() . "-" . $fileName;

                $img_path = 'users-details/logo';
                $request->file('logo')->move($img_path, $newName);
                $userDetails->update([
                    'logo' => $newName,
                ]);
            }

            $userDetails->update([
                'campany_name_ar' => $request->campany_name_en,
                'campany_name_en' => $request->campany_name_ar,
                'category_id' => $request->campany_category_id,
                'industry_field_id' => $request->industry_field_id,
                'location_url' => $request->location_url,
                'address' => $request->address,
                'city_id' => $request->city_id,
                'created_at' => $request->start_date,
                'campany_about' => $request->campany_about,
                'campany_about_ar' => $request->campany_about_ar,
            ]);
        } else {

            if ($request->file('logo')) {
                $fileName = $request->file('logo')->getClientOriginalName();
                $newName = time() . "-" . $fileName;
                $img_path = 'users-details/logo';
                $request->file('logo')->move($img_path, $newName);
            }
            //start CreateOrupdate logo and delete the old logo
            UsersDetail::create([
                'user_id' => $user_id,
                'logo' => $newName ?? null,
                'campany_name_ar' => $request->campany_name_en,
                'campany_name_en' => $request->campany_name_ar,
                'category_id' => $request->campany_category_id,
                'industry_field_id' => $request->industry_field_id,
                'location_url' => $request->location_url,
                'address' => $request->address,
                'city_id' => $request->city_id,
                'created_at' => $request->start_date,
                'campany_about' => $request->campany_about,
                'campany_about_ar' => $request->campany_about_ar,
            ]);
        };
        //end CreateOrupdate logo and delete the old logo
        //start create slide  
        if ($request->hasFile('slide')) {
            $files = $request->file('slide');
            foreach ($files as $file) {
                $img_path = 'users-details/sliders';
                $fileName = $file->getClientOriginalName();
                $newName = time() . "-" . $fileName;
                $file->move($img_path, $newName);
                $user->sliders()->create([
                    'user_id' => $user_id,
                    'image' => $newName
                ]);
            }
        };
        //end update slide  
        //start emails and phones 
        if ($request->has('contacts')) {
            if (!empty($user->contacts))
                foreach ($user->contacts as $con)
                    $con->delete();
            foreach ($request['contacts'] as $key => $contact)
                if ($contact['contact'] != null)
                    ContactsUser::create(['contact' => $contact['contact'], 'user_id' => $user_id]);
        }
        if ($request->has('emails')) {
            if (!empty($user->emails))
                foreach ($user->emails as $em)
                    $em->delete();
            foreach ($request['emails'] as $key => $email)

                if ($email['email'] != null)
                    UsersEmail::create(['email' => $email['email'], 'user_id' => $user_id]);
        }
        //end emails and phones 
        //start create certification  
        if ($request->hasFile('certification')) {
            $files = $request->file('certification');
            foreach ($files as $file) {
                $img_path = 'users-details/certification';
                $fileName = $file->getClientOriginalName();
                $newName = time() . "-" . $fileName;
                $file->move($img_path, $newName);
                $user->certification()->create([
                    'user_id' => $user_id,
                    'image' => $newName
                ]);
            }
        };
        //end update certification
        return  redirect()->back()->with('message', 'Profile Updated Successfully');
    }
}
