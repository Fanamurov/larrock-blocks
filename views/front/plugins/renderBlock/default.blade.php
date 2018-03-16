<section class="larrock-block larrock-block-{{ $data->id }} larrock-block-{{ $data->url }} uk-position-relative @role('Админ|Модератор') larrock-admin-block @endrole">
    @role('Админ|Модератор')
    <a class="admin_edit" href="/admin/blocks/{{ $data->id }}/edit">Редактировать</a>
    @endrole
    {!! $data->description_render !!}
</section>