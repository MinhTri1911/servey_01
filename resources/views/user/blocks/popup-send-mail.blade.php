<div class="popupBackground">
    <div class="popupCenter">
        <div class="popupInfo animated bounceInDown">
            {!! Form::open(['class' => 'frm-submit-mail']) !!}
                <div>
                    <div>
                        <span class="popup-span">{{ trans('info.email') }}</span>
                        <div class="popup-input">
                            {!! Form::email('emailUser',
                                (Auth::guard()->check()) ? Auth::user()->email : '', [
                                'placeholder' => trans('info.your_email'),
                                'class' => 'input-email form-control',
                                auth()->check() ? ('disabled = true') : '',
                            ]) !!}
                        </div>
                    </div>
                    <div>
                        <span class="popup-span">{{ trans('temp.send_to') }}</span>
                        <div class="popup-input">
                            {!! Form::text('emailsUser', '', [
                                'placeholder' => trans('info.sender_email'),
                                'class' => 'frm-mail-user',
                                'data-role' => 'tagsinput',
                            ]) !!}
                        </div>
                    </div>
                    <div class="validate-send-email animated fadeInDown row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="alert alert-warning warning-login-register">
                                {{ trans('temp.email_invalid') }}
                            </div>
                        </div>
                    </div>
                    <div class="div-send row">
                        <div class="col-md-6">
                            {!! Form::submit(trans('temp.send'),  [
                                'class' => 'btn-send-mail',
                            ]) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::button(trans('temp.cancel'),  [
                                'class' => 'btn-close-popup',
                            ]) !!}
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
