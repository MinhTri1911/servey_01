<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        @include('user.result.chart')
    </div>
    <div id="menu1" class="tab-pane fade in">
        @include('user.result.detail-result')
    </div>
    @if ($listUserAnswer)
        <div id="menu3" class="tab-pane fade in">
            @include('user.result.users-answer')
        </div>
    @endif
    @if ($history)
        <div id="menu2" class="tab-pane fade in">
            @include('user.result.history')
        </div>
    @endif
</div>
