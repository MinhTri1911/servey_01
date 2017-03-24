<div class="clear clear-as{{ $number . $num_as }}"></div>
<div class="div-content-answer qs-as{{ $number . $num_as }} row">
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
            {!! Html::image(config('settings.image_system') . 'img-answer.png', '', [
                'class' => 'picture-answer'
            ]) !!}
            {!! Form::file("image[answers][$number][]", [
                'class' => 'hidden-image fileImgAnswer' . $number . $num_as,
            ]) !!}
            <a class="glyphicon glyphicon-remove btn-remove-answer" id-as="{{ $number . $num_as }}" num="{{ $number }}">
            </a>
        </div>
    </div>
    <div class="content-image-answer{{ $number . $num_as }} div-image-answer animated slideInDown">
        {!! Html::image(config('temp.image_default'), '', [
            'class' => 'set-image-answer image-answer' . $number . $num_as,
        ]) !!}
        <span class="remove-image-answer glyphicon glyphicon-remove" id-answer="{{ $number . $num_as }}"></span>
    </div>
</div>
