<li class="title-question animated zoomIn row question{{ $number }}"
    question="{{ $number }}"
    temp-qs="{{ config('temp.temp_radio') }}"
    trash="{{ config('temp.trash_question_radio') }}"
>
    <div>
        <div class="row">
            <div class="text-question col-md-10">
                {!! Form::text("txt-question[question][$number]", '', [
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
    <div class="content-image-question{{ $number }} div-image-question animated slideInDown">
        {!! Html::image(config('temp.image_default'), '', [
            'class' => 'set-image image-question' . $number,
        ]) !!}
        <span class="remove-image-question glyphicon glyphicon-remove" id-question="{{ $number }}"></span>
    </div>
    <div class="clear clear-as{{ $number }}0"></div>
    <div class="div-content-answer qs-as{{ $number }}0 row">
        <div class="row">
            <div class="col-md-1 div-radius"></div>
            <div class="col-md-9">
                <div class="div-text-answer">
                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_radio') . "]", '', [
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
                    'class' => 'hidden-image fileImgAnswer' . $number . '0',
                ]) !!}
                <a class="glyphicon glyphicon-remove btn-remove-answer" id-as="{{ $number }}0" num="{{ $number }}">
                </a>
            </div>
        </div>
        <div class="content-image-answer{{ $number }}0 div-image-answer animated slideInDown">
            {!! Html::image(config('temp.image_default'), '', [
                'class' => 'set-image-answer image-answer' . $number . '0',
            ]) !!}
            <span class="remove-image-answer glyphicon glyphicon-remove" id-answer="{{ $number }}0"></span>
        </div>
    </div>
    <div class="clear clear-as{{ $number }}1"></div>
    <div class="div-content-answer qs-as{{ $number }}1 row">
        <div class="row">
            <div class="col-md-1 div-radius"></div>
            <div class="col-md-9">
                <div class="div-text-answer">
                    {!! Form::text("txt-question[answers][$number][][" . config('survey.type_radio') . "]", '', [
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
                    'class' => 'hidden-image fileImgAnswer' . $number . '1',
                ]) !!}
                <a class="glyphicon glyphicon-remove btn-remove-answer" id-as="{{ $number }}1" num="{{ $number }}">
                </a>
            </div>
        </div>
        <div class="content-image-answer{{ $number }}1 div-image-answer animated slideInDown">
            {!! Html::image(config('temp.image_default'), '', [
                'class' => 'set-image-answer image-answer' . $number . '1',
            ]) !!}
            <span class="remove-image-answer glyphicon glyphicon-remove" id-answer="{{ $number }}1"></span>
        </div>
    </div>
    <div class="clear temp-other{{ $number }}"></div>
    <div class="choose-action row">
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
            {!! Form::button(trans('home.add_other'), [
                'class' => 'add-radio-other other' . $number,
                'typeId' => config('survey.type_other_radio'),
                'url' => action('TempController@addTemp', config('temp.other_radio')),
            ]) !!}
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
                            ]) }}
                            {{ Form::label('radio' . $number, ' ') }}
                        </div>
                    </div>
                </div>
                </li>
            </ul>
        </div>
    </div>
</li>
