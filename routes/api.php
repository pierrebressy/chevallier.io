<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

function is_teacher() {
    return Auth::user()->hasRole('teacher');
}

Route::namespace('Api')->group(function () {
    // Public
    Route::get('/', function() {
        return [
            'keywords' => [
                'description' => "Each question may be tagged with keywords",
                'url' => url('/api/keywords'),
            ],
            'students' => [
                'description' => "The list of all students that used the platform",
                'url' => is_teacher() ? url('/api/students') : null,
            ],
            'users' => [
                'description' => "People that have an account on this system",
                'url' => is_teacher() ? url('/api/users') : null,
            ],
            'courses' => [
                'description' => "All courses that have classes of students",
                'url' => is_teacher() ? url('/api/courses') : null,
            ],
            'quizzes' => [
                'description' => "The list of all available quizzes",
                'url' => url('api/quizzes'),
            ],
            'activities' => [
                'description' => "Activities are quizzes served to students as an exam during a T time",
                'url' => url('api/activities'),
            ],
            'rosters' => [
                'description' => "Student roster associated to a course",
                'url' => url('api/rosters'),
            ]
        ];
    });

    Route::get('keywords', 'KeywordController@index');
    Route::get('keywords/{category}', 'KeywordController@with_category');

    // Teacher
    Route::group(['middleware' => 'role:teacher'], function() {
        Route::get('students', 'StudentController@index');

        Route::get('user', 'UserController@me');
        Route::get('user/activities', 'ActivityController@owned');
        Route::get('user/rosters', 'RosterController@owned');

        Route::get('users', 'UserController@index');
        Route::get('users/{id}', 'UserController@show');
        Route::get('users/{id}/activities', 'UserController@activities');
        Route::get('users/{id}/rosters', 'RosterController@owned');

        Route::get('courses', 'CourseController@index');
        Route::get('courses/{id}', 'CourseController@show');

        Route::get('rosters', 'RosterController@index');
        Route::get('rosters/{id}', 'RosterController@show');
        Route::get('rosters/{id}/students', 'RosterController@students');
        Route::get('rosters/{id}/course', 'RosterController@course');
        Route::get('rosters/{id}/activities', 'ActivityController@rosterActivities');

        Route::get('quizzes', 'QuizController@index');
        Route::get('quizzes/{id}', 'QuizController@show');
        Route::get('quizzes/{id}/questions', 'QuizController@questions');
        Route::get('quizzes/{id}/questions/{q}', 'QuizController@question');
        Route::get('quizzes/{id}/activities', 'QuizController@activities');

        Route::get('activities', 'ActivityController@index');
        Route::get('activities/{id}', 'ActivityController@show');
        Route::get('activities/{id}/roster', 'ActivityController@roster');
        Route::get('activities/{id}/quiz', 'ActivityController@quiz');
        Route::get('activities/{id}/progression', 'ActivityController@progression');

        Route::post('activities/create', 'ActivityController@create');

        Route::post('activities/{id}/start', 'ActivityController@start');
        Route::post('activities/{id}/hide', 'ActivityController@set_hidden');
        Route::post('activities/{id}/show', 'ActivityController@set_visible');
        Route::post('activities/{id}/delete', 'ActivityController@delete');
    });

    // Student
    //Route::group(['middleware' => 'role:student'], function() {
        Route::get('activities/{id}/questions', 'ActivityController@questions');
        Route::get('activities/{id}/questions/{question_id}', 'ActivityController@question');
        Route::post('activities/{id}/questions/{question_id}', 'ActivityController@question'); // New answer
    //});


    // Route::get('question/{id}', 'QuizController@index')->name('question');
    // Route::get('quiz/{id}', 'QuizController@getQuiz')->name('quiz');

    // Route::get('activities/answer/{id}', 'ActivityController@getActivityAnswer');
    // Route::get('activities/{activity_id}/{num}', 'ActivityController@getQuestion');
    // Route::get('activities', 'ActivityController@getMyActivities');
    // Route::get('activities/{id}', 'ActivityController@getActivity');
});
