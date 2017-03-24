@extends('user.master')
@section('content')
    <div id="survey_container" class="survey_container animated zoomIn wizard" novalidate="novalidate">
        <div id="top-wizard">
            <div class="container-menu-wizard row">
                <div class="menu-wizard col-md-10 active-answer active-menu">
                    {{ trans('home.detail_survey') }}
                </div>
            </div>
        </div>
        <div class="container-list-answer">
            <div class="middel-content-detail wizard-branch wizard-wrapper">
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
                <div class="tab-class">
                    <section class="tabs">
                        {{ Form::radio('radio-set', config('temp.radio.label_info'), '', [
                            'id' => 'tab-1',
                            'class' => 'tab-choose tab-selector-1',
                            'checked' => 'checked',
                        ]) }}
                        {{ Form::label('tab-1', trans('survey.information'), [
                            'class' => 'label tab-label-1',
                        ]) }}
                        {{ Form::radio('radio-set', config('temp.radio.label_setting'), '', [
                            'id' => 'tab-2',
                            'class' => 'tab-choose tab-selector-2',
                        ]) }}
                        {{ Form::label('tab-2', trans('survey.setting'), [
                            'class' => 'label tab-label-2',
                        ]) }}
                        {{ Form::radio('radio-set', config('temp.radio.label_result'), '', [
                            'id' => 'tab-3',
                            'class' => 'tab-choose tab-selector-3',
                        ]) }}
                        {{ Form::label('tab-3', trans('survey.result'), [
                            'class' => 'label tab-label-3',
                        ]) }}
                        {{ Form::radio('radio-set', config('temp.radio.label_detail_result'), '', [
                            'id' => 'tab-4',
                            'class' => 'tab-choose tab-selector-4',
                        ]) }}
                        {{ Form::label('tab-4', trans('survey.specific_result'), [
                            'class' => 'label tab-label-4',
                        ]) }}
                        <div class="clear-shadow"></div>
                        <div class="content">
                            <div class="content-1">
                                @include('user.component.tab-info')
                            </div>
                            <div class="content-2 div-hidden">
                                @include('user.component.tab-setting')
                            </div>
                            <div class="content-3 div-hidden">
                                 <div class="div-result-survey">
                                    <div class="div-table-list">
                                        <div class="table-list-row row">
                                            <div class="col-md-3">
                                                <ul class="nav nav-tabs tabs-left sideways">
                                                    <li class=" active">
                                                        <a href="#home-v" data-toggle="tab" style="">
                                                            <span class="glyphicon glyphicon-adjust"></span>
                                                            {{  trans('survey.overview') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#profile-v" data-toggle="tab">
                                                            <span class="glyphicon glyphicon-asterisk"></span>
                                                            {{  trans('survey.see_detail') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#messages-v" data-toggle="tab">
                                                            <span class="glyphicon glyphicon-list"></span>
                                                            {{ trans('survey.users_anwser') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#settings-v" data-toggle="tab">
                                                            <span class="glyphicon glyphicon-export"></span>
                                                            {{ trans('survey.export') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="home-v">
                                                        @include('user.result.chart')
                                                    </div>
                                                    <div class="tab-pane" id="profile-v">
                                                        @include('user.result.detail-result')
                                                    </div>
                                                    <div class="tab-pane" id="messages-v">
                                                        @include('user.result.users-answer')
                                                    </div>
                                                    <div class="tab-pane" id="settings-v">
                                                        <div class="excel row">
                                                            <div class="export-excel col-md-4">
                                                                {!! Html::image(config('settings.image_path_system') . 'excel.png', '') !!}
                                                                <span>
                                                                    {{ trans('survey.exportExecl') }}
                                                                </span>
                                                            </div>
                                                            <div class="col-md-1"></div>
                                                            <div class="export-PDF col-md-4">
                                                                {!! Html::image(config('settings.image_path_system') . 'pdf.png', '') !!}
                                                                <span>
                                                                    {{ trans('survey.exportPDF') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        {!! Form::open(['action' => [
                                                                'User\ExportController@export',
                                                                'id' => $survey->id,
                                                                'type'=>'excel'
                                                            ],
                                                            'method' => 'GET',
                                                        ]) !!}
                                                            {!! Form::submit('', [
                                                                'class' => 'div-hidden exportExcel',
                                                            ]) !!}
                                                        {!! Form::close() !!}
                                                        {!! Form::open(['action' => [
                                                                'User\ExportController@export',
                                                                'id' => $survey->id,
                                                                'type'=>'PDF'
                                                            ],
                                                            'method' => 'GET',
                                                        ]) !!}
                                                            {!! Form::submit('', [
                                                                'class' => 'div-hidden exportPDF',
                                                            ]) !!}
                                                        {!! Form::close() !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-4 div-hidden">
                                @include('user.component.tab-update-survey')
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div id="bottom-wizard bottom-wizard-anwser"></div>
        </div>
    </div>
@endsection

