@php
    $maxUpdateQuestion = $survey->questions->max('update');
    $indexShow = 0;
    $surveyAll = $survey->questions()->simplePaginate(config('settings.paginate'));
@endphp
@foreach($surveyAll as $key => $question)
    @if ($question->update >= 0)
        <div>
            <h4 class="tag-question">
                {{ ++$indexShow }} . {{ $question->content }}
                <span>{{ ($question->required) ? '(*)' : '' }}</span>
            </h4>
            @if ($question->image)
            <div class="image-frame">
                {!! Html::image($question->image, '',[
                    'class' => 'show-img-question images',
                ]) !!}
                <div class="middle">
                    <div class="text" data-video="{{ $question->video ?: '' }}"><i class="fa {{ $question->video ? 'fa-play' : 'fa-eye' }}"></i></div>
                </div>
                <span class="cz-label fa {{ $question->video ? 'fa-video-camera' : 'fa-image'}}"></span>
            </div>
            @endif
            <ul class="data-list">
                @foreach($question->answers as $temp => $answer)
                    @if ($answer->update >= 0)
                        @php
                            $checked = '';
                            $maxUpdate = $question->answers->max('update');
                        @endphp
                        @if ($tempAnswers)
                            @foreach($tempAnswers as $tempAnswer)
                                <?php $checked = ($tempAnswer['answer_id'] == $answer->id) ? $tempAnswer['content'] : $checked; ?>
                            @endforeach
                        @endif
                        <li class="{{ ($question->required) ?  'required': '' }}">
                            @switch($answer->type)
                                @case(config('survey.type_radio'))
                                    <div class="type-radio-answer row">
                                        <div class="box-radio col-md-1">
                                            {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                                'id' => "$key$temp",
                                                'class' => 'option-choose input-radio',
                                                'temp-as' => $answer->id,
                                                'temp-qs' => $question->id,
                                                ($checked) ? 'checked = checked' : null,
                                            ]) }}
                                            {{ Form::label($key . $temp, ' ', [
                                                'class' => 'label-radio',
                                            ]) }}
                                            <div class="check"><div class="inside"></div></div>
                                        </div>
                                        <div class="col-md-11">{{ $answer->content }}</div>
                                    </div>
                                    @if ($answer->image)
                                        <div class="image-frame">
                                            {!! Html::image($answer->image, '',[
                                                'class' => 'show-img-answer images',
                                            ]) !!}
                                            <div class="middle">
                                                <div class="text" data-video="{{ $answer->video ?: '' }}"><i class="fa {{ $answer->video ? 'fa-play' : 'fa-eye' }}"></i></div>
                                            </div>
                                            <span class="cz-label fa {{ $answer->video ? 'fa-video-camera' : 'fa-image'}}"></span>
                                        </div>
                                    @endif
                                    @breakswitch
                                @case(config('survey.type_checkbox'))
                                    <div class="type-checkbox-answer row">
                                        <div class="checkbox-answer col-md-1">
                                            {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                                'id' => "$key$temp",
                                                'class' => 'input-checkbox',
                                                ($checked) ? 'checked = checked' : null,
                                            ]) }}
                                            {{ Form::label($key . $temp, ' ', [
                                                'class' => 'label-checkbox'
                                            ]) }}
                                        </div>
                                        <div class="col-md-11">{{ $answer->content }}</div>
                                    </div>
                                    @if ($answer->image)
                                        <div class="image-frame">
                                            {!! Html::image($answer->image, '',[
                                                'class' => 'show-img-answer images',
                                            ]) !!}
                                            <div class="middle">
                                                <div class="text" data-video="{{ $answer->video ?: '' }}"><i class="fa {{ $answer->video ? 'fa-play' : 'fa-eye' }}"></i></div>
                                            </div>
                                            <span class="cz-label fa {{ $answer->video ? 'fa-video-camera' : 'fa-image'}}"></span>
                                        </div>
                                    @endif
                                    @breakswitch
                                @case(config('survey.type_text'))
                                    {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                        'class' => 'js-elasticArea form-control answer',
                                        'id' => "answer[$question->id][$answer->id]",
                                        'placeholder' => trans('home.answer_here'),
                                    ]) !!}
                                    @breakswitch
                                @case(config('survey.type_time'))
                                    {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                        'class' => 'frm-time form-control',
                                        'id' => "answer[$question->id]",
                                        'placeholder' => trans('home.choose_time')
                                    ]) !!}
                                    @breakswitch
                                @case(config('survey.type_date'))
                                    {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                        'class' => 'form-control frm-date-2',
                                        'id' => "answer[$question->id]",
                                        'placeholder' => trans('home.choose_date'),
                                    ]) !!}
                                    @breakswitch
                                @case(config('survey.type_other_radio'))
                                        <div class="type-radio-answer row">
                                            <div class="box-radio col-md-1">
                                                {{ Form::radio("answer[$question->id]", $answer->id, '', [
                                                    'id' => "$key$temp",
                                                    'class' => 'input-radio option-add',
                                                    'temp-as' => $answer->id,
                                                    'temp-qs' => $question->id,
                                                    'url' => action('TempController@addTemp', config('temp.text_other')),
                                                    ($checked) ? 'checked = checked' : null,
                                                ]) }}
                                                {{ Form::label($key.$temp,' ', [
                                                    'class' => 'label-radio',
                                                ]) }}
                                                <div class="check"><div class="inside"></div></div>
                                            </div>
                                            <div class="col-md-2 label-other">{{ trans('home.other') }}</div>
                                            <div class="append-input col-md-8 append-as{{ $question->id }}">
                                                @if ($checked)
                                                    {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                                        'class' => 'box-orther js-elasticArea animated zoomIn form-control input' . $question->id,
                                                        'placeholder' => trans('home.answer_here'),
                                                        'required' => true,
                                                    ]) !!}
                                                @endif
                                            </div>
                                        </div>
                                    @breakswitch
                                @case(config('survey.type_other_checkbox'))
                                    <div class="type-checkbox-answer row">
                                        <div class="checkbox-answer col-md-1">
                                            {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                                'id' => "$key$temp",
                                                'class' => 'input-checkbox option-add',
                                                'temp-as' => $answer->id,
                                                'temp-qs' => $question->id,
                                                'url' => action('TempController@addTemp', config('temp.text_other')),
                                                ($checked) ? 'checked = checked' : null,
                                            ]) }}
                                            {{ Form::label($key.$temp, ' ', [
                                                'class' => 'label-checkbox',
                                            ]) }}
                                        </div>
                                        <div class="col-md-2 label-other">{{ trans('home.other') }}</div>
                                        <div class="col-md-8 append-input-checkbox append-as{{ $question->id }}">
                                            @if ($checked)
                                                {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                                    'class' => 'box-orther js-elasticArea animated zoomIn form-control input' . $question->id,
                                                    'required' => true,
                                                    'placeholder' => trans('home.answer_here'),
                                                ]) !!}
                                            @endif
                                        </div>
                                    </div>
                                @breakswitch
                            @endswitch
                        </li>
                    @endif
                @endforeach
            </ul>
            @if ($errors->has('answer.' . $question->id))
                <div class="alert alert-danger alert-message">
                    {{ $errors->first('answer.' . $question->id) }}
                </div>
            @endif
            @if ($errors->has('answer.' . $question->id . '.' . $question->answers->first()->id))
                <div class="alert alert-danger alert-message">
                    {{ $errors->first('answer.' . $question->id . '.' . $question->answers->first()->id) }}
                </div>
            @endif
        </div>
    @endif
@endforeach
{!! $surveyAll->links() !!}
