<table class="table table">
    <thead class="thead-default">
        <tr>
            <th>
                {{ trans('user.name') }}
            </th>
            <th>
                {{ trans('user.email') }}
            </th>
            <th>
                {{ trans('user.date') }}
            </th>
            <th>
                {{ trans('user.detail') }}
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listUserAnswer as $user)
            <tr>
                <td>
                    {{ $user[0]['name'] }}
                </td>
                <td>
                    {{ $user[0]['email'] }}
                </td>
                <td>
                    {{ Carbon\Carbon::parse($user[0]['created_at'])->format('Y-m-d') }}
                </td>
                <td>
                    <a href="{{ action('AnswerController@showMultiHistory', [
                        'surveyId' => $survey->id,
                        'createdAt' => $user[0]['created_at'],
                        'userId' => $user[0]['sender_id'],
                        'email' => $user[0]['email'],
                    ]) }}">{{ trans('home.link') }}</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

