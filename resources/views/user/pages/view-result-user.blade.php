<div class="multi-history animated fadeInRight wizard" novalidate="novalidate">
    <div class="container-list-result result-by-time">
    <div class="tag-notice-answer row">
        <div class="col-md-10">Bạn đã trả lời survey này {{ count($history) }} lần vào lúc</div>
        <div class="col-md-1"><span class="hidden-result glyphicon glyphicon-remove"></span></div>
    </div>
        <div class="div-show-result wizard-branch wizard-wrapper">
            <div class="tab-bootstrap">
                <ul class="nav nav-tabs">
                    @foreach ($history as $key => $result)
                        @if ($loop->first)
                            <li class="active">
                                <a data-toggle="tab" href="#home">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    {{ Carbon\Carbon::parse($key)->format('Y-m-d H:i') }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a data-toggle="tab" href="#menu{{ $loop->index }}">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    {{ Carbon\Carbon::parse($key)->format('Y-m-d H:i') }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach ($history as $time => $results)
                        <div id="{{ $loop->first ? 'home' : 'menu' . $loop->index }}"
                            class="{{ $loop->first ? 'tab-pane fade in active' : 'tab-pane fade in' }}">
                            @foreach ($survey->questions as $question)
                                <div>
                                    <h4 class="tag-question">
                                        {{ ++$loop->index . '. ' . $question->content }}
                                        <span>{{ ($question->required) ? '(*)' : '' }}</span>
                                    </h4>
                                    @if ($question->image)
                                        {!! Html::image($question->image, '', [
                                            'class' => 'show-img-answer',
                                        ]) !!}
                                    @endif
                                    <ul class="data-list">
                                        @foreach ($question->answers as $answer)
                                            @php
                                                $checked = false;
                                            @endphp
                                            @foreach ($results as $result)
                                                @if ($result['answer_id'] == $answer->id)
                                                    @php
                                                        $checked = $result['content'] ?: true;
                                                    @endphp
                                                    @break
                                                @endif
                                            @endforeach
                                            <li class="{{ ($question->required) ? 'required' : '' }}">
                                                @switch($answer->type)
                                                    @case(config('survey.type_radio'))
                                                        {{ Form::radio("$time", $answer->id, '', [
                                                            'class' => 'option-choose input-radio',
                                                            ($checked) ? 'checked = checked' : null,
                                                        ]) }}
                                                        {{ Form::label($loop->index, $answer->content, [
                                                            'class' => 'label-radio',
                                                        ]) }}
                                                        @breakswitch
                                                    @case(config('survey.type_checkbox'))
                                                        <div>
                                                            {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                                                'class' => 'input-checkbox',
                                                                ($checked) ? 'checked = checked' : null,
                                                            ]) }}
                                                            {{ Form::label($loop->index, $answer->content, [
                                                                'class' => 'label-checkbox'
                                                            ]) }}
                                                        </div>
                                                        @breakswitch
                                                    @case(config('survey.type_text'))
                                                        {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                                            'class' => 'form-control answer',
                                                            'disabled' => 'true',
                                                        ]) !!}
                                                        @breakswitch
                                                    @case(config('survey.type_time'))
                                                        {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                                            'class' => 'frm-time form-control',
                                                            'disabled' => 'true',
                                                        ]) !!}
                                                        @breakswitch
                                                    @case(config('survey.type_date'))
                                                        {!! Form::text("answer[$question->id][$answer->id]", $checked, [
                                                            'class' => 'form-control frm-date-2',
                                                            'disabled' => 'true',
                                                        ]) !!}
                                                        @breakswitch
                                                    @case(config('survey.type_other_radio'))
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                {{ Form::radio("$time", $answer->id, '', [
                                                                    'class' => 'input-radio option-add',
                                                                    ($checked) ? 'checked = checked' : null,
                                                                ]) }}
                                                                {{ Form::label($loop->index, trans('home.other'), [
                                                                    'class' => 'label-radio',
                                                                ]) }}
                                                            </div>
                                                            <div class="append-input col-md-8 append-as{{ $question->id }}">
                                                                @if ($checked)
                                                                    {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                                                        'class' => 'animated zoomIn form-control input' . $question->id,
                                                                        'disabled' => 'true',
                                                                    ]) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @breakswitch
                                                    @case(config('survey.type_other_checkbox'))
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                {{ Form::checkbox("answer[$question->id][$answer->id]", $answer->id, '', [
                                                                    'class' => 'input-checkbox option-add',
                                                                    ($checked) ? ('checked = checked') : '',
                                                                ]) }}
                                                                {{ Form::label($loop->index, trans('home.other'), [
                                                                    'class' => 'label-checkbox',
                                                                ]) }}
                                                            </div>
                                                            <div class="col-md-8 append-input-checkbox append-as{{ $question->id }}">
                                                                @if ($checked)
                                                                    {!! Form::textarea("answer[$question->id][$answer->id]", $checked, [
                                                                        'class' => 'animated zoomIn form-control input' . $question->id,
                                                                        'disabled' => 'true',
                                                                    ]) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @breakswitch
                                                    @default
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="alert alert-danger">
                                                                    {{ trans('view.error') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @breakswitch
                                                @endswitch
                                                @if ($answer->image)
                                                    <div>
                                                        {!! Html::image($answer->image, '',[
                                                            'class' => 'show-img-answer',
                                                        ]) !!}
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
