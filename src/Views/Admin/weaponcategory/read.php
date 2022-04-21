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
                    Estas a punto de eliminar la categoría <strong id="replaceName"></strong> ¿Estás seguro de hacerlo?

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
    <?php include __DIR__."/../session-messages/success-error.phtml"?>
    <table class="table is-fullwidth">
        <thead>
        <tr>
            <th><abbr title="Position">Id</abbr></th>
            <th><abbr title="Played">Categoría</abbr></th>
            <th><abbr title="Drawn">Imagen</abbr></th>
            <th><abbr title="Drawn">Acciones</abbr></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allData as $data): ?>
            <tr>
                <!-- is-selected para seleccionar xd -->
                <th><?=$data['wpcategory_id']?></th>
                <td ><a href="#" class="is-link"><?=$data['name']?></a></td>
                <td>
                    <figure class="image is-64x64 is-right">
                        <img id="blah" src="/uploads/images/weapon_category/<?=$data['image']?>?<?=time()?>" alt="your image" />
                    </figure>
                </td>
                <td>
                    <a href="<?=BASE_URL?>/admin/categorias-armas/editar/<?=$data['wpcategory_id']?>" class="button is-warning">Editar</a>
                    <button class="button is-danger js-modal-trigger delete-wp-category" data-target="modal-js-example">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>
