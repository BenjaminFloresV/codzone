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
                    Estas a punto de eliminar el juego <strong id="replaceName"></strong> ¿Estás seguro de hacerlo?

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
                <th><abbr title="Played">Desarrolladora</abbr></th>
                <th><abbr title="Won">Título</abbr></th>
                <th>Nombre Corto</th>
                <th><abbr title="Drawn">Lanzamiento</abbr></th>
                <th><abbr title="Drawn">Logo</abbr></th>
                <th><abbr title="Drawn">Acciones</abbr></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($allData as $data): ?>
        <tr>
            <!-- is-selected para seleccionar xd -->
            <th class="is-vcentered"><?=$data['game_id']?></th>
            <td class="is-vcentered" ><a href="#" class="is-link"><?php echo ( is_null($data['companyName']) ? 'No tiene': $data['companyName'] ) ?></a></td>
            <td class="is-vcentered"><?=$data['name']?></td>
            <td class="is-vcentered"><?=$data['short_name']?></td>
            <td class="is-vcentered"><?=$data['release_date']?></td>
            <td class="is-vcentered">
                <figure class="image is-70x56 is-right">
                    <img id="blah" src="/uploads/images/game/<?=$data['image']?>?<?=time()?>" alt="your image" />
                </figure>
            </td>
            <td class="is-vcentered">
                <a href="/admin/juegos/editar/<?=$data['game_id']?>" class="button is-warning">Editar</a>
                <button class="button is-danger js-modal-trigger delete-game" data-target="modal-js-example">Eliminar</button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>



