@extends('user.master')
@section('content')
    <div class="survey_container animated zoomIn wizard" novalidate="novalidate">
        <div id="top-wizard">
            <div class="container-menu-wizard row">
                @if (!in_array(config('settings.key.hideResult'), array_keys($access)) || ($survey->user_id == auth()->id()))
                    <div class="menu-wizard col-md-5 active-answer active-menu">
                        {{ trans('result.answer') }}
                    </div>
                    <div class="menu-wizard col-md-5 active-result col-md-offset-1">
                        {{ trans('result.result') }}
                    </div>
                @else
                    <div class="menu-wizard col-md-8 col-md-offset-2 active-answer active-menu">
                        {{ trans('result.answer') }}
                    </div>
                @endif
                <div class="alert alert-info save-message-success alert-message"></div>
                <div class="alert alert-danger save-message-fail alert-message"></div>
            </div>
        </div>
        <div class="container-list-answer">
            {!! Form::open([
                'id' => 'wrapped',
                'class' => 'wizard-form',
                'novalidate' => 'novalidate',
                'action' => ['ResultController@result', $survey->token],
                'method' => 'POST',
            ]) !!}
                <div class="container-answer wizard-branch wizard-wrapper">
                    <div class="get-title-survey">
                        {{ $survey->title }}
                    </div>
                    <div class="description-survey">
                        <h4>
                            {{ $survey->description }}
                        </h4>
                    </div>
                    @if (Session::has('message'))
                        <div class="alert alert-info alert-message">
                            {{ Session::get('message') }}
                        </div>
                    @endif
                    @if (Session::has('message-fail'))
                        <div class="alert alert-danger alert-message">
                            {{ Session::get('message-fail') }}
                        </div>
                    @endif
                    <div class="container-all-question step row wizard-step ">
                       <div class="container-survey" id="container-survey">
                           @include('user.component.temp-answer')
                       </div>
                    </div>
                </div>
                <div class="required-user">
                    <div class="row" >
                        @if (in_array(config('settings.key.requireAnswer'), array_keys($access))
                            && $access[config('settings.key.requireAnswer')]
                        )
                            @switch($access[config('settings.key.requireAnswer')])
                                @case(config('settings.require.email'))
                                    <div class="div-require-info">
                                        {{ trans('survey.label.require_email') }}
                                        @if ($access[config('settings.key.tailMail')])
                                            ({{ trans('survey.label.require_tailmail') . $access[config('settings.key.tailMail')] }})
                                        @endif
                                    </div>
                                    <div class="col-md-5 col-md-offset-1">
                                        <div class="container-infor">
                                            {!! Html::image(config('settings.image_system') . 'email1.png', '') !!}
                                            {!! Form::email('email-answer', '', [
                                                'id' => 'email',
                                                'class' => 'frm-require-answer form-control',
                                                'placeholder' => trans('login.your_email'),
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('email-answer'))
                                            <div class="alert alert-danger alert-message">
                                                {{ $errors->first('email-answer') }}
                                            </div>
                                        @endif
                                    </div>
                                    @breakswitch
                                @case(config('settings.require.name'))
                                    <div class="div-require-info">
                                        {{ trans('survey.label.require_name') }}
                                    </div>
                                    <div class="col-md-5 col-md-offset-1">
                                        <div class="container-infor">
                                            {!! Html::image(config('settings.image_system') . 'name.png', '') !!}
                                            {!! Form::text('name-answer', '', [
                                                'placeholder' => trans('login.your_name'),
                                                'id' => 'name',
                                                'class' => 'frm-require-answer form-control',
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('name-answer'))
                                            <div class="alert alert-danger alert-message">
                                                {{ $errors->first('name-answer') }}
                                            </div>
                                        @endif
                                    </div>
                                    @breakswitch
                                @default
                                    <div class="div-require-info">
                                        {{ trans('survey.label.require_both') }}
                                        @if ($access[config('settings.key.tailMail')])
                                            ({{ trans('survey.label.require_tailmail') . $access[config('settings.key.tailMail')] }})
                                        @endif
                                    </div>
                                    <div class="col-md-5 col-md-offset-1">
                                        <div class="container-infor">
                                            {!! Html::image(config('settings.image_system') . 'email1.png', '') !!}
                                            {!! Form::email('email-answer', '', [
                                                'id' => 'email',
                                                'class' => 'frm-require-answer form-control',
                                                'placeholder' => trans('login.your_email'),
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('email-answer'))
                                            <div class="alert alert-danger alert-message">
                                                {{ $errors->first('email-answer') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-5 ">
                                        <div class="container-infor">
                                            {!! Html::image(config('settings.image_system') . 'name.png', '') !!}
                                            {!! Form::text('name-answer', '', [
                                                'placeholder' => trans('login.your_name'),
                                                'id' => 'name',
                                                'class' => 'frm-require-answer form-control',
                                            ]) !!}
                                        </div>
                                        @if ($errors->has('name-answer'))
                                            <div class="alert alert-danger alert-message">
                                                {{ $errors->first('name-answer') }}
                                            </div>
                                        @endif
                                    </div>
                                    @breakswitch
                            @endswitch
                        @endif
                    </div>
                    @if (Session::has('message-validate-tailmail'))
                        <div class="row">
                            <div class="col-md-5 col-md-offset-1">
                                <div class="alert alert-danger alert-message">
                                    {{ Session::get('message-validate-tailmail') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div id="bottom-wizard">
                <?php $inArray = in_array(config('settings.key.limitAnswer'), array_keys($access));
                    $check = ($inArray && ($access[config('settings.key.limitAnswer')]
                    || !$access[config('settings.key.limitAnswer')])); ?>
                    @if ($survey->status
                        && (Carbon\Carbon::parse($survey->deadline)->gt(Carbon\Carbon::now()) || empty($survey->deadline))
                        && $check)
                        @if (auth()->check())
                            {!! Form::button(trans('home.save'), [
                                'class' => 'submit-answer btn btn-info save-survey',
                                'survey-id' => $survey->id,
                                'data-url' => action('User\SaveTempController@store'),
                                'feature' => $survey->feature,
                                'user-id' => $survey->user_id,
                                'id' => 'btn-save',
                            ]) !!}
                            {!! Form::button(trans('home.load'), [
                                'class' => 'submit-answer btn btn-info show-survey',
                                'survey-id' => $survey->id,
                                'data-url' => action('User\SaveTempController@show'),
                                'id' => 'btn-load',
                            ]) !!}
                        @endif
                        {!! Form::submit(trans('home.finish'), [
                            'class' => 'submit-answer btn btn-info',
                        ]) !!}
                    @endif
                </div>
            {!! Form::close() !!}
        </div>
        @include('user.pages.result')
    </div>
@endsection
