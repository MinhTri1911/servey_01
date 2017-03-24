<?php

namespace App\Repositories\Answer;

use App\Models\Answer;
use App\Models\Result;
use App\Repositories\BaseRepository;
use App\Repositories\Result\ResultInterface;
use Illuminate\Support\Collection;
use DB;
use Exception;
use Carbon\Carbon;

class AnswerRepository extends BaseRepository implements AnswerInterface
{
    protected $resultRepository;
    public function __construct(Answer $answer, ResultInterface $result)
    {
        parent::__construct($answer);
        $this->resultRepository = $result;
    }
    public function deleteByQuestionId($questionIds)
    {
        $ids = is_array($questionIds) ? $questionIds : [$questionIds];
        $answers = $this->whereIn('question_id', $ids)->lists('id')->toArray();
        $this->resultRepository
            ->delete($this->resultRepository->whereIn('answer_id', $answers)->lists('id')->toArray());
        parent::delete($answers);
    }
    public function delete($ids)
    {
        DB::beginTransaction();
        try {
            $ids = is_array($ids) ? $ids : [$ids];
            $this->resultRepository
                ->delete($this->resultRepository->whereIn('answer_id', $ids)->lists('id')->toArray());
            parent::delete($ids);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
    }
    public function getAnswerIds($questionIds)
    {
        return $this->whereIn('question_id', $questionIds)->lists('id')->toArray();
    }
    public function getResultByAnswer($questionIds, $time = null)
    {
        $answerIds = $this->getAnswerIds($questionIds);
        if (!$time) {
            return $this->resultRepository
                ->whereIn('answer_id', $answerIds);
        }
        $time = Carbon::parse($time)->format('Y-m-d');
        return $this->resultRepository
            ->whereIn('answer_id', $answerIds)
            ->where('created_at', 'LIKE', "%$time%");
    }
    public function deleteResultWhenUpdateAnswer($ids)
    {
        $this->resultRepository->newQuery(new Result());
        $isSuccess = $this->resultRepository
            ->delete($this->resultRepository->whereIn('answer_id', is_array($ids) ? $ids : [$ids])->lists('id')->toArray());
        return $isSuccess;
    }
    public function createOrUpdateAnswer(
        $questionId,
        collection $answersInQuestion,
        collection $collectAnswer,
        array $imagesAnswer,
        array $answers,
        array $removeAnswerIds,
        $isDelete,
        array $deleteImageIds
    ) {
        if ($answersInQuestion && !$answersInQuestion->isEmpty()) {
            $index = 0;
            $arrayInfoUpdate = $answers[$questionId];
            // check image answer is exists in question
            $checkImages = ($imagesAnswer && array_key_exists($questionId, $imagesAnswer));
            // remove if last index of answer[$question] is other radio or other checkbox in last list answer
            if ($arrayInfoUpdate && in_array(head(array_keys(last($arrayInfoUpdate))), [
                config('survey.type_other_radio'),
                config('survey.type_other_checkbox'),
            ])) {
                end($answers[$questionId]);
                $key = key($answers[$questionId]);
                $arrayInfoUpdate = array_except($arrayInfoUpdate, [$key]);
            }
            foreach ($answersInQuestion as $indexAnswer => $answer) {
                $updateAnswer = [];
                $questionId = $answer->question_id;
                $typeAnswer = $answer->type;
                $updateAnswer['content'] = $arrayInfoUpdate[$index][$typeAnswer];
                if (in_array($answer->id, $deleteImageIds)) {
                    $updateAnswer['image'] = null;
                }
                // check the answer is have image
                $checkHaveImage = ($checkImages && array_key_exists($indexAnswer, $imagesAnswer[$questionId]));
                if ($checkHaveImage) {
                    $updateAnswer['image'] = $this
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
                                ? $this->uploadImage($imagesAnswer[$questionId][$indexAnswer], config('settings.image_answer_path'))
                                : null,
                        ];
                    }
                }
                $this->multiCreate($dataInput);
            }
        }
        return [
            'success' => true,
            'answers' => $answers,
            'removeAnswerIds' => $removeAnswerIds,
        ];
    }
}
