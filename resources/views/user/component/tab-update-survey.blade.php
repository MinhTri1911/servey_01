<div class="detail-survey">
    {!! Form::open([
        'id' => 'wrapped-update',
        'class' => 'wizard-form',
        'action' => ['SurveyController@updateSurveyContent',
            $survey->id,
            $survey->token_manage
        ],
        'novalidate' => 'novalidate',
        'enctype' => 'multipart/form-data',
    ]) !!}
        <div class="update-content-survey wizard-branch wizard-wrapper">
            <div class="step row wizard-step">
                <div class="title-create row">
                    <div class="col-md-9">
                        <h3 class="wizard-header">
                            {{ trans('home.choose_question') }}
                        </h3>
                    </div>
                    <div class="col-md-3">
                        <span>
                            {{ Html::image(config('settings.image_path_system') . 'arrow-down1.png', '', [
                                'class' => 'animated bounceInDown'
                            ]) }}
                        </span>
                    </div>
                </div>
                <div class="wizard-hidden create-question-validate row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="alert alert-warning warning-center">
                            {{ trans('info.create_invalid') }}
                        </div>
                    </div>
                </div>
                <ul class="container-add-question">
                    <div class="add-question col-md-1"></div>
                    @foreach ($survey->questions as $question)
                        @php
                            $number = $question->id;
                        @endphp
                        <li class="title-question animated zoomIn row question{{ $number }}"
                            question="{{ $number }}"
                            temp-qs="{{ count($question->answers) - 1 }}"
                            trash="{{ count($question->answers) }}">
                            <div>
                                <div class="row">
                                    <div class="text-question col-md-10">
                                        {!! Form::text("txt-question[question][$number]", $question->content, [
                                            'placeholder' => trans('home.enter_question_here'),
                                            'required' => true,
                                        ]) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <div class="img-trash row">
                                            <a class="glyphicon glyphicon-picture col-md-3"></a>
                                            {!! Form::file("image[question][$number]", [
                                                'class' => 'hidden-image fileImg' . $number,
                                            ]) !!}
                                            <a class="glyphicon glyphicon-trash col-md-1" id-question="{{ $number }}"></a>
                                            <a class="glyphicon glyphicon-sort col-md-3"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-image-question{{ $number }}
                                {{ $question->image ? 'show-image-question' : 'div-image-question' }}
                                animated slideInDown">
                                {!! Html::image($question->image ?: config('temp.image_default'), '', [
                                    'class' => 'set-image image-question' . $number,
                                ]) !!}
                                <span class="remove-image-question glyphicon glyphicon-remove"
                                    id-question="{{ $number }}"
                                    data-id-image="{{ $number }}">
                                </span>
                            </div>
                            @foreach ($question->answers as $answer)
                                @switch($answer->type)
                                    @case(config('survey.type_radio'))
                                        <div class="clear clear-as{{ $number . $loop->index }}"></div>
                                        <div class="div-content-answer qs-as{{ $number . $loop->index }} row">
                                            <div class="row">
                                                <div class="col-md-1 div-radius"></div>
                                                <div class="col-md-9">
                                                    <div class="div-text-answer">
                                                        {!! Form::text("txt-question[answers][$number][][" . config('survey.type_radio') . "]", $answer->content, [
                                                            'placeholder' => trans('home.enter_answer_here'),
                                                            'required' => true,
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="remove-answer col-md-2">
                                                    {!! Html::image(config('settings.image_path_system') . 'img-answer.png', '', [
                                                        'class' => 'picture-answer',
                                                    ]) !!}
                                                    {!! Form::file("image[answers][$number][]", [
                                                        'class' => 'hidden-image fileImgAnswer' . $number . $loop->index,
                                                    ]) !!}
                                                    <a class="glyphicon glyphicon-remove btn-remove-answer"
                                                        id-as="{{ $number . $loop->index }}"
                                                        num="{{ $number  }}"
                                                        data-answerId="{{ $answer->id }}">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="content-image-answer{{ $number . $loop->index }}
                                                {{ $answer->image ? 'show-image-answer' : 'div-image-answer' }}
                                                show-update animated slideInDown">
                                                {!! Html::image($answer->image, '', [
                                                    'class' => 'set-image-answer image-answer' . $number . $loop->index,
                                                ]) !!}
                                                <span class="remove-image-answer glyphicon glyphicon-remove"
                                                    id-answer="{{ $number . $loop->index }}"
                                                    data-answerId="{{ $answer->id }}">
                                                </span>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_checkbox'))
                                        <div class="clear clear-as{{ $number }}"></div>
                                        <div class="div-content-answer qs-as{{ $number . $loop->index}} row">
                                            <div class="row">
                                                <div class="col-md-1 div-square"></div>
                                                <div class="col-md-9">
                                                    <div class="div-text-answer">
                                                        {!! Form::text("txt-question[answers][$number][][" . config('survey.type_checkbox') . "]", $answer->content, [
                                                            'placeholder' => trans('home.enter_answer_here'),
                                                            'required' => true,
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="remove-answer col-md-2">
                                                    {!! Html::image(config('settings.image_path_system') . 'img-answer.png', '', [
                                                        'class' => 'picture-answer',
                                                    ]) !!}
                                                    {!! Form::file("image[answers][$number][]", [
                                                        'class' => 'hidden-image fileImgAnswer' . $number . $loop->index,
                                                    ]) !!}
                                                    <a class="glyphicon glyphicon-remove btn-remove-answer"
                                                        id-as="{{ $number . $loop->index}}"
                                                        num="{{ $number }}"
                                                        data-answerId="{{ $answer->id }}">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="content-image-answer{{ $number . $loop->index }}
                                                {{ $answer->image ? 'show-image-answer' : 'div-image-answer' }}
                                                show-update animated slideInDown">
                                                {!! Html::image($answer->image, '', [
                                                    'class' => 'set-image-answer image-answer' . $number . $loop->index,
                                                ]) !!}
                                                <span class="remove-image-answer glyphicon glyphicon-remove"
                                                    id-answer="{{ $number . $loop->index }}"
                                                    data-answerId="{{ $answer->id }}">
                                                </span>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_text'))
                                        <div class="clear"></div>
                                        <div class="div-content-answer">
                                            <div class="col-md-1 text-icon">
                                                <span class="glyphicon glyphicon-pencil"></span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="text-empty">
                                                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_text') . "]", '', [
                                                        'placeholder' => trans('temp.text'),
                                                        'readonly' => true,
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_time'))
                                        <div class="clear"></div>
                                        <div class="div-content-answer">
                                            <div class="col-md-1 text-icon" >
                                                <span class="glyphicon glyphicon-time"></span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="text-empty">
                                                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_time') . "]", '', [
                                                        'placeholder' => trans('temp.time_text'),
                                                        'readonly' => true,
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_date'))
                                        <div class="clear"></div>
                                        <div class="div-content-answer">
                                            <div class="col-md-1 text-icon" >
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="text-empty">
                                                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_date') . "]", '', [
                                                        'placeholder' => trans('temp.date_month'),
                                                        'readonly' => true,
                                                    ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_other_radio'))
                                        <div class="clear temp-other{{ $number }}"></div>
                                        <div class="temp div-content-answer answer-other{{ $number }} row">
                                            <div class="col-md-1 div-radius"></div>
                                            <div class="col-md-10">
                                                <div class="container-text-other div-text-answer">
                                                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_other_radio') . "]", '', [
                                                        'readonly' => 'true',
                                                        'placeholder' => trans('home.other'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="remove-answer col-md-1">
                                                <a class="glyphicon glyphicon-remove remove-other"
                                                    id-qs="{{ $number }}"
                                                    num="{{ $number }}"
                                                    data-answerId="{{ $answer->id }}">
                                                </a>
                                            </div>
                                        </div>
                                        @breakswitch
                                    @case(config('survey.type_other_checkbox'))
                                        <div class="clear temp-other{{ $number }}"></div>
                                        <div class="temp div-content-answer answer-other{{ $number }} row">
                                            <div class="col-md-1 div-square"></div>
                                            <div class="col-md-10">
                                                <div class="container-text-other div-text-answer">
                                                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_other_checkbox') . "]", '', [
                                                        'readonly' => 'true',
                                                        'placeholder' => trans('home.other'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="remove-answer col-md-1">
                                                <a class="glyphicon glyphicon-remove remove-other"
                                                    id-qs="{{ $number }}"
                                                    num="{{ $number }}"
                                                    data-answerId="{{ $answer->id }}">
                                                </a>
                                            </div>
                                        </div>
                                    @breakswitch
                                @endswitch
                            @endforeach
                            @if (in_array($question->answers->first()->type, [
                                    config('survey.type_radio'),
                                    config('survey.type_checkbox'),
                                ]))
                                <div class="clear temp-other{{ $number }}"></div>
                            @endif
                            <div class="choose-action row">
                                @if ($question->answers->first()->type == config('survey.type_radio'))
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3">
                                        {!! Form::button(trans('home.add_option'), [
                                            'class' => 'add-radio',
                                            'id-as' => $number,
                                            'typeId' => config('survey.type_radio'),
                                            'url' => action('TempController@addTemp', config('temp.radio_answer')),
                                        ]) !!}
                                    </div>
                                    <div class="col-md-3">
                                        @if ($question->answers->last()->type == config('survey.type_other_radio'))
                                            {!! Form::button(trans('home.add_other'), [
                                                'class' => 'add-radio-other other' . $number . ' div-hidden',
                                                'typeId' => config('survey.type_other_radio'),
                                                'url' => action('TempController@addTemp', config('temp.other_radio')),
                                            ]) !!}
                                        @else
                                            {!! Form::button(trans('home.add_other'), [
                                                'class' => 'add-radio-other other' . $number,
                                                'typeId' => config('survey.type_other_radio'),
                                                'url' => action('TempController@addTemp', config('temp.other_radio')),
                                            ]) !!}
                                        @endif
                                    </div>
                                    <div class="col-md-3" class="div-require">
                                        <ul class="data-list">
                                            <li>
                                            <div class="row">
                                                <div class="col-md-6 label-require">
                                                    <strong><a>{{ trans('temp.require') }}?</a></strong>
                                                </div>
                                                <div class="col-md-5 button-require">
                                                    <div class="class-option-require slideThree">
                                                        {{ Form::checkbox("checkboxRequired[question][$number]", $number, '', [
                                                            'id' => 'radio' . $number,
                                                            $question->required ? ('checked = checked') : '',
                                                        ]) }}
                                                        {{ Form::label('radio' . $number, ' ') }}
                                                    </div>
                                                </div>
                                            </div>
                                            </li>
                                        </ul>
                                    </div>
                                @elseif ($question->answers->first()->type == config('survey.type_checkbox'))
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3">
                                        {!! Form::button(trans('home.add_option'), [
                                            'class' => 'add-checkbox',
                                            'id-as' => $number,
                                            'typeId' => config('survey.type_checkbox'),
                                            'url' => action('TempController@addTemp', config('temp.checkbox_answer')),
                                        ]) !!}
                                    </div>
                                    <div class="col-md-3">
                                        @if ($question->answers->last()->type == config('survey.type_other_checkbox'))
                                            {!! Form::button(trans('home.add_other'), [
                                                'class' => 'add-checkbox-other other' . $number . ' div-hidden',
                                                'typeId' => config('survey.type_other_checbox'),
                                                'url' => action('TempController@addTemp', config('temp.other_checkbox')),
                                            ]) !!}
                                        @else
                                            {!! Form::button(trans('home.add_other'), [
                                                'class' => 'add-checkbox-other other' . $number,
                                                'typeId' => config('survey.type_other_checbox'),
                                                'url' => action('TempController@addTemp', config('temp.other_checkbox')),
                                            ]) !!}
                                        @endif
                                    </div>
                                    <div class="col-md-3" class="div-require">
                                        <ul class="data-list">
                                            <li>
                                            <div class="row">
                                                <div class="col-md-6 label-require">
                                                    <strong><a>{{ trans('temp.require') }}?</a></strong>
                                                </div>
                                                <div class="col-md-5 button-require">
                                                    <div class="class-option-require slideThree">
                                                        {{ Form::checkbox("checkboxRequired[question][$number]", $number, '', [
                                                            'id' => 'radio' . $number,
                                                            $question->required ? ('checked = checked') : '',
                                                        ]) }}
                                                        {{ Form::label('radio' . $number, ' ') }}
                                                    </div>
                                                </div>
                                            </div>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <div class="offset-label col-md-6"></div>
                                        <div class="col-md-3" class="div-require">
                                            <ul class="data-require data-list">
                                                <li>
                                                    <div class="row">
                                                        <div class="col-md-6 time-text label-require">
                                                            <strong><a>{{ trans('temp.require') }}?</a></strong>
                                                        </div>
                                                        <div class="col-md-5 button-require">
                                                           <div class="slideThree">
                                                                {{ Form::checkbox("checkboxRequired[question][$number]", $number, '', [
                                                                    'id' => 'date' . $number,
                                                                    $question->required ? ('checked = checked') : '',
                                                                ]) }}
                                                                {{ Form::label('date' . $number, ' ') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                    <div class="hide"></div>
                </ul>
                <div class="row">
                    <div class="col-md-2">
                        {!! Form::submit('Update', [
                            'class' => 'btn-change-survey btn btn-info submit-answer',
                        ]) !!}
                    </div>
                    <div class="row col-md-4 col-md-offset-6 parent-option">
                        <div class="col-md-2 col-md-offset-1 container-option-image">
                            <span class="span-option-1">
                                {{ trans('temp.one_choose') }}
                            </span>
                            {{ Html::image(config('settings.image_path_system') . 'radio.png', '', [
                                'class' => 'image-type-option',
                                'url' => action('TempController@addTemp', config('temp.radio_question')),
                                'id' => 'radio-button',
                                'typeId' => config('survey.type_radio'),
                            ]) }}
                        </div>
                        <div class="col-md-2 container-option-image">
                            <span class="span-option-2">
                                {{ trans('temp.multi_choose') }}
                            </span>
                            {{ Html::image(config('settings.image_path_system') . 'checkbox.png', '', [
                                'class' => 'image-type-option',
                                'url' => action('TempController@addTemp', config('temp.checkbox_question')),
                                'id' => 'checkbox-button',
                                'typeId' => config('survey.type_checkbox'),
                            ]) }}
                        </div>
                        <div class="col-md-2 container-option-image">
                            <span class="span-option-3">
                                {{ trans('temp.text') }}
                            </span>
                            {{ Html::image(config('settings.image_path_system') . 'text.png', '', [
                                'class' => 'image-type-option',
                                'url' => action('TempController@addTemp', config('temp.text_question')),
                                'id' => 'text-button',
                                'typeId' => config('survey.type_text'),
                            ]) }}
                        </div>
                        <div class="col-md-2 container-option-image">
                            <span class="span-option-4">
                                {{ trans('temp.time') }}
                            </span>
                            {{ Html::image(config('settings.image_path_system') . 'time.png', '', [
                                'class' => 'image-type-option',
                                'url' => action('TempController@addTemp', config('temp.time_question')),
                                'id' => 'time-button',
                                'typeId' => config('survey.type_time'),
                            ]) }}
                        </div>
                        <div class="col-md-2 container-option-image">
                            <span class="span-option-5">
                                {{ trans('temp.date') }}
                            </span>
                            {{ Html::image(config('settings.image_path_system') . 'type-date.png', '', [
                                'class' => 'image-type-option',
                                'url' => action('TempController@addTemp', config('temp.date_question')),
                                'id' => 'date-button',
                                'typeId' => config('survey.type_date'),
                            ]) }}
                        </div>
                    </div>
                    {!! Form::hidden('del-question', '') !!}
                    {!! Form::hidden('del-answer', '') !!}
                    {!! Form::hidden('del-question-image', '') !!}
                    {!! Form::hidden('del-answer-image', '') !!}
                </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>

