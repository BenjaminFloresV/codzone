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
                    Estas a punto de eliminar a la desarrolladora <strong id="replaceName"></strong> ¿Estás seguro de hacerlo?

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
                <th><abbr title="Played">Nombre</abbr></th>
                <th><abbr title="Won">Empleados</abbr></th>
                <th><abbr title="Drawn">Fundación</abbr></th>
                <th><abbr title="Drawn">Logo</abbr></th>
                <th><abbr title="Drawn">Acciones</abbr></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($allData as $data): ?>
        <tr>
            <!-- is-selected para seleccionar xd -->
            <th><?=$data['company_id']?></th>
            <td ><?=$data['name']?></td>
            <td><?=$data['employees']?></td>
            <td><?=$data['foundation']?></td>
            <td>
                <figure class="image is-64x64 is-right">
                    <img id="blah" src="/uploads/images/company/<?=$data['image']?>?<?=time()?>" alt="your image" />
                </figure>
            </td>
            <td>
                <a href="<?=BASE_URL?>/admin/desarrolladoras/editar/<?=$data['company_id']?>" class="button is-warning">Editar</a>
                <button class="button is-danger js-modal-trigger delete-company" data-target="modal-js-example">Eliminar</button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>



