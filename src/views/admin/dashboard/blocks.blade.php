<div class="uk-margin-bottom uk-width-1-1 uk-width-1-4@m">
    <h4 class="panel-p-title"><a href="/admin/{{ $component->name }}">{{ $component->title }}</a></h4>
    <div class="uk-card uk-card-default uk-card-small uk-margin-remove-top">
        <div class="uk-card-body">
            @if(count($data) > 0)
                <p class="uk-margin-remove-bottom">Доступны в шаблонах:</p>
                @foreach($data as $value)
                    <a href="/admin/{{ $component->name }}/{{ $value->id }}/edit" class="uk-label uk-label-success uk-text-lowercase">{{ $value->url }}</a>
                @endforeach
            @else
                <p>Блоков еще нет</p>
            @endif
            <p>
                <a href="/admin/{{ $component->name }}/create" class="uk-button uk-button-default uk-width-1-1">Создать блок</a>
            </p>
        </div>
    </div>
</div>