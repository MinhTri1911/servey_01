<?php

namespace App\Repositories\Survey;

use DB;
use Exception;
use App\Repositories\Question\QuestionInterface;
use App\Repositories\Like\LikeInterface;
use App\Repositories\Invite\InviteInterface;
use App\Repositories\Setting\SettingInterface;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use App\Models\Survey;

class SurveyRepository extends BaseRepository implements SurveyInterface
{
    protected $likeRepository;
    protected $questionRepository;
    protected $inviteRepository;
    protected $settingRepository;

    public function __construct(
        Survey $survey,
        QuestionInterface $question,
        LikeInterface $like,
        InviteInterface $invite,
        SettingInterface $setting
    ) {
        parent::__construct($survey);
        $this->likeRepository = $like;
        $this->inviteRepository = $invite;
        $this->questionRepository = $question;
        $this->settingRepository = $setting;
    }

    public function delete($ids)
    {
        DB::beginTransaction();
        try {
            $ids = is_array($ids) ? $ids : [$ids];
            $this->inviteRepository->deleteBySurveyId($ids);
            $this->likeRepository->deleteBySurveyId($ids);
            $this->questionRepository->deleteBySurveyId($ids);
            parent::delete($ids);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function getResutlSurvey($token)
    {
        $survey = $this->where('token', $token)->first();

        if (!$survey) {
            return view('errors.503');
        }

        $datasInput = $this->inviteRepository->getResult($survey->id);
        $questions = $datasInput['questions'];
        $temp = [];
        $results = [];

        if (empty($datasInput['results']->toArray())) {
            return $results = false;
        }

        foreach ($questions as $key => $question) {
            $answers = $datasInput['answers']->where('question_id', $question->id);

            foreach ($answers as $answer) {
                $total = $datasInput['results']
                    ->whereIn('answer_id', $answers->pluck('id')
                        ->toArray())
                    ->pluck('id')
                    ->toArray();
                $answerResult = $datasInput['results']
                    ->whereIn('answer_id', $answer->id)
                    ->pluck('id')
                    ->toArray();
                $temp[] = [
                    'answerId' => $answer->id,
                    'content' => ($answer->type == config('survey.type_time')
                        || $answer->type == config('survey.type_text')
                        || $answer->type == config('survey.type_date'))
                        ? $datasInput['results']->whereIn('answer_id', $answer->id)
                        : $answer->content,
                    'percent' => (count($total) > 0) ? (double)(count($answerResult)*100)/(count($total)) : 0,
                ];
            }
            $results[] = [
                'question' => $question,
                'answers' => $temp,
            ];
            $temp = [];
        }

        return $results;
    }

    public function createSurvey(
        $inputs,
        array $settings,
        array $arrayQuestionWithAnswer,
        array $questionsRequired,
        array $images
    ) {
        $surveyInputs = $inputs->only([
            'user_id',
            'mail',
            'title',
            'feature',
            'token',
            'token_manage',
            'status',
            'deadline',
            'description',
        ]);

        $surveyInputs['feature'] = ($inputs['feature'])
            ? config('settings.feature')
            : config('settings.not_feature');
        $surveyInputs['status'] = (Carbon::parse($inputs['deadline'])->gt(Carbon::now()) || (empty($inputs['deadline'])))
            ? config('survey.status.avaiable')
            : config('survey.status.block');
        $surveyInputs['deadline'] = ($inputs['deadline'])
            ? Carbon::parse($inputs['deadline'])->format('Y/m/d H:i')
            : null;
        $surveyInputs['created_at'] = $surveyInputs['updated_at'] = Carbon::now();
        $surveyId = parent::create($surveyInputs->toArray());

        if (!$surveyId) {
            return false;
        }

        //(1,5) That is the settings quantity for a survey
        foreach (range(1, 5) as $key) {
            if(!array_has($settings, $key)) {
                $settings[$key] = '';
            }
        }

        $this->settingRepository->createMultiSetting($settings, $surveyId);
        $txtQuestion = $arrayQuestionWithAnswer;
        $questions = $txtQuestion['question'];
        $answers = $txtQuestion['answers'];
        $this->questionRepository
            ->createMultiQuestion(
                $surveyId,
                $questions,
                $answers,
                $images,
                $questionsRequired
            );

        return $surveyId;
    }

    public function checkCloseSurvey($inviteIds, $surveyIds)
    {
        $ids = array_merge(
            $inviteIds->lists('survey_id')->toArray(),
            $surveyIds->lists('id')->toArray()
        );

        return $this->settingRepository
            ->whereIn('survey_id', $ids)
            ->where([
                'key' => config('settings.key.limitAnswer'),
                'value' => 0,
            ])
            ->lists('survey_id')
            ->toArray();
    }

    public function listsSurvey($userId, $email = null)
    {
        $invites = $inviteIds = $this->inviteRepository
            ->where('recevier_id', $userId)
            ->orWhere('mail', $email);
        $surveys = $surveyIds = $this->where('user_id', $userId)->orWhere('mail', $email);
        $settings = $this->checkCloseSurvey($inviteIds, $surveyIds);
        $invites = $invites
            ->orderBy('id', 'desc')
            ->paginate(config('settings.paginate'));
        $surveys = $surveys
            ->orderBy('id', 'desc')
            ->paginate(config('settings.paginate'));

        return compact('surveys', 'surveys', 'settings');
    }

    public function checkSurveyCanAnswer(array $inputs)
    {
        $date = ($inputs['deadline']) ? Carbon::parse($inputs['deadline'])->gt(Carbon::now()) : true;
        $invite = true;
        $email = $inputs['email'];
        $surveyId = $inputs['surveyId'];

        if (!$date || !$inputs['status']) {
            return false;
        } elseif (!$inputs['type']) {
            $invite = $this->inviteRepository
                ->where('recevier_id', $inputs['userId'])
                ->where('survey_id', $inputs['surveyId'])
                ->orWhere(function ($query) use ($email, $surveyId) {
                    $query->where('mail', $email)->where('survey_id', $surveyId);
                })
                ->exists();
        }

        return $invite;
    }

    public function getSettings($surveyId)
    {
        if (!$surveyId) {
            return [];
        }

        return $this->settingRepository
            ->where('survey_id', $surveyId)
            ->lists('value', 'key')
            ->toArray();
    }

    public function getHistory($userId, $surveyId, array $options)
    {
        if (!$userId && $options['type'] == 'history' || !$surveyId) {
            return [];
        }

        if ($options['type'] == 'history') {
            $results = $this->questionRepository
                ->getResultByQuestionIds($surveyId)
                ->where('sender_id', $userId)
                ->get();
        } else {
            $email = $options['email'];
            $results = $this->questionRepository
                ->getResultByQuestionIds($surveyId)
                ->where(function($query) use ($userId, $email) {
                    $query->where('sender_id', $userId)
                        ->orWhere('email', $email ?: config('settings.email_unidentified'));
                })
                ->get()
                ->toArray();
            $collection = collect($results);

            return $collection->groupBy('created_at')->toArray();
        }

        if (!$results) {
            return [];
        }

        $history = [];
        $maxCreate = $results->max('created_at');

        foreach ($results as $key => $value) {
            if ($options['type'] == 'history' && $value->created_at == $maxCreate) {
                $history[$value->answer_id] = $value->content;
            }
        }

        return $history;
    }

    public function getUserAnswer($token)
    {
        $survey = $this->where('token', $token)->orWhere('token_manage', $token)->first();

        if (!$survey) {
            return false;
        }

        $results = $this->questionRepository
            ->getResultByQuestionIds($survey->id);
        $results = $results->distinct('created_at')->get([
            'created_at',
            'name',
            'email',
            'sender_id',
        ])
        ->toArray();
        /*
            Get all user answer survey and group by user id.
            Sender_id can be null.
        */
        $collection = collect($results)->groupBy('sender_id')->toArray();
        //  Get users login when anwser survey with key = user id.
        $userLogin = collect($collection)->except([""])->toArray();
        /*
            Get users not login when answer survey and group by email.
            Email can be set default because user don't need enter email.
        */
        $userNotLogin = collect($collection[""])->groupBy('email')->toArray();

        return array_merge($userLogin, $userNotLogin);
    }
}
