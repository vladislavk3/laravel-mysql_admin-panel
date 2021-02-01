<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\MessageUser;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Activity;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input();
        $filter = $this->set_pageinfo($filter);
        $filter['user_id'] = $request->session()->get(config('radasm.session.user'))->id;

        $activities = Activity::get_recent_list($filter);
        $transactions = Transaction::get_recent_list_user($filter);
        $filter['perPage'] = 5;
        $messages = MessageUser::get_recent_list($filter);

        $data['activities'] = $activities;
        $data['messages'] = $messages;
        $data['transactions'] = $transactions;

        return view('user.index', $data);
    }

    public function getRecentMessageCount(Request $request) {
        $user_id = $request->session()->get(config('radasm.session.user'))->id;
        $count = MessageUser::get_recent_count($user_id);

        return $count;
    }

    public function apply_visa() {
        $country_list = Assessment::get_country_list();

        return view('user.assessment_visa', ['country_list' => $country_list]);
    }

    public function apply_study() {
        $country_list = Assessment::get_country_list();

        return view('user.assessment_study', ['country_list' => $country_list]);
    }

	public function get_one(Request $request) {
		$activity = Activity::get_one($request['id']);
		$message_user = MessageUser::get_one($request['message_id']);

		$data = [
			'result' => 'success',
			'activity' => $activity,
			'admin_message_id' => $message_user->admin_message_id,
		];
		return json_encode($data);
	}
}
