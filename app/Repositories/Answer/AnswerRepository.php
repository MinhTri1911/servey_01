<?php

namespace App\Repositories\Answer;

use App\Models\Answer;
use App\Repositories\BaseRepository;
use App\Repositories\Result\ResultInterface;
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
}
