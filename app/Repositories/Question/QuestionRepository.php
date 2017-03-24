<?php

namespace App\Repositories\Question;

use App\Repositories\Answer\AnswerInterface;
use App\Repositories\BaseRepository;
use App\Models\Answer;
use DB;
use Exception;
use App\Models\Question;
use Illuminate\Support\Collection;

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

        $sequence = 0;

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
                'sequence' => $sequence,
            ];

            $sequence++;
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

    private function createOrUpdateQuestion(
        $surveyId,
        collection $collectQuestion,
        $questionId,
        array $questions,
        array $answers,
        array $options,
        array $deleteImageIds
    ) {
        $isDelete = $options['flag'];
        $checkboxRequired = $options['checkboxRequired'];
        $imagesQuestion = $options['imagesQuestion'];
        $imagesAnswer = $options['imagesAnswer'];
        $dataUpdate = [];
        $dataUpdate['content'] = $options['questionConent'];
        $dataUpdate['sequence'] = $options['indexQuestion'];

        if (in_array($questionId, $deleteImageIds)) {
            $dataUpdate['image'] = null;
        }

        $dataUpdate['required'] = array_key_exists($questionId, $checkboxRequired);

        if ($imagesQuestion && array_key_exists($questionId, $imagesQuestion)) {
            $dataUpdate['image'] = $this->uploadImage($imagesQuestion[$questionId], config('settings.image_question_path'));
        }

        $modelQuestion = $collectQuestion->where('id', $questionId)->first();

        if ($modelQuestion) {
            $modelQuestion->fill($dataUpdate);

            if ($field = $modelQuestion->getDirty()) {
                $modelQuestion->save();

                if (head(array_keys($field)) != config('survey.field.sequence')) {
                    $isDelete = true;
                }
            }
        } else {
            // not found record in collection then insert question
            $modelQuestion = $this->firstOrCreate([
                'sequence' => $dataUpdate['sequence'],
                'survey_id' => $surveyId,
                'content' => $dataUpdate['content'],
                'image' => array_key_exists('image', $dataUpdate) ? $dataUpdate['image'] : null,
                'required' => $dataUpdate['required'],
            ]);

            // insert answers after insert question
            $dataInput = [];
            $checkImagesAnswerCreate = ($imagesAnswer && array_key_exists($questionId, $imagesAnswer));

            foreach ($answers[$questionId] as $answerIndex => $content) {
                $checkHaveImage = ($checkImagesAnswerCreate && array_key_exists($answerIndex, $imagesAnswer[$questionId]));
                $dataInput[] = [
                    'question_id' => $modelQuestion->id,
                    'content' => head($content),
                    'type' => head(array_keys($content)),
                    'image' => $checkHaveImage
                        ? $this->answerRepository->uploadImage($imagesAnswer[$questionId][$answerIndex], config('settings.image_answer_path'))
                        : null,
                ];
            }

            $this->answerRepository->multiCreate($dataInput);
            $answers = array_except($answers, [$questionId]);
        }

        return [
            'success' => true,
            'flag' => $isDelete,
            'answers' => $answers,
        ];
    }

    private function sliptString($value)
    {
        return array_where(explode(',', $value), function($value, $key) {
            return !empty($value);
        });
    }
    
    public function updateSurvey(array $inputs, $surveyId)
    {
        $ids = null;

        if (!$surveyId) {
            return false;
        }

        if ($inputs['del-question']) {
            $ids = $this->sliptString($inputs['del-question']);
            $this->delete($ids ?: []);
        }

        if ($inputs['del-answer']) {
            $ids = $this->sliptString($inputs['del-answer']);
            $this->answerRepository->delete($ids ?: []);

        }

        $this->newQuery(new Question());
        $collectQuestion = $questionIds = $this->where('survey_id', $surveyId)->whereNotIn('id', $ids ?: []);
        $this->answerRepository->newQuery(new Answer());

        $questions = $inputs['txt-question']['question']; // the questions get by request in controller
        $answers = $inputs['txt-question']['answers']; // the answers get by request in controller
        $checkboxRequired = $inputs['checkboxRequired']['question'] ?: []; // ids question required get bay request in controller
        $images = $inputs['image']; // image of answer and question get by requset in controller
        $imagesQuestion = ($images && array_key_exists('question', $images)) ? $images['question'] : [];
        $imagesAnswer = ($images && array_key_exists('answers', $images)) ? $images['answers'] : [];
        $removeAnswerIds = []; // the ids result will be remove when update quesiton or answer
        $collectAnswer = $this->answerRepository
            ->whereIn('question_id', $questionIds->pluck('id')->toArray())
            ->whereNotIn('id', $ids ?: [])
            ->get()
            ->groupBy('question_id');
        $collectQuestion = !$collectQuestion->get()->isEmpty() ? $collectQuestion->get() : collect([]);
        $collectAnswer = !$collectAnswer->isEmpty() ? $collectAnswer : collect([]);
        $indexQuestion = 0;

        // if ($collectQuestion->isEmpty() || $collectAnswer->isEmpty()) {
        //     return false;
        // }

        foreach ($questions as $questionId => $questionConent) {
            $options = [
                'questionConent' => $questionConent,
                'indexQuestion' => $indexQuestion,
                'checkboxRequired' => $checkboxRequired,
                'flag' => false,
                'imagesAnswer' => $imagesAnswer,
                'imagesQuestion' => $imagesQuestion,
            ];
            $deleteImageIds = !empty($inputs['del-answer-image']) ? $this->sliptString($inputs['del-question-image']) : [];
            $questionsResult = $this->createOrUpdateQuestion(
                $surveyId,
                $collectQuestion,
                $questionId,
                $questions,
                $answers,
                $options,
                $deleteImageIds
            );
            $indexQuestion++;
            // insert or update answer after create or update question
            $answersInQuestion = $collectAnswer->has($questionId)
                ? $collectAnswer[$questionId]->whereIn('type', [config('survey.type_radio'), config('survey.type_checkbox')])
                : collect([]);
            $deleteImageIds = !empty($inputs['del-answer-image']) ? $this->sliptString($inputs['del-answer-image']) : [];
            $answersResult = $this->answerRepository->createOrUpdateAnswer(
                $questionId,
                $answersInQuestion,
                $collectAnswer,
                $imagesAnswer,
                $questionsResult['answers'],
                $removeAnswerIds,
                $questionsResult['flag'],
                $deleteImageIds
            );
            $answers = $answersResult['answers'];
            $removeAnswerIds = $answersResult['removeAnswerIds'];
            // get id of last element in collection if it's the orther radio or orther checkbox if the question is update
            if ($questionsResult['flag'] && $collectAnswer->has($questionId)) {
                $answer = $collectAnswer[$questionId]->whereIn('type', [
                    config('survey.type_other_radio'),
                    config('survey.type_other_checkbox'),
                ]);
                if (!$answer->isEmpty()) {
                    $removeAnswerIds[] = $answer->first()->id;
                }
            }
        }

        $this->answerRepository->deleteResultWhenUpdateAnswer($removeAnswerIds);

        return $removeAnswerIds;
    }
}
