<?php

namespace App\Repositories\Setting;

use DB;
use Exception;
use App\Repositories\Setting\SettingInterface;
use App\Repositories\Survey\SurveyInterface;
use App\Repositories\BaseRepository;
use App\Models\Setting;

class SettingRepository extends BaseRepository implements SettingInterface
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }

    public function delete($ids)
    {
        DB::beginTransaction();
        try {
            $ids = is_array($ids) ? $ids : [$ids];
            parent::delete($ids);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    public function deleteBySurveyId($surveyId)
    {
        $settings = $this->whereIn('survey_id', $surveyId)->lists('id')->toArray();
        parent::delete($settings);
    }

    public function deleteByKey($surveyId, $key)
    {
        $settings = $this->whereIn('survey_id', $surveyId)->where('key', $key)->get()->first();
        parent::delete($settings);
    }

    public function createMultiSetting(array $settings, $surveyId)
    {
        if (empty($settings)) {
            return true;
        }

        $inputs = [];

        foreach ($settings as $key => $value) {
            $inputs[] = [
                'survey_id' => $surveyId,
                'key' => $key,
                'value' => $value,
            ];
        }

        return $this->multiCreate($inputs);
    }

    public function update($surveyId, $value)
    {
        if (!$surveyId || !$value) {
            return false;
        }

        $settings = $this
            ->where('survey_id', $surveyId)
            ->whereIn('key', config('settings.listKey'))
            ->get();

        foreach ($settings as $setting) {
            if (!array_has($value['setting'], $setting->key) || !$value['setting'][$setting->key]) {
                $value['setting'][$setting->key] = null;
            }

            $input = [
                'key' => $setting->key,
                'value' => $value['setting'][$setting->key],
            ];
            parent::update($setting->id, $input);

            if ($setting !== end($settings)) {
                $this->newQuery($setting);
            }
        }

        return true;
    }
}
