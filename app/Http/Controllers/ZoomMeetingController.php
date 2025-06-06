<?php

namespace App\Http\Controllers;

use App\Traits\ZoomMeetingTrait;
use App\Models\ZoomMeeting as LocalZoomMeeting;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Spatie\GoogleCalendar\Event as GoogleEvent;

class ZoomMeetingController extends Controller
{

    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL = "https://api.zoom.us/v2/";


    public function index()
    {
        if (\Auth::user()->can('Manage Zoom meeting')) {
            if (\Auth::user()->type == 'company' || \Auth::user()->type == 'hr') {
                $created_by = \Auth::user()->creatorId();
                $ZoomMeetings = LocalZoomMeeting::where('created_by', $created_by)->get();
                // $this->statusUpdate();
                return view('zoom_meeting.index', compact('ZoomMeetings'));
            } elseif (\Auth::user()->type == 'employee') {
                $created_by = \Auth::user()->creatorId();
                $ZoomMeetings = LocalZoomMeeting::where('created_by', $created_by)->get();
                foreach ($ZoomMeetings as $ZoomMeeting) {
                    $ids = $ZoomMeeting->user_id;
                    $ids = explode(',', $ids);
                    if (in_array(\Auth::user()->id, $ids)) {
                        $m_ids[] = $ZoomMeeting->id;
                    }
                }

                $ZoomMeetings = LocalZoomMeeting::where('created_by', $created_by)->whereIn('id', $m_ids)->get();

                // Fetch zoom meetings for the employee


                // $this->statusUpdate();
                return view('zoom_meeting.index', compact('ZoomMeetings'));
            } else {
                $created_by = Auth::user()->creatorId();
                $ZoomMeetings = LocalZoomMeeting::where('user_id', \Auth::user()->id)->get();
                // $this->statusUpdate();
                return view('zoom_meeting.index', compact('ZoomMeetings'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('Create Zoom meeting')) {
            $created_by = \Auth::user()->creatorId();
            $employee_option = User::where('created_by', $created_by)->pluck('name', 'id');
            return view('zoom_meeting.create', compact('employee_option'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Zoom meeting')) {
            $settings = \App\Models\Utility::settings();

            if ($settings['zoom_account_id'] != "" && $settings['zoom_client_id'] != "" && $settings['zoom_client_secret'] != "") {

                $data['topic'] = $request->title;
                $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
                $data['duration'] = (int)$request->duration;
                $data['password'] = $request->password;
                $data['host_video'] = 0;
                $data['participant_video'] = 0;
                try {
                    $meeting_create = $this->createmitting($data);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', __('Invalid access token.'));
                }

                \Log::info('Meeting');
                \Log::info((array)$meeting_create);
                if (isset($meeting_create['success']) &&  $meeting_create['success'] == true) {
                    $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
                    $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
                    $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
                    $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';

                    $created_by = \Auth::user()->creatorId();
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'title' => 'required',
                            'user_id' => 'required',
                            'start_date' => 'required',
                            'duration' => 'required',
                        ]
                    );

                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }

                    $user_id = 0;
                    if (!empty($request->user_id)) {
                        $user_id = implode(',', $request->user_id);
                    }


                    $ZoomMeeting = new LocalZoomMeeting();
                    $ZoomMeeting->title = $request->title;
                    $ZoomMeeting->meeting_id = $meeting_id;
                    $ZoomMeeting->user_id = $user_id;
                    $ZoomMeeting->password = $request->password;
                    $ZoomMeeting->join_url = $join_url;
                    $ZoomMeeting->start_date = $request->start_date;
                    $ZoomMeeting->duration = $request->duration;
                    $ZoomMeeting->start_url = $start_url;
                    $ZoomMeeting->status = $status;
                    $ZoomMeeting->created_by = $created_by;

                    $ZoomMeeting->save();

                    // Google celander
                    if ($request->get('synchronize_type')  == 'google_calender') {

                        $type = 'zoom_meeting';
                        $request1 = new GoogleEvent();
                        $request1->title = $request->title;
                        $request1->start_date = $request->start_date;
                        $request1->end_date = $request->start_date;

                        Utility::addCalendarData($request1, $type);
                    }

                    return redirect()->back()->with('success', __('Meeting created successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Meeting not created.'));
                }
            } else {
                return redirect()->back()->with('error', __('Please Add Zoom Settings'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(LocalZoomMeeting $ZoomMeeting)
    {
        if (\Auth::user()->can('Show Zoom meeting')) {
            if ($ZoomMeeting->created_by == \Auth::user()->creatorId()) {
                $ids = $ZoomMeeting->user_id;
                $ids = explode(',', $ids);
                $ids = User::whereIn('id', $ids)->pluck('name')->toArray();
                $names = implode(', ', $ids);

                return view('zoom_meeting.view', compact('ZoomMeeting', 'names'));
            } else {
                return redirect()->back()->with('error', 'permission Denied');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit(LocalZoomMeeting $ZoomMeeting)
    {
        $created_by = \Auth::user()->creatorId();
        $employee_option = User::where('created_by', $created_by)->pluck('name', 'id');
        return view('zoom_meeting.edit', compact('employee_option', 'ZoomMeeting'));
    }


    public function update(Request $request, LocalZoomMeeting $ZoomMeeting)
    {
        $created_by = \Auth::user()->creatorId();
        $validator = \Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'user_id' => 'required',
                // 'password' => 'required',
                'start_date' => 'required',
                'duration' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $ZoomMeeting = new LocalZoomMeeting();
        $ZoomMeeting->title = $request->title;
        $ZoomMeeting->user_id = $request->user_id;
        $ZoomMeeting->password = $request->password;
        $ZoomMeeting->start_date = $request->start_date;
        $ZoomMeeting->duration = $request->duration;
        $ZoomMeeting->created_by = $created_by;

        $ZoomMeeting->save();
        return redirect()->back()->with('success', __('Zoom Meeting update Successfully'));
    }


    public function destroy(LocalZoomMeeting $ZoomMeeting)
    {
        if (\Auth::user()->can('Delete Zoom meeting')) {
            $ZoomMeeting->delete();
            return redirect()->back()->with('success', __('Zoom Meeting Delete Succsefully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function statusUpdate()
    {
        $meetings = LocalZoomMeeting::where('created_by', \Auth::user()->id)->pluck('meeting_id');
        foreach ($meetings as $meeting) {
            $data = $this->get($meeting);
            if (isset($data['data']) && !empty($data['data'])) {
                $meeting = LocalZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
            }
        }
    }

    public function calender()
    {
        $created_by = Auth::user()->creatorId();
        $ZoomMeetings = LocalZoomMeeting::where('created_by', $created_by)->get();
        $today_date = date('m');

        if (\Auth::user()->type == 'employee') {
            // Fetch zoom meetings for the employee within the current month
            $current_month_event = LocalZoomMeeting::where('created_by', \Auth::user()->creatorId())
                ->where('user_id', \Auth::user()->id)
                ->select('id', 'start_date', 'title', 'created_at')
                ->whereRaw('MONTH(start_date) = ?', [$today_date])
                ->get();
        } else {
            // Fetch zoom meetings for non-employees within the current month
            $current_month_event = LocalZoomMeeting::where('created_by', \Auth::user()->creatorId())
                ->select('id', 'start_date', 'title', 'created_at')
                ->whereRaw('MONTH(start_date) = ?', [$today_date])
                ->get();
        }
        $arrMeeting = [];

        foreach ($ZoomMeetings as $zoommeeting) {
            $arr['id']        = $zoommeeting['id'];
            $arr['title']     = $zoommeeting['title'];
            $arr['start']     = date('Y-m-d', strtotime($zoommeeting['start_date']));
            //  $arr['start']       =date('Y-m-d',strtotime($zoommeeting['start_date'])).'T'.date('h:m:s',strtotime($zoommeeting['start_date']));
            $arr['className'] = 'event-primary';
            $arr['url']       = route('zoom-meeting.show', $zoommeeting['id']);
            $arrMeeting[]        = $arr;
        }
        $calandar = array_merge($arrMeeting);
        //$calandar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));
        $calandar =  json_encode($calandar);

        return view('zoom_meeting.calendar', compact('calandar', 'current_month_event'));
    }

    public function get_zoom_meeting_data(Request $request)
    {
        $arrayJson = [];
        if ($request->get('calender_type') == 'google_calender') {
            $type = 'zoom_meeting';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            if (\Auth::user()->type == 'employee') {
                $data = LocalZoomMeeting::where('created_by', \Auth::user()->creatorId())->where('user_id', \Auth::user()->id)->get();
            } else {
                $data = LocalZoomMeeting::where('created_by', \Auth::user()->creatorId())->get();
            }

            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->title,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "allDay" => true,
                    "url" => route('zoom-meeting.show', $val['id']),
                ];
            }
        }

        return $arrayJson;
    }
}
