<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Invite\InviteInterface;
use App\Repositories\Survey\SurveyInterface;
use App\Repositories\Setting\SettingInterface;
use Carbon\Carbon;

class AnswerController extends Controller
{
    protected $surveyRepository;
    protected $inviteRepository;
    protected $settingRepository;

    public function __construct(
        SurveyInterface $surveyRepository,
        InviteInterface $inviteRepository,
        SettingInterface $settingRepository
    ) {
        $this->surveyRepository = $surveyRepository;
        $this->inviteRepository = $inviteRepository;
        $this->settingRepository = $settingRepository;
    }

    private function chart(array $inputs)
    {
        $result = [];

        foreach ($inputs as $key => $value) {
            $results[] = [
                'answer' => $value['content'],
                'percent' => $value['percent'],
            ];
        }

        return $results;
    }

    private function viewChart($token)
    {
        $results = $this->surveyRepository->getResutlSurvey($token);
        $charts = [];

        if (!$results) {
            return [
                'charts' => null,
                'status' => false,
            ];
        }

        foreach ($results as $key => $value) {
            $charts[] = [
                'question' => $value['question'],
                'chart' => ($this->chart($value['answers'])) ?: null,
            ];
        }

        return [
            'charts' => $charts,
            'status' => true,
        ];
    }

    public function answer($token, $view = 'detail', $isPublic = true)
    {
        $survey = $this->surveyRepository
            ->where(($view == 'detail') ? 'token_manage' : 'token', $token)
            ->first();

        if (!$survey || ($view == 'answer' && $survey->feature != $isPublic)) {
            return view('errors.404');
        }

        $access = $this->surveyRepository->getSettings($survey->id);
        $listUserAnswer = $this->showUserAnswer($token);
        $history = ($view == 'answer') ? $this->surveyRepository->getHistory(auth()->id(), $survey->id, ['type' => 'history']) : null;
        $getCharts = $this->viewChart($survey->token);
        $status = $getCharts['status'];
        $charts = $getCharts['charts'];
        $check = $this->surveyRepository->checkSurveyCanAnswer([
            'surveyId' => $survey->id,
            'deadline' => $survey->deadline,
            'status' => $survey->status,
            'type' => $isPublic,
            'userId' => auth()->id(),
            'email' => auth()->check() ? auth()->user()->email : null,
        ]);

        if ($survey) {
            if ($survey->user_id == auth()->id() || $check) {
                $tempAnswers = null;

                return view(($view == 'answer')
                    ? 'user.pages.answer'
                    : 'user.pages.detail-survey', compact(
                        'survey',
                        'status',
                        'charts',
                        'access',
                        'history',
                        'listUserAnswer',
                        'tempAnswers'
                    )
                );
            }
        }

        return redirect()->action('SurveyController@index')
            ->with('message-fail', trans('messages.permisstion',[
                'object' => class_basename(Invite::class),
            ]));
    }

    public function answerPublic($token)
    {
        return $this->answer($token, 'answer', true);
    }

    public function answerPrivate($token)
    {
        return $this->answer($token, 'answer', false);
    }

    public function show($token)
    {
        return $this->answer($token, 'detail');
    }

    public function showUserAnswer($token)
    {
        return $this->surveyRepository->getUserAnswer($token);
    }

    public function showMultiHistory(Request $request, $surveyId, $userId = null, $email = null)
    {
        if (!$request->ajax()) {
            return [
                'success' => false,
            ];
        }

        $survey = $this->surveyRepository->find($surveyId);

        if (!$survey) {
            return action('SurveyController@index')
                ->with('message-fail', trans('messages.load_fail', [
                    'object' => class_basename(Result::class),
            ]));
        }

        $options = [
            'type' => 'result',
            'email' => $email,
        ];

        $history  = $this->surveyRepository->getHistory($userId, $surveyId, $options);

        return [
            'success' => true,
            'data' => view('user.pages.view-result-user', compact('history', 'survey'))->render(),
        ];
    }
}
