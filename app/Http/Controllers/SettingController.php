<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Survey\SurveyInterface;
use App\Repositories\Setting\SettingInterface;
use DB;
use Exception;

class SettingController extends Controller
{
    protected $settingRepository;
    protected $surveyRepository;

    public function __construct(
        SurveyInterface $surveyRepository,
        SettingInterface $settingRepository
    ) {
        $this->surveyRepository = $surveyRepository;
        $this->settingRepository = $settingRepository;
    }

    public function update(Request $request, $surveyId, $token)
    {
        $value = $request->only([
            'setting',
            'feature',
        ]);

        DB::beginTransaction();
        try {
            $this->settingRepository->update($surveyId, $value);
            $this->surveyRepository->update($surveyId, [
                'feature' => $value['feature'] ?: 0,
            ]);
            DB::commit();

            return redirect()->action('AnswerController@show',$token)
                ->with('message', trans('survey.update_success'));
        } catch (Exception $e) {
            throw $e;

            DB::rollback();
        }

        return redirect()->action('AnswerController@show',$token)
            ->with('message-fail', trans('survey.update_fail'));
    }
}
