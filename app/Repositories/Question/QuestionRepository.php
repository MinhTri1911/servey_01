<?php

namespace App\Repositories\Question;

use App\Repositories\Answer\AnswerInterface;
use App\Repositories\BaseRepository;
use DB;
use Exception;
use App\Models\Question;

class QuestionRepository extends BaseRepository implements QuestionInterface
{
    protected $answerRepository;

    public function __construct(
        Question $question,
        AnswerInterface $answer
    ) {
        parent::__construct($question);
        $this->answerRepository = $answer;
    }

    public function deleteBySurveyId($surveyIds)
    {
        $ids = is_array($surveyIds) ? $surveyIds : [$surveyIds];
        $questions = $this->whereIn('survey_id', $ids)->lists('id')->toArray();
        $this->answerRepository->deleteByQuestionId($questions);
        parent::delete($questions);
    }

    public function delete($ids)
    {
        DB::beginTransaction();
        try {
            $ids = is_array($ids) ? $ids : [$ids];
            $this->answerRepository->deleteByQuestionId($ids);
            parent::delete($ids);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    public function createMultiQuestion($survey, $questions, $answers, $image, $required = null)
    {
        $questionsAdd = [];
        $answersAdd = [];
        $image = [
            'question' => (array_get($image, 'question')) ? $image['question']: [],
            'answers' => (array_get($image, 'answers')) ? $image['answers'] : [],
        ];

        // check the serial number of arrays answer questions coincide with the array or not
        if (array_keys($questions) !== array_keys($answers)) {
            return false;
        }

        if (empty($required)) {
            $required = [];
        }

        foreach ($questions as $key => $value) {

            if (!strlen($value) && $questions) {
                $value = config('survey.question_default');
            }

            $questionsAdd[] = [
                'content' => $value,
                'survey_id' => $survey,
                'image' => array_get($image['question'], $key)
                    ? $this->uploadImage($image['question'][$key], config('settings.image_question_path'))
                    : null,
                'required' => in_array($key, $required),
            ];
        }

        if ($this->multiCreate($questionsAdd)) {
            $questionIds = $this
                ->where('survey_id', $survey)
                ->lists('id')
                ->toArray();

            foreach (array_keys($questions) as $number => $index) {
                foreach ($answers[$index] as $key => $value) {
                    $type = array_keys($value)[0];

                    switch ($type) {
                        case config('survey.type_other_radio'): case config('survey.type_other_checkbox'):
                            $temp = trans('temp.other');
                            break;
                        case config('survey.type_text'):
                            $temp = trans('temp.text');
                            break;
                        case config('survey.type_time'):
                            $temp = trans('temp.time');
                            break;
                        case config('survey.type_date'):
                            $temp = trans('temp.date');
                            break;
                        default:
                            $temp = $value[$type];
                            break;
                    }

                    // checking the answers in the question have image and the answer is have any image
                    $isHaveImage = (array_get($image['answers'], $index)
                        && array_get($image['answers'][$index], $key));
                    $answersAdd[] = [
                        'content' => $temp,
                        'question_id' => $questionIds[$number],
                        'type' => $type,
                        'image' => ($isHaveImage )
                            ? $this->answerRepository->uploadImage($image['answers'][$index][$key], config('settings.image_answer_path'))
                            : null,
                    ];
                }
            }

            if ($this->answerRepository->multiCreate($answersAdd)) {
                return true;
            }
        }

        return false;
    }

    public function getQuestionIds($surveyId)
    {
        return $this->where('survey_id', $surveyId)->lists('id')->toArray();
    }

    public function getResultByQuestionIds($surveyId, $time = null)
    {
        $questionIds = $this->getQuestionIds($surveyId);

        if (!$time) {
            return $this->answerRepository->getResultByAnswer($questionIds);
        }

        return $this->answerRepository->getResultByAnswer($questionIds, $time);
    }
}
