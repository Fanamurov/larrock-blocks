<div id="dashboard-blocks" class="dashboard-item uk-width-small-1-2 uk-width-medium-1-4">
    <div class="uk-panel uk-alert">
        <p class="uk-h3"><a href="/admin/{{ $component->name }}">{{ $component->title }}</a></p>
        @if(count($data) > 0)
            <p class="uk-margin-bottom-remove">Доступны в шаблонах:</p>
        @endif
        @foreach($data as $value)
            <span class="uk-badge uk-badge-notification">{{ $value->url }}</span>
        @endforeach
        <div class="uk-clearfix uk-margin-top"></div>
        <a href="/admin/blocks/create" class="uk-button uk-button-primary">Создать блок</a>
    </div>
</div>