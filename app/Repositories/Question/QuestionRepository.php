<?php

namespace App\Repositories\Question;

use App\Repositories\Answer\AnswerInterface;
use App\Repositories\BaseRepository;
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

    private function createOrUpdateQuestion($surveyId, $imagesQuestion, $index, $flag, array $inputsQuestion, array $inputsAnswer)
    {
        $questionId = $index;
        $isDelete = $flag;
        $dataUpdate = [];
        $dataUpdate['content'] = $questionConent;
        $dataUpdate['sequence'] = $indexQuestion;

        if (array_key_exists($questionId, $checkboxRequired)) {
            $dataUpdate['required'] = 1;
        } else {
            $dataUpdate['required'] = 0;
        }

        if ($imagesQuestion && array_key_exists($questionId, $imagesQuestion)) {
            $dataUpdate['image'] = $this->uploadImage($imagesQuestion[$questionId], config('settings.image_question_path'));
        }

        $modelQuestion = $collectQuestion->where('id', $questionId)->first();

        try {
            if ($modelQuestion) {
                $modelQuestion->fill($dataUpdate);

                if ($field = $modelQuestion->getDirty()) {
                    $modelQuestion->save();

                    if (head(array_keys($field)) != 'sequence') {
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

                $answers = array_except($answers, [$questionId]);
                $this->answerRepository->multiCreate($dataInput);
            }
        } catch (Exception $e) {
            throw $e; 
        }

        return ;
    }

    private function createOrUpdateAnswer($questionId, $imagesAnswer, array $inputs)
    {
        # code...
    }

    public function updateSurvey(array $inputs, $surveyId)
    {
        $questions = $inputs['txt-question']['question'];
        $answers = $inputs['txt-question']['answers'];
        $checkboxRequired = $inputs['checkboxRequired']['question'] ?: [];
        $images = $inputs['image'];
        $imagesQuestion = ($images && array_key_exists('question', $images)) ? $images['question'] : null;
        $imagesAnswer = ($images && array_key_exists('answers', $images)) ? $images['answers'] : null;
        $removeAnswerIds = [];
        $collectQuestion = $questionIds = $this->where('survey_id', $surveyId);
        $collectAnswer = $this->answerRepository
            ->whereIn('question_id', $questionIds->lists('id')->toArray())
            ->get()->groupBy('question_id');
        $collectQuestion = $collectQuestion->get();
        $indexQuestion = 0;
    }

    public function updateSurvey(array $inputs, $surveyId)
    {
        $questions = $inputs['txt-question']['question'];
        $answers = $inputs['txt-question']['answers'];
        $checkboxRequired = $inputs['checkboxRequired']['question'] ?: [];
        $images = $inputs['image'];
        $imagesQuestion = ($images && array_key_exists('question', $images)) ? $images['question'] : null;
        $imagesAnswer = ($images && array_key_exists('answers', $images)) ? $images['answers'] : null;
        $removeAnswerIds = [];
        $collectQuestion = $questionIds = $this->where('survey_id', $surveyId);
        $collectAnswer = $this->answerRepository
            ->whereIn('question_id', $questionIds->lists('id')->toArray())
            ->get()->groupBy('question_id');
        $collectQuestion = $collectQuestion->get();
        $indexQuestion = 0;
        
        foreach ($questions as $questionId => $questionConent) {
            $isDelete = $flag;
            $dataUpdate = [];
            $dataUpdate['content'] = $questionConent;
            $dataUpdate['sequence'] = $indexQuestion;

            if (array_key_exists($questionId, $checkboxRequired)) {
                $dataUpdate['required'] = 1;
            } else {
                $dataUpdate['required'] = 0;
            }

            if ($imagesQuestion && array_key_exists($questionId, $imagesQuestion)) {
                $dataUpdate['image'] = $this->uploadImage($imagesQuestion[$questionId], config('settings.image_question_path'));
            }

            $modelQuestion = $collectQuestion->where('id', $questionId)->first();

            if ($modelQuestion) {
                $modelQuestion->fill($dataUpdate);

                if ($field = $modelQuestion->getDirty()) {
                    $modelQuestion->save();

                    if (head(array_keys($field)) != 'sequence') {
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

                $answers = array_except($answers, [$questionId]);
                $this->answerRepository->multiCreate($dataInput);
            }

            $indexQuestion++;
            // insert or update answer after create or update question
            $answersInQuestion = $collectAnswer->has($questionId)
                ? $collectAnswer[$questionId]->whereIn('type', [config('survey.type_radio'), config('survey.type_checkbox')])
                : null;

            if ($answersInQuestion && !$answersInQuestion->isEmpty()) {
                $index = 0;
                $arrayInfoUpdate = $answers[$questionId];
                $checkImages = ($imagesAnswer && array_key_exists($questionId, $imagesAnswer)); // check image answer is exists in question

                if ($arrayInfoUpdate && in_array(head(array_keys(last($arrayInfoUpdate))), [ 
                    config('survey.type_other_radio'),
                    config('survey.type_other_checkbox'),
                ])) {
                    // remove if last index of answer[$question] is other radio or other checkbox in last list answer
                    end($answers[$questionId]);
                    $key = key($answers[$questionId]);
                    $arrayInfoUpdate = array_except($arrayInfoUpdate, [$key]); 
                }

                foreach ($answersInQuestion as $indexAnswer => $answer) {
                    $updateAnswer = [];
                    $questionId = $answer->question_id;
                    $typeAnswer = $answer->type;
                    $updateAnswer['content'] = $arrayInfoUpdate[$index][$typeAnswer];
                    $checkHaveImage = ($checkImages && array_key_exists($indexAnswer, $imagesAnswer[$questionId])); // check the answer is have image

                    if ($checkHaveImage) {
                        $updateAnswer['image'] = $this->answerRepository
                            ->uploadImage($imagesAnswer[$questionId][$indexAnswer], config('settings.image_answer_path'));
                    }

                    $modelAnswer = $answer->fill($updateAnswer);

                    if ($modelAnswer->getDirty()) {
                        $modelAnswer->save();

                        if (!$isDelete) {
                            $removeAnswerIds[] = $modelAnswer->id;
                        }
                    }

                    if ($isDelete) {
                        $removeAnswerIds[] = $answer->id;
                    }

                    $answers[$questionId] = array_except($answers[$questionId], [$indexAnswer]);

                    $index++;
                }

                $check = $collectAnswer[$questionId]->whereIn('type', [
                    config('survey.type_other_radio'),
                    config('survey.type_other_checkbox'),
                ]);

                /*
                * check if the question have other radio or orther checkbox
                * if true remove the element in array to uncreate new orther answer
                * else then the user had update new orther answer
                */
                if (!$check->isEmpty()) {
                    end($answers[$questionId]);
                    $key = key($answers[$questionId]);
                    $answers[$questionId] = array_except($answers[$questionId], [$key]);
                }

                if ($answersCreate = $answers[$questionId]) {
                    $dataInput = [];

                    foreach ($answersCreate as $indexAnswer => $answer) {
                        $checkHaveImage = ($checkImages && array_key_exists($indexAnswer, $imagesAnswer[$questionId]));

                        if ($answer) {
                            $dataInput[] = [
                                'content' => head($answer),
                                'question_id' => $questionId,
                                'type' => head(array_keys($answer)),
                                'image' => $checkHaveImage
                                    ? $this->answerRepository->uploadImage($imagesAnswer[$questionId][$indexAnswer], config('settings.image_answer_path'))
                                    : null,
                            ];
                        }
                    }

                    $this->answerRepository->multiCreate($dataInput);
                }
            }

            /*
            * check if update the question then remove all of record have answer_id in question 
            * remove the result if the last answer of question is orther radio or orther checkbox 
            */
            if ($isDelete && $collectAnswer->has($questionId) && $answerCheck = $collectAnswer[$questionId]->whereIn('type', [
                config('survey.type_other_radio'),
                config('survey.type_other_checkbox'),
            ])) {
                if (!$answerCheck->isEmpty()) {
                    $removeAnswerIds[] = $answerCheck->first()->id;
                }
            }
        }

        $this->answerRepository->deleteResultWhenUpdateAnswer($removeAnswerIds);

        return $removeAnswerIds;
    }
}






















































































































































// private function createOrUpdateQuestion(collect $inputs)
    // {
    //     $value = $inputs->only([
    //         'collectQuestion',
    //         'questionId',
    //         'imagesQuestion',
    //         'imagesAnswer',
    //         'checkboxRequired',
    //         'surveyId',
    //         'answers',
    //         'questionConent',
    //         'indexQuestion',
    //     ]);

    //     $collectQuestion = $value['collectQuestion'];
    //     $questionId = $value['questionId'];
    //     $imagesQuestion = $value['imagesAnswer'];
    //     $imagesAnswer = $value['imagesAnswer'];
    //     $checkboxRequired = $value['checkboxRequired'];
    //     $surveyId = $value['surveyId'];
    //     $answers = $value['answers'];
    //     $questionConent = $value['questionConent'];
    //     $indexQuestion = $value['indexQuestion'];
    //     $isDelete = false;
    //     $dataUpdate = [];
    //     $dataUpdate['content'] = $questionConent;
    //     $dataUpdate['sequence'] = $indexQuestion;

    //     try {
    //         if (array_key_exists($questionId, $checkboxRequired)) {
    //             $dataUpdate['required'] = 1;
    //         } else {
    //             $dataUpdate['required'] = 0;
    //         }

    //         if ($imagesQuestion && array_key_exists($questionId, $imagesQuestion)) {
    //             $dataUpdate['image'] = $this->uploadImage($imagesQuestion[$questionId], config('settings.image_question_path'));
    //         }

    //         $modelQuestion = $collectQuestion->where('id', $questionId)->first();

    //         if ($modelQuestion) {

    //             $modelQuestion->fill($dataUpdate);

    //             if ($field = $modelQuestion->getDirty()) {
    //                 $modelQuestion->save();

    //                 if (head(array_keys($field)) != 'sequence') {
    //                     $isDelete = true;
    //                 }
    //             }
    //         } else {
    //             // not found record in collection and insert question
    //             $modelQuestion = $this->firstOrCreate([
    //                 'sequence' => $dataUpdate['sequence'],
    //                 'survey_id' => $surveyId,
    //                 'content' => $dataUpdate['content'],
    //                 'image' => array_key_exists('image', $dataUpdate) ? $dataUpdate['image'] : null,
    //                 'required' => $dataUpdate['required'],
    //             ]);

    //             // insert answers after insert question
    //             $dataInput = [];
    //             $checkImagesAnswerCreate = ($imagesAnswer && array_key_exists($questionId, $imagesAnswer));

    //             foreach ($answers[$questionId] as $answerIndex => $content) {
    //                 $checkImagesAnswerCreate = ($checkImagesAnswerCreate && array_key_exists($answerIndex, $imagesAnswer[$questionId]));
    //                 $dataInput[] = [
    //                     'question_id' => $modelQuestion->id,
    //                     'content' => head($content),
    //                     'type' => head(array_keys($content)),
    //                     'image' => $checkImagesAnswerCreate
    //                         ? $this->answerRepository->uploadImage($imagesAnswer[$questionId][$answerIndex], config('settings.image_answer_path'))
    //                         : null,
    //                 ];
    //             }

    //             $this->answerRepository->multiCreate($dataInput);
    //         }
    //     } catch (Exception $e) {
    //         throw $e;
    //     }

    //     return [
    //         'success' => true,
    //         'isDelete' => $isDelete,
    //     ];
    // }

    // private function createOrUpdateAnswerIfQuestionExist(collect $inputs)
    // {
    //     $value = $inputs->only([
    //         'collectAnswer',
    //         'answers',
    //         'imagesAnswer',
    //         'isDelete',
    //         'removeAnswerIds',
    //         'questionId',
    //     ]);

    //     $collectAnswer = $value['collectAnswer'];
    //     $answers = $value['answers'];
    //     $imagesAnswer = $value['imagesAnswer'];
    //     $questionId = $value['questionId'];
    //     $isDelete = $value['isDelete'];
    //     $removeAnswerIds = $value['removeAnswerIds'];
    //     // insert or update answer after create or update question
    //     $answersInQuestion = $collectAnswer->has($questionId) ? $collectAnswer[$questionId]->whereIn('type', [1, 2]) : null;
    //     dd($collectAnswer, $answers);
    //     if ($answersInQuestion && !$answersInQuestion->isEmpty()) {
    //         // remove if last index of answer[$question] is other radio or other checkbox in last list answer
    //         if ($answers[$questionId] && in_array(head(array_keys(last($answers[$questionId]))), [5, 6])) {
    //             $answers[$questionId] = array_except($answers[$questionId], [head(array_keys(last($answers[$questionId])))]);
    //         }

    //         // check image answer is exists in question
    //         $checkImages = ($imagesAnswer && array_key_exists($questionId, $imagesAnswer));
    //         $index = 0;

    //         foreach ($answersInQuestion as $indexAnswer => $answer) {
    //             $updateAnswer = [];
    //             $questionId = $answer->question_id;
    //             $typeAnswer = $answer->type;
    //             $updateAnswer['content'] = $answers[$questionId][$index][$typeAnswer];
    //             // check the answer is have image
    //             $checkImages = ($checkImages && array_key_exists($indexAnswer, $imagesAnswer[$questionId]));

    //             if ($checkImages) {
    //                 $updateAnswer['image'] = $this->answerRepository
    //                     ->uploadImage($imagesAnswer[$questionId][$indexAnswer], config('settings.image_answer_path'));
    //             }

    //             $modelAnswer = $answer->fill($updateAnswer);

    //             if ($modelAnswer->getDirty()) {
    //                 $modelAnswer->save();

    //                 if (!$isDelete) {
    //                     $removeAnswerIds[] = $modelAnswer->id;
    //                 }
    //             }

    //             if ($isDelete) {
    //                 $removeAnswerIds[] = $answer->id;
    //             }

    //             $answers[$questionId] = array_except($answers[$questionId], [$indexAnswer]);

    //             $index++;
    //         }

    //         if ($answersCreate = $answers[$questionId]) {
    //             $dataInput = [];

    //             foreach ($answersCreate as $indexAnswer => $answer) {
    //                 $checkImages = ($checkImages && array_key_exists($indexAnswer, $imagesAnswer[$questionId]));

    //                 if ($answer) {
    //                     $dataInput[] = [
    //                         'content' => head($answer),
    //                         'question_id' => $questionId,
    //                         'type' => head(array_keys($answer)),
    //                         'image' => $checkImages
    //                             ? $this->answerRepository->uploadImage($imagesAnswer[$questionId][$indexAnswer], config('settings.image_answer_path'))
    //                             : null,
    //                     ];
    //                 }
    //             }

    //             $this->answerRepository->multiCreate($dataInput);
    //         }
    //     }

    //     return $removeAnswerIds;
    // }

    // public function updateSurvey(array $inputs, $surveyId)
    // {
    //     $questions = $inputs['txt-question']['question'];
    //     $answers = $inputs['txt-question']['answers'];
    //     $checkboxRequired = $inputs['checkboxRequired']['question'] ?: [];
    //     $images = $inputs['image'];
    //     $imagesQuestion = ($images && array_key_exists('question', $images)) ? $images['question'] : null;
    //     $imagesAnswer = ($images && array_key_exists('answers', $images)) ? $images['answers'] : null;
    //     $removeAnswerIds = [];
    //     $collectQuestion = $questionIds = $this->where('survey_id', $surveyId);
    //     $collectAnswer = $this->answerRepository
    //         ->whereIn('question_id', $questionIds->lists('id')->toArray())
    //         ->get()->groupBy('question_id');
    //     $collectQuestion = $collectQuestion->get();
    //     $indexQuestion = 0;

    //     foreach ($questions as $questionId => $questionConent) {
            
    //         $inputs = collect([
    //             'collectQuestion' => $collectQuestion,
    //             'questionId' => $questionId,
    //             'imagesQuestion' => $imagesQuestion,
    //             'imagesAnswer' => $imagesAnswer,
    //             'checkboxRequired' => $checkboxRequired,
    //             'surveyId' => $surveyId,
    //             'answers' => $answers,
    //             'questionConent' => $questionConent,
    //             'indexQuestion' => $indexQuestion,
    //             ]);

    //         $isSucces = $this->createOrUpdateQuestion($inputs);
            
    //         if ($isSucces['success']) {
    //             $indexQuestion++;
    //             try {
    //                 $inputs = collect([
    //                     'collectAnswer' => $collectAnswer,
    //                     'answers' => $answers,
    //                     'imagesAnswer' => $imagesAnswer,
    //                     'isDelete' => $isSucces['isDelete'],
    //                     'removeAnswerIds' => $removeAnswerIds,
    //                     'questionId' => $questionId,
    //                 ]);
    //                 $removeAnswerIds = $this->createOrUpdateAnswerIfQuestionExist($inputs);
    //             } catch (Exception $e) {
    //                 throw $e;
    //             }
    //         }
    //     }

    //     // Delete all result if answer or question have been update or delete
    //     // $this->answerRepository->deleteResultWhenUpdateAnswer($removeAnswerIds);

    //     return true;
    // }