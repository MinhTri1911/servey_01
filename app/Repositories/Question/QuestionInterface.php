<?php

namespace App\Repositories\Question;

interface QuestionInterface
{
    public function deleteBySurveyId($surveyId);

    public function delete($ids);

    public function createMultiQuestion($survey, $questions, $answers, $image, $required = null);

    public function getQuestionIds($surveyId);

    public function getResultByQuestionIds($surveyId, $time = null);
}
