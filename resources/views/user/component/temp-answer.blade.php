@foreach($survey->questions as $key => $question)
    <div>
        <h4 class="tag-question">
            {{ ++$key }} . {{ $question->content }}
            <span>{{ ($question->required) ? '(*)' : '' }}</span>
        </h4>
        @if ($question->image)
            {!! Html::image($question->image, '',[
                'class' => 'show-img-question',
            ]) !!}
        @endif
        <ul class="data-list">
            @foreach($question->answers as $temp => $answer)
                <?php $checked = ''; ?>
                @if ($tempAnswers)
                    @foreach($tempAnswers as $tempAnswer)
                        <?php $checked = ($tempAnswer['answer_id'] == $answer->id) ? $tempAnswer['content'] : $checked; ?>
                    @endforeach
                @endif
                <li class="{{ ($question->required) ?  'required': '' }}">
                    @switch($answer->type)
                        @case(config('survey.type_radio'))
                            {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                'id' => "$key$temp",
                                'class' => 'option-choose input-radio',
                                'temp-as' => $answer->id,
                                'temp-qs' => $question->id,
                                ($checked) ? 'checked = checked' : null,
                            ]) }}
                            {{ Form::label($key . $temp, $answer->content, [
                                'class' => 'label-radio',
                            ]) }}
                            @if ($answer->image)
                                <div>
                                    {!! Html::image($answer->image, '',[
                                        'class' => 'show-img-answer',
                                    ]) !!}
                                </div>
                            @endif
                            @breakswitch
                        @case(config('survey.type_checkbox'))
                            <div>
                                {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                'id' => "$key$temp",
                                'class' => 'input-checkbox',
                                ($checked) ? 'checked = checked' : null,
                            ]) }}
                            {{ Form::label($key . $temp, $answer->content, [
                                'class' => 'label-checkbox'
                            ]) }}
                            </div>
                            @if ($answer->image)
                                <div>
                                    {!! Html::image($answer->image, '',[
                                        'class' => 'show-img-answer',
                                    ]) !!}
                                </div>
                            @endif
                            @breakswitch
                        @case(config('survey.type_text'))
                             {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                'class' => 'form-control answer',
                                'id' => "answer[$question->id][$answer->id]",
                            ]) !!}
                            @breakswitch
                        @case(config('survey.type_time'))
                            {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                'class' => 'frm-time form-control',
                                'id' => "answer[$question->id]",
                            ]) !!}
                            @breakswitch
                        @case(config('survey.type_date'))
                            {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                'class' => 'form-control frm-date-2',
                                'id' => "answer[$question->id]",
                            ]) !!}
                            @breakswitch
                        @case(config('survey.type_other_radio'))
                            <div class="row">
                                <div class="col-md-2">
                                    {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                        'id' => "$key$temp",
                                        'class' => 'input-radio option-add',
                                        'temp-as' => $answer->id,
                                        'temp-qs' => $question->id,
                                        'url' => action('TempController@addTemp', config('temp.text_other')),
                                        ($checked) ? 'checked = checked' : null,
                                    ]) }}
                                    {{ Form::label($key.$temp, trans('home.other'), [
                                        'class' => 'label-radio',
                                    ]) }}
                                </div>
                                <div class="append-input col-md-8 append-as{{ $question->id }}">
                                    @if ($checked)
                                        {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                            'class' => 'animated zoomIn form-control input' . $question->id,
                                            'required' => true,
                                        ]) !!}
                                    @endif
                                </div>
                            </div>
                            @breakswitch
                        @case(config('survey.type_other_checkbox'))
                            <div class="row">
                                <div class="col-md-2">
                                    {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                        'id' => "$key$temp",
                                        'class' => 'input-checkbox option-add',
                                        'temp-as' => $answer->id,
                                        'temp-qs' => $question->id,
                                        'url' => action('TempController@addTemp', config('temp.text_other')),
                                        ($checked) ? 'checked = checked' : null,
                                    ]) }}
                                    {{ Form::label($key.$temp, trans('home.other'), [
                                        'class' => 'label-checkbox',
                                    ]) }}
                                </div>
                                <div class="col-md-8 append-input-checkbox append-as{{ $question->id }}">
                                    @if ($checked)
                                        {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                            'class' => 'animated zoomIn form-control input' . $question->id,
                                            'required' => true,
                                        ]) !!}
                                    @endif
                                </div>
                            </div>
                        @breakswitch
                    @endswitch
                </li>
            @endforeach
        </ul>
        @if ($errors->has('answer.' . $question->id))
            <div class="alert alert-danger alert-message">
                {{ $errors->first('answer.' . $question->id) }}
            </div>
        @endif
        @if ($errors->has('answer.' . $question->id . '.' . $question->answers[0]->id))
            <div class="alert alert-danger alert-message">
                {{ $errors->first('answer.' . $question->id . '.' . $question->answers[0]->id) }}
            </div>
        @endif
    </div>
@endforeach
