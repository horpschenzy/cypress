<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::where('role', 'USER')->count();
        $activityCount = Activity::whereNull('activity_id')->count();
        return view('pages.dashboard', compact('userCount', 'activityCount'));
    }
    
    public function activities()
    {
        $users = User::where('role', 'USER')->get();
        $activities = Activity::where('event_title', '>=', date('Y-m-d'))->orderBy('start_date')->limit(5)->get();
        return view('pages.activities', compact('users', 'activities'));
    }

    public function addActivity(Request $request)
    {
        // VALIDATE ACTIVITY REQUEST
        $validate  = Validator::make($request->all(), [
            'event_title' => ['required', 'string'],
            'user' => ['required'],
            'event_image' => ['required', 'mimes:jpg,bmp,png', 'max:5000'],
            'description' => ['string'],
            'start_date' => ['string'],
        ]);

        if($validate->fails()){
            $notification = array(
                'message' => $validate->messages()->first(),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification)->withInput();
        }

        //CHECK IF ACTIVITY ADDED IS MORE THAN 4
        $countActivity = Activity::where('start_date', $request->start_date)->count();
        if ($countActivity >= 4) {
            $notification = array(
                'message' => 'Activity Can not be saved. Maximum entries of (4) reached!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // SAVE ACTIVITY
        DB::beginTransaction();
        try {
            $activity = new Activity();
            $activity->event_title  = $request->event_title;
            $activity->description = $request->description;
            $activity->user = $request->user;
            if ($request->hasFile('event_image')) {
                $event_image =  strtotime(date('y-m-d h:i:s')) . '.png';
                Image::make($request->event_image)->save(public_path('assets/images/events/') . $event_image);
                $activity->event_image = '/assets/images/events/'.$event_image;
            }
            $activity->start_date = $request->start_date;
            if ($activity->save()) {
                if ($request->user != 'ALL USERS') {
                    $userIds = $request->user;
                } else {
                    $userIds = User::where('role', 'USER')->get()->pluck('id');
                }
                
                // ATTACH USERS TO ACTIVITY
                $activity->users()->sync($userIds);

                $notification = array(
                    'message' => 'Activity saved successfully!',
                    'alert-type' => 'success'
                );
            } else {
                $notification = array(
                    'message' => 'Activity Can not be saved. Please try again later!',
                    'alert-type' => 'error'
                );
            }
            DB::commit();
            return back()->with($notification);
        } catch (\Exception $e) {
            DB::rollback();
            $notification = array(
                'message' => 'Internal Server Error!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }
    }

    public function fetchActivities()
    {
        $data = Activity::whereDate('start_date', '>=', request()->start)
        ->whereDate('start_date',   '<=', request()->end)
        ->whereNull('activity_id')
        ->get(['id', 'event_title as title', 'start_date as start', 'description', 'event_image', 'user']);
        return response()->json($data);
    }

    public function editActivityDate(Request $request)
    {
       $activity = Activity::find($request->id);
       $activity->start_date = $request->start;
       $activity->save();
    }
    
    public function editActivity(Request $request)
    {
       $activity = Activity::find($request->id);
       $activity->event_title = $request->event_title;
       $activity->description = $request->description;
       $activity->user = $request->user;
       if ($request->hasFile('event_image')) {
            unlink(public_path($activity->event_image));
            $event_image =  strtotime(date('y-m-d h:i:s')) . '.png';
            Image::make($request->event_image)->save(public_path('assets/images/events/') . $event_image);
            $activity->event_image = '/assets/images/events/'.$event_image;
        }
        if ($activity->save()) {
            if ($request->user != 'ALL USERS') {
                $userIds = $request->user;
                $activity->users()->sync($userIds);
            }
            $notification = array(
                'message' => 'Activity Edit successfully!',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Activity can not be edited. Please try again!',
                'alert-type' => 'success'
            );
        }
        return back()->with($notification);

    }
    
    public function deleteActivity(Request $request)
    {
       $activity = Activity::find($request->id);
       unlink(public_path($activity->event_image));
        if ($activity->delete()) {
            return true;
        }
        return false;
    }
    
    public function deleteUserActivity(Request $request)
    {
       $deleteActivity = DB::table('activity_user')->where('user_id', $request->user_id)
                        ->where('activity_id', $request->id)->delete();
        if ($deleteActivity) {
            return true;
        }
        return false;
    }

    public function editUserActivity(Request $request)
    {
       $activity = Activity::find($request->id);
       if ($activity->user == 'ALL USERS') {
            $newActivity = new Activity();
            $newActivity->event_title  = $request->event_title;
            $newActivity->description = $request->description;
            $newActivity->event_image = $activity->event_image;
            $newActivity->start_date = ($request->start_date) ?: $activity->start_date;
            $newActivity->user = $request->user;
            if ($request->hasFile('event_image')) {
                $event_image =  strtotime(date('y-m-d h:i:s')) . '.png';
                Image::make($request->event_image)->save(public_path('assets/images/events/') . $event_image);
                $newActivity->event_image = '/assets/images/events/'.$event_image;
            }
            $edit = $newActivity->save();
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['id' => $request->id]);
            $myRequest->request->add(['user_id' => $request->user]);
            $this->deleteUserActivity($myRequest);
            $newActivity->users()->sync($request->user);

       } else {
            $activity->event_title = $request->event_title;
            $activity->description = $request->description;
            $activity->user = $request->user;
            if ($request->hasFile('event_image')) {
                unlink(public_path($activity->event_image));
                $event_image =  strtotime(date('y-m-d h:i:s')) . '.png';
                Image::make($request->event_image)->save(public_path('assets/images/events/') . $event_image);
                $activity->event_image = '/assets/images/events/'.$event_image;
            }
            $edit = $activity->save();
       }
        if ($edit) {
            $notification = array(
                'message' => 'Activity Edit successfully!',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Activity Edit successfully!',
                'alert-type' => 'success'
            );
        }
        return back()->with($notification);

    }
    
    public function logout()
    {
        Auth::logout();
        $notification = array(
            'message' => 'Logout Successfully!',
            'alert-type' => 'success'
        );
        return redirect('/')->with($notification);
    }
}
