<li class="title-question animated zoomIn row question{{ $number }}" question="{{ $number }}">
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
    <div class="choose-action row">
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
                                'id' => 'text' . $number,
                            ]) }}
                            {{ Form::label('text' . $number, ' ') }}
                        </div>
                    </div>
                </div>
                </li>
            </ul>
        </div>
    </div>
</li>
