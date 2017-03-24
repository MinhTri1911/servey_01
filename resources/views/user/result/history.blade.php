<div class="container-list-answer1">
    <div class="div-show-result wizard-branch wizard-wrapper">
        <div class="get-title-survey">
            {{ $survey->title }}
        </div>
        <div class="description-survey">
            <h4>
                {{ $survey->description }}
            </h4>
        </div>
        <div class="row">
            @foreach ($survey->questions as $key => $question)
                <div>
                    <h4 class="tag-question">
                        {{ ($key + 1) . '. ' . $question->content }}
                        <span>{{ ($question->required) ? '(*)' : '' }}</span>
                    </h4>
                    @if ($question->image)
                        {!! Html::image($question->image, '', [
                            'class' => 'show-img-answer',
                        ]) !!}
                    @endif
                    <ul class="data-list">
                        @foreach ($question->answers as $temp => $answer)
                            <li class="{{ ($question->required) ?  'required' : '' }}">
                                @switch($answer->type)
                                    @case(config('survey.type_radio'))
                                        {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                            'id' => "$key$temp",
                                            'class' => 'option-choose input-radio',
                                            'temp-as' => $answer->id,
                                            'temp-qs' => $question->id,
                                            (in_array($answer->id, array_keys($history))) ? ('checked = checked') : null,
                                        ]) }}
                                        {{ Form::label($key.$temp, $answer->content, [
                                            'class' => 'label-radio',
                                        ]) }}
                                        @breakswitch
                                    @case(config('survey.type_checkbox'))
                                        {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                            'id' => "$key$temp",
                                            'class' => 'input-checkbox',
                                            (in_array($answer->id, array_keys($history))) ? ('checked = checked') : null,
                                        ]) }}
                                        {{ Form::label($key.$temp, $answer->content, [
                                            'class' => 'label-checkbox'
                                        ]) }}
                                        @breakswitch
                                    @case(config('survey.type_text'))
                                        {!! Form::textarea("answer[$question->id][$answer->id]",
                                            (in_array($answer->id, array_keys($history))) ? $history[$answer->id] : null, [
                                                'class' => 'form-control',
                                                'id' => "answer[$question->id][$answer->id]",
                                                (in_array($answer->id, array_keys($history))) ? ('disabled = true') : null,
                                        ]) !!}
                                        @breakswitch
                                    @case(config('survey.type_time'))
                                        {!! Form::text("answer[$question->id][$answer->id]",
                                            (in_array($answer->id, array_keys($history))) ? $history[$answer->id] : null, [
                                                'class' => 'frm-time form-control ',
                                                'id' => "answer[$question->id]",
                                                (in_array($answer->id, array_keys($history))) ? ('disabled = true') : null,
                                        ]) !!}
                                        @breakswitch
                                    @case(config('survey.type_date'))
                                        {!! Form::text("answer[$question->id][$answer->id]",
                                            (in_array($answer->id, array_keys($history))) ? $history[$answer->id] : null, [
                                                'class' => 'form-control frm-date-2',
                                                'id' => "answer[$question->id]",
                                                (in_array($answer->id, array_keys($history))) ? ('disabled = true') : null,
                                        ]) !!}
                                        @breakswitch
                                    @case(config('survey.type_other_radio'))
                                        <div class="row">
                                            <div class="col-md-10">
                                                {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                                    'id' => "$key$temp",
                                                    'class' => 'input-radio option-add',
                                                    'temp-as' => $answer->id,
                                                    'temp-qs' => $question->id,
                                                    (in_array($answer->id, array_keys($history))) ? ('checked = checked') : null,
                                                ]) }}
                                                {{ Form::label($key . $temp, (in_array($answer->id, array_keys($history)))
                                                    ? ( trans('home.other') . $history[$answer->id] )
                                                    : null, [
                                                        'class' => 'label-radio',
                                                ]) }}
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_other_checkbox'))
                                        <div class="row">
                                            <div class="col-md-10">
                                                {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                                    'id' => "$key$temp",
                                                    'class' => 'input-checkbox option-add',
                                                    'temp-as' => $answer->id,
                                                    'temp-qs' => $question->id,
                                                    (in_array($answer->id, array_keys($history))) ? ('checked = checked') : null,
                                                ]) }}
                                                {{ Form::label($key . $temp, (in_array($answer->id, array_keys($history)))
                                                    ? ( trans('home.other') . $history[$answer->id] )
                                                    : null, [
                                                        'class' => 'label-checkbox',
                                                ]) }}
                                            </div>
                                        </div>
                                    @breakswitch
                                @endswitch
                            </li>
                            @if ($answer->image)
                                <li>
                                    {!! Html::image($answer->image, '', [
                                        'class' => 'show-img-answer',
                                    ]) !!}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
    <div id="bottom-wizard">
    </div>
</div>
