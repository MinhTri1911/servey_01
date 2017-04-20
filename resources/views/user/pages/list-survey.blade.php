@extends('user.master')
@section('content')
    <div id="survey_container" class="survey_container animated zoomIn wizard" novalidate="novalidate">
        <div id="top-wizard">
            <div class="container-menu-wizard row">
                <div class="menu-wizard col-md-10 active-answer active-menu">
                    {{ trans('home.list_survey') }}
                </div>
            </div>
        </div>
        <div class="container-list-answer">
            <div class="middle-content-detail wizard-branch wizard-wrapper">
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
                <div class="div-table-list" style="">
                    <div class="table-list-row row">
                        <div class="col-md-3">
                            <ul class="nav nav-tabs tabs-left sideways">
                                <li class=" active">
                                    <a href="#home-v" data-toggle="tab" style="">
                                        <span class="glyphicon glyphicon-th-list"></span>
                                        {{ trans('home.your_survey') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#profile-v" data-toggle="tab">
                                        <span class="glyphicon glyphicon-envelope"></span>
                                        {{ trans('home.survey_invited') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#messages-v" data-toggle="tab">
                                        <span class="glyphicon glyphicon-dashboard"></span>
                                        {{ trans('home.survey_closed') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9">
                          <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="home-v">
                                    @include('user.pages.surveys_tab')
                                </div>
                                <div class="tab-pane" id="profile-v">
                                    @include('user.pages.list-invited')
                                </div>
                                <div class="tab-pane" id="messages-v">
                                    <div >
                                        <table class="table-list-survey table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('survey.name') }}</th>
                                                    <th>{{ trans('survey.date_create') }}</th>
                                                    <th>{{ trans('survey.status') }}</th>
                                                    <th></th>
                                                    <th>{{ trans('survey.setting') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($surveys as $survey)
                                                    @if (!$survey->status || !$survey->is_open || in_array($survey->id, $settings))
                                                        <tr>
                                                            <td>
                                                                {{ $loop->iteration }}.
                                                                <a href="{{ action(($survey->feature)
                                                                    ? 'AnswerController@answerPublic'
                                                                    : 'AnswerController@answerPrivate', [
                                                                        'token' => $survey->token,
                                                                ]) }}">
                                                                {{ $survey->title }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                {{ $survey->created_at->format('M d Y') }}
                                                            </td>
                                                            <td class="margin-center" colspan="2">
                                                                {{ trans('survey.closed') }}
                                                            </td>
                                                            <td class="margin-center">
                                                                <a href="{{ action('AnswerController@show', [
                                                                    'token' => $survey->token_manage,
                                                                    'type' => $survey->feature,
                                                                ]) }}" class="glyphicon glyphicon-cog"></a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="bottom-wizard bottom-wizard-anwser"></div>
        </div>
    </div>
@endsection
