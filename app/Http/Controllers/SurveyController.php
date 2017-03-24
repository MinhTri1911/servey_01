<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Survey\SurveyInterface;
use App\Repositories\Question\QuestionInterface;
use App\Repositories\Invite\InviteInterface;
use App\Repositories\Setting\SettingInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\SendMail;
use Carbon\Carbon;
use Mail;
use DB;

class SurveyController extends Controller
{
    use DispatchesJobs;

    protected $surveyRepository;
    protected $questionRepository;
    protected $inviteRepository;
    protected $settingRepository;

    public function __construct(
        SurveyInterface $surveyRepository,
        QuestionInterface $questionRepository,
        InviteInterface $inviteRepository,
        SettingInterface $settingRepository
    ) {
        $this->surveyRepository = $surveyRepository;
        $this->questionRepository = $questionRepository;
        $this->inviteRepository = $inviteRepository;
        $this->settingRepository = $settingRepository;
    }

    public function index()
    {
        return view('user.pages.home');
    }

    public function checkCloseSurvey($inviteIds, $surveyIds)
    {
        $ids = array_merge(
            $inviteIds->lists('survey_id')->toArray(),
            $surveyIds->lists('id')->toArray()
        ); // đưa 2 cái survey của mình vs survey đcinvite thành mảng các ids

        return $this->settingRepository // kiểm tra trong bảng settings có survey =ids vafkey ==...
            ->whereIn('survey_id', $ids)
            ->where([
                'key' => config('settings.key.limitAnswer'),
                'value' => 0,
            ])
            ->lists('survey_id')
            ->toArray();
    }

    public function listSurveyUser()
    {
        $invites = $inviteIds = $this->inviteRepository  // tất cả survey được mời o day
            ->where('recevier_id', auth()->id())
            ->orWhere('mail', auth()->user()->email);
        $surveys = $surveyIds = $this->surveyRepository
            ->where('user_id', auth()->id()); // tất cả survey cua minh
        $settings = $this->checkCloseSurvey($inviteIds, $surveyIds);// kiêm tra survey
        $invites = $invites
            ->orderBy('id', 'desc')
            ->paginate(config('settings.paginate'));
        $surveys = $surveys
            ->orderBy('id', 'desc')
            ->paginate(config('settings.paginate'));

        return view('user.pages.list-survey', compact('surveys', 'invites', 'settings'));
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            return [
                'success' => false,
            ];
        }

        $idSurvey = $request->get('idSurvey');
        $this->surveyRepository->delete($idSurvey);

        return [
            'success' => true,
        ];
    }

    public function show($token)
    {
        $surveys = $this->surveyRepository->where('token', $token)->first();

        if (!$surveys) {
            return view('errors.404');
        }

        return view('user.pages.answer', compact('surveys'));
    }

    public function updateSurvey(Request $request, $id)
    {
        $survey = $this->surveyRepository->find($id);
        $isSuccess = false;
        $data = $request->only([
            'title',
            'description',
        ]);

        if ($request->get('deadline')) {
            $data['deadline'] = Carbon::parse($request->get('deadline'))->format('Y/m/d H:i');
        }

        if ($survey) {
            DB::beginTransaction();
            try {
                $isSuccess = $this->surveyRepository->update($id, $data);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
            }
        }

        return redirect()->action('AnswerController@show', $survey->token_manage)
            ->with(($isSuccess) ? 'message' : 'message-fail', ($isSuccess)
                ? trans('messages.object_updated_successfully', [
                    'object' => class_basename(Survey::class),
                ])
                : trans('messages.object_updated_unsuccessfully', [
                    'object' => class_basename(Survey::class)
                ])
            );
    }

    public function updateSurveyContent(Request $request, $surveyId, $token)
    {
        DB::beginTransaction();
        try {
            $inputs = $request->only([
                'txt-question',
                'checkboxRequired',
                'required-question',
                'image',
                'del-question',
                'del-answer',
                'del-question-image',
                'del-answer-image',
            ]);
            $this->questionRepository->updateSurvey($inputs, $surveyId);
            DB::commit();

            return redirect()->action('AnswerController@show', $token)->with('message', trans('messages.object_updated_successfully', [
                'object' => class_basename(Survey::class)
            ]));
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->action('AnswerController@show', $token)->with('message-fail', trans('messages.object_updated_unsuccessfully', [
                'object' => class_basename(Survey::class)
            ]));
        }
    }

    public function create(Request $request)
    {
        $value = $request->only([
            'title',
            'feature',
            'deadline',
            'description',
            'txt-question',
            'checkboxRequired',
            'email',
            'emails',
            'setting',
            'name',
            'image',
        ]);

        if (!strlen($value['title'])) {
            $value['title'] = config('survey.title_default');
        }

        $token = md5(uniqid(rand(), true));
        $tokenManage = md5(uniqid(rand(), true));

        DB::beginTransaction();
        try {
            $inputs = collect([
                'user_id' => (auth()->id()) ? auth()->id() : null,
                'mail' => (!auth()->id()) ? $value['email'] : null,
                'title' => $value['title'],
                'feature' => $value['feature'],
                'token' => $token,
                'token_manage' => $tokenManage,
                'status' => $value['deadline'],
                'deadline' => $value['deadline'],
                'description' => $value['description'],
            ]);

            $survey = $this->surveyRepository->createSurvey(
                $inputs,
                ($value['setting']) ?: [],
                $value['txt-question'],
                ($value['checkboxRequired']['question']) ?: [],
                ($value['image']) ?: []
            );

            if ($survey) {
                $inputInfo = $request->only([
                    'name',
                    'email',
                    'title',
                    'description',
                ]);
                $inputInfo['link'] = action($inputs['feature']
                    ? 'AnswerController@answerPublic'
                    : 'AnswerController@answerPrivate', [
                        'token' => $token,
                ]);
                $inputInfo['linkManage'] = action('AnswerController@show', [
                    'tokenManage' =>  $tokenManage,
                ]);
                $job = (new SendMail(collect($inputInfo), 'mailManage'))
                    ->onConnection('database')
                    ->onQueue('emails');
                $isSuccess = ($this->dispatch($job) && $this->inviteUser($request, $survey, config('settings.return.bool')));

                if (!$isSuccess) {
                    DB::rollback();

                    return redirect()->action('SurveyController@index')
                        ->with('message-fail', trans('messages.object_created_unsuccessfully', [
                            'object' => class_basename(Survey::class),
                    ]));
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->action('SurveyController@index')
                ->with('message-fail', trans('messages.object_created_unsuccessfully', [
                    'object' => class_basename(Survey::class),
            ]));
        }

        return view('user.pages.complete', [
            'name' => $value['name'],
            'email' => $value['email'],
            'token' => $token,
            'name' => $value['name'],
            'tokenManage' => $tokenManage,
            'feature' => ($value['feature']) ? config('settings.feature') : config('settings.not_feature'),
        ]);
    }

    public function inviteUser(Request $request, $surveyId, $type)
    {
        // type nếu là bool thì hàm inviteUser sẽ trả về true hoặc flase, view thì hàm inviteUser sẽ trả về action('SurveyController@listSurveyUser')
        $isSuccess = false;
        $data['name'] = $request->get('name') ?: auth()->user()->name;
        $data['email'] = $request->get(($type == config('settings.return.bool')) ? 'email' : 'emailUser') ?: auth()->user()->email;
        $data['emails'] = $request->get(($type == config('settings.return.bool')) ? 'emails' : 'emailsUser');
        $data['feature'] = $request->get('feature');

        if (empty($data['emails'])) {
            return true;
        }

        $data['emails'] = explode(',', $data['emails']);

        if ($data['emails'] && $surveyId) {
            $survey = $this->surveyRepository->find($surveyId);
            $invite = $this->inviteRepository
                ->invite(auth()->id(), $data['emails'], $surveyId);
            $data['feature'] = ($type == config('settings.return.bool')) ? $data['feature'] : $survey->feature;

            if ($invite) {
                $inputInfo = [
                    'name' => auth()->check() ? auth()->user()->name : $data['name'],
                    'email' => $data['emails'],
                    'title' => $survey->title,
                    'description' => $survey->description,
                    'link' => action($survey->feature
                        ? 'AnswerController@answerPublic'
                        : 'AnswerController@answerPrivate', [
                            'token' => $survey->token,
                    ]),
                    'emailSender' => $data['email'],
                ];
                $job = (new SendMail(collect($inputInfo), 'mailInvite'))
                    ->onConnection('database')
                    ->onQueue('emails');
                $this->dispatch($job);
                $isSuccess = true;
            }
        }

        return ($type == config('setttings.return.bool')) ? $isSuccess : ($isSuccess)
            ? redirect()->action('SurveyController@listSurveyUser')
                ->with('message', trans('survey.invite_success'))
            : redirect()->action('SurveyController@listSurveyUser')
                ->with('message-fail', trans('survey.invite_fail'));
    }
}
