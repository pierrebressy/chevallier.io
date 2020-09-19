<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;
use App\Models\Student;
use App\Models\Roster;
use App\Models\Question;
use App\Models\Answer;
use Auth;

class ActivityController extends Controller
{
    function index() {
        return Activity::where('class.teacher_id', User::id());
    }

    function getActivity() {
        return Activity::with(['quiz','roster'])->find(1);
    }

    function startActivity($id) {
        $act = Activity::with(['answer.student'])->find($id);
        if( $act->teacher->id == Auth::user()->id ){
            //Todo(tmz) Start activity
        }
    }

    function getActivityAnswer($id) {
        //if( Auth::user() ) //Todo(tmz) Check if teacher
        return Activity::with(['answer.student'])->find($id);
    }

    function getQuestion($activity_id, $num) {
        $user_id = Auth::user()->id;
        $user_id = 2; //Todo(tmz) Debug
        $student = Student::where('user_id', '=', $user_id)->first();

        $act = Activity::find($activity_id);
        if(!$act->roster->students->contains('id', $student->id)){
            return;
        }

        if($student && $act->state != 'in_progress'){
            return;
        }

        $nbr_question = $act->quiz->question->count();
        $question_id = $num < $nbr_question ? $num + 1 : 1;

        $ans = Answer::where('activity_id', $activity_id)->
                    where('question_id', $question_id)->
                    where('student_id', $student->id)->
                    first();

        $quest = Question::find($question_id)->toArray();
        $quest['current_answer'] = $ans ? $ans->answer : '';
        return $quest;
    }

    function getMyActivities() {
        $user_id = Auth::user()->id;
        $user_id = 1; //Todo(tmz) Debug
        $student = Student::where('user_id', '=', $user_id)->first();
        $act = [];
        if($student) {
            $classes = $student->rosters;
        }
        else {
            $classes = Roster::all();
        }

        foreach($classes as $cl) {
            $activities = Activity::with(['quiz','roster','teacher'])->
                where('roster_id', '=', $cl->id)->get()->all();
            foreach($activities as $a) {
                array_push($act, $a);
            }
        }
        return $act;
    }
}
