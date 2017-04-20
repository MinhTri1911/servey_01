<div>
    <table class="table-list-survey table table-hover">
        @forelse ($surveys as $survey)
            @if ($loop->first)
                <thead>
                    <tr>
                        <th>{{ trans('survey.name') }}</th>
                        <th>{{ trans('survey.date_create') }}</th>
                        <th>{{ trans('survey.send') }}</th>
                        <th>{{ trans('survey.share') }}</th>
                        <th>{{ trans('survey.setting') }}</th>
                    </tr>
                </thead>
                <tbody>
            @endif
            @if ($survey->status && $survey->isOpen && !in_array($survey->id, $settings))
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
                        <td>
                            <a class="tag-send-email"
                                data-url="{{ action('SurveyController@inviteUser', [
                                    'id' => $survey->id,
                                    'type' => config('settings.return.view'),
                                ]) }}">
                                <span class="glyphicon glyphicon-send"></span>
                                {{ trans('survey.send') }}
                            </a>
                        </td>
                        @if ($survey->feature)
                            <td>
                                <div class="fb-share-button"
                                    data-href="{{
                                action('AnswerController@answerPublic', $survey->token)
                                }}"
                                    data-layout="button_count"
                                    data-size="small"
                                    data-mobile-iframe="true">
                                    <a class="fb-xfbml-parse-ignore"
                                        target="_blank"
                                        href="{{
                                    action('AnswerController@answerPublic', $survey->token)
                                    }}">
                                        {{ trans('survey.share') }}
                                    </a>
                                </div>
                            </td>
                        @else
                            <td>{{ trans('survey.private') }}</td>
                        @endif
                    <td class="margin-center">
                        <a href="{{ action('AnswerController@show', [
                            'token' => $survey->token_manage,
                            'type' => $survey->feature,
                        ]) }}" class="glyphicon glyphicon-cog"></a>
                    </td>
                </tr>
            @endif
            @if ($loop->last)
                <tbody>
            @endif
        @empty
            <div class="alert alert-warning">
                {{ trans('messages.not_have_results') }}
            </div>
        @endforelse
    </table>
    {{ $surveys->render() }}
</div>
