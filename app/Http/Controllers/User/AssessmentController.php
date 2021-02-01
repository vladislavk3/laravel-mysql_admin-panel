<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\Activity;
use App\Models\Admission;

class AssessmentController extends Controller
{
    const BASE_PATH = "upload_data/";
    public function register(Request $request)
    {
        //register assessment
        $data = $request->all();
        if ($request->hasFile('resume_filename')){
            // $destinationPath = self::BASE_PATH . $data['first_name'] . " " . $data['last_name'] . "/";
            $destinationPath = base_path('public/' . self::BASE_PATH . "/");
            $filename = $data['file_name'];
            list($name, $extension) = explode('.', $filename);
            $realname = Carbon::now()->timestamp . '.' . $extension;
            $upload_success = $data['resume_filename']->move($destinationPath, $realname);
            $data['resume_filename'] = $filename;
            $data['resume_realname'] = $realname;
        } else {
            $data['resume_filename'] = "";
            $data['resume_realname'] = "";
        }

        $requested_services = "";

        if ($request->exists('educational_consultation') == "1")
            $requested_services .= "1";
        else
            $requested_services .= "0";

        if ($request->exists('apply_admission') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('credential') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('apply_fund') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('apply_accommodations') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('apply_insurance') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('help_permits') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('travel_consultation') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('airport_transfer') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('help_registration') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('help_bank') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        if ($request->exists('help_driving') == "1")
            $requested_services .= ",1";
        else
            $requested_services .= ",0";

        $data['requested_services'] = $requested_services;
        if (!$request->has('indicate'))
            $data['indicate'] = null;

        if (!$request->has('indicate_overall'))
            $data['indicate_overall'] = null;

        if (!$request->has('indicate_writing'))
            $data['indicate_writing'] = null;

        if (!$request->has('indicate_listening'))
            $data['indicate_listening'] = null;

        if (!$request->has('indicate_reading'))
            $data['indicate_reading'] = null;

        if (!$request->has('indicate_speaking'))
            $data['indicate_speaking'] = null;

        if (!$request->has('french_writing'))
            $data['french_writing'] = null;

        if (!$request->has('french_listening'))
            $data['french_listening'] = null;

        if (!$request->has('french_reading'))
            $data['french_reading'] = null;

        if (!$request->has('french_speaking'))
            $data['french_speaking'] = null;

        if (!$request->has('refusal_reason'))
            $data['refusal_reason'] = null;

        $data['user_id'] = $request->session()->get(config('radasm.session.user'))->id;

        Assessment::insert($data);
        //end register assessment

        $last_one = DB::select("select MAX(id) as assessment_id from tbl_assessment t1");
        $last_id = 0;
        if (count($last_one) > 0)
            $last_id = $last_one[0]->assessment_id;
        $last_one = Assessment::get_one($last_id);

        $activity = [];
        $activity['assessment_id'] = $last_id;
        $activity['activity_type'] = 0;
        $activity['status'] = 0;

        if ($last_one->assessment_type==1)  // case Study.
        {
            Activity::insert($activity);

            // register admission
            $admission = [];
            $admission['assessment_id'] = $last_id;

            Admission::insert($admission);
            // end register admission

            return redirect('/');
        }
        else    // case Visa.
        {
            return redirect('policy/visa/'.$last_id);
        }
    }

	public function get_one(Request $request) {
		$assessment = Assessment::get_category($request['id']);

		$data = [
			'result' => 'success',
			'assessment' => $assessment
		];
		return json_encode($data);
	}
}