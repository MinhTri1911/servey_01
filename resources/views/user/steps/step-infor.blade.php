<div class="step1 wizard-hidden step wizard-step current">
    <div class="row">
        <h3 class="label-header col-md-10 wizard-header">
            {{ trans('info.enter_info') }}
        </h3>
        <div class="col-md-6">
            <ul class="data-list">
                <li>
                    <div class="container-infor">
                        {!! Html::image(config('settings.image_path_system') . 'email1.png', '') !!}
                        {!! Form::email('email', (Auth::user() ? Auth::user()->email : ''), [
                            'id' => 'email',
                            'class' => 'required form-control',
                            'placeholder' => trans('info.email'),
                            auth()->check() ? ('readonly = true') : false,
                        ]) !!}
                    </div>
                </li>
                <li>
                    <div class="container-infor">
                        {!! Html::image(config('settings.image_path_system') . 'title1.png', '') !!}
                        {!! Form::text('title', '', [
                            'placeholder' => trans('info.title'),
                            'id' => 'title',
                            'class' => 'required form-control',
                        ]) !!}
                    </div>
                </li>
                {!! Form::text('website', '', [
                    'id' => 'website',
                ]) !!}
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="data-list">
                <li>
                    <div class="container-infor">
                        {!! Html::image(config('settings.image_path_system') . 'name.png', '') !!}
                        {!! Form::text('name', ((Auth::user()) ? Auth::user()->name : ''), [
                            'placeholder' => trans('info.name'),
                            'id' => 'name',
                            'class' => 'required form-control',
                            auth()->check() ? ('readonly = true') : false,
                        ]) !!}
                    </div>
                </li>
            </ul>
            <ul class="data-list">
                <li>
                    <div class="container-infor">
                        {!! Html::image(config('settings.image_path_system') . 'date.png', '') !!}
                        {!! Form::text('deadline', '', [
                            'placeholder' =>  trans('info.duration'),
                            'id' => 'deadline',
                            'class' => 'frm-deadline datetimepicker form-control',
                        ]) !!}
                        {!! Form::label('deadline', trans('info.date_invalid'), [
                            'class' => 'wizard-hidden validate-time error',
                        ]) !!}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="frm-textarea container-infor">
            {!! Html::image(config('settings.image_path_system') . 'description.png', '') !!}
            {!! Form::textarea('description', '', [
                'class' => 'form-control',
                'placeholder' => trans('info.description'),
            ]) !!}
        </div>
    </div>
</div>
