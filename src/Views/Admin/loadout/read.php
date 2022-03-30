<?php
/* @var array $allData */
/* @var array $selects */


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
    <div class="is-block">
        <form class="is-flex is-justify-content-end pt-3 pr-3" method="GET" action="http://localhost:8001/admin/clases">
            <div class="field is-flex">
                <label class="label is-align-self-center" for="game">Juego:</label>
                <div class="control ml-3 mr-3">
                    <div class="select">
                        <select name="game">
                            <?php foreach ($selects['Game'] as $game): ?>
                                <?php if( isset($_GET['game']) && $_GET['game'] == $game['game_id']):?>
                                    <option value="<?=$game['game_id']?>" selected><?=$game['name']?></option>
                                <?php else: ?>
                                    <option value="<?=$game['game_id']?>"><?=$game['name']?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field is-flex">
                <label class="label is-align-self-center" for="weaponcat">Categoría:</label>
                <div class="control ml-3">
                    <div class="select">
                        <select name="weaponcat">
                            <?php foreach ($selects['WeaponCategory'] as $weaponCat): ?>
                                <?php if( isset($_GET['weaponcat']) && $_GET['weaponcat'] == $weaponCat['wpcategory_id']):?>
                                    <option value="<?=$weaponCat['wpcategory_id']?>" selected><?=$weaponCat['name']?></option>
                                <?php else: ?>
                                    <option value="<?=$weaponCat['wpcategory_id']?>"><?=$weaponCat['name']?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if( isset($_GET['game']) || isset($_GET['weaponcat']) ):?>
                <a href="/admin/clases" class="button ml-3">Ver Todo</a>
            <?php endif; ?>
            <input type="submit" class="button ml-3" value="Filtrar">
        </form>
    </div>
    <table class="table is-fullwidth">
        <thead>
        <tr>
            <th><abbr title="Position">Id</abbr></th>
            <th><abbr title="Won">Juego</abbr></th>
            <th><abbr title="Played">Arma</abbr></th>
            <th><abbr title="Drawn">Categoría</abbr></th>
            <th><abbr title="Drawn">Título</abbr></th>
            <th><abbr title="Drawn">Fecha</abbr></th>
            <th><abbr title="Drawn">Acciones</abbr></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allData as $data): ?>
            <tr>
                <!-- is-selected para seleccionar xd -->
                <th><?=$data['loadout_id']?></th>
                <td ><a href="#" class="is-link"><?=$data['shortName']?></a></td>
                <td><?=$data['weaponName']?></td>
                <td><?=$data['weaponcatName']?></td>
                <td><?=$data['title']?></td>
                <td><?=$data['creation_date']?></td>
                <td>
                    <a href="/admin/clases/editar/<?=$data['loadout_id']?>" class="button is-warning">Editar</a>
                    <button class="button is-danger js-modal-trigger delete-loadout" data-target="modal-js-example">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>

