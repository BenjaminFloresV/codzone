<?php
/* @var array $allData */
?>
<div class="table-container has-background-white-ter">
    <div id="modal-js-example" class="modal">
        <div class="modal-background"></div>

        <div class="modal-content">
            <div class="box">
                <h3 class="subtitle has-text-danger has-text-weight-bold is-size-3">ADVERTENCIA</h3>
                <p class="is-size-4">
                    Estas a punto de eliminar la publicación: <strong id="replaceName"></strong> ¿Estás seguro de hacerlo?

                </p>
                <!-- Your content -->
                <div class="columns is-centered mt-5">
                    <div class="column has-text-centered">
                        <a id="del-anchor" href="" class="button is-danger">Eliminar</a>
                        <button class="button is-warning cancel">Cancelar</button>
                    </div>
                </div>

            </div>
        </div>

        <button class="modal-close is-large" aria-label="close"></button>
    </div>
    <table class="table is-fullwidth">
        <thead>
        <tr>
            <th><abbr title="Position">Id</abbr></th>
            <th>Id Imágenes</th>
            <th><abbr title="Won">Categoría</abbr></th>
            <th><abbr title="Played">Título</abbr></th>
            <th><abbr title="Drawn">Publicación</abbr></th>
            <th><abbr title="Drawn">Acciones</abbr></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allData as $data): ?>
            <tr>
                <!-- is-selected para seleccionar xd -->
                <th><?=$data['tutorial_id']?></th>
                <td><?=$data['images_id']?></td>
                <td ><a href="#" class="is-link"><?=$data['category_id']?></a></td>
                <td><?=$data['title']?></td>
                <td><?=$data['creation_date']?></td>
                <td>
                    <a href="/admin/tutoriales/editar/<?=$data['tutorial_id']?>" class="button is-warning">Editar</a>
                    <button class="button is-danger js-modal-trigger delete-tutorial" data-target="modal-js-example">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>