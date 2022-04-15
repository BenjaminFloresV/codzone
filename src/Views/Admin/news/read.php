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
        <form class="is-flex is-justify-content-end pt-3 pr-3" method="GET" action="http://localhost:8001/admin/noticias">
            <div class="field is-flex">
                <label class="label is-align-self-center" for="weaponcat">Categoría:</label>
                <div class="control ml-3">
                    <div class="select">
                        <select name="category">
                            <?php foreach ($selects as $category): ?>
                                <?php if( isset($_GET['category']) && $_GET['category'] == $category['category_id']):?>
                                    <option value="<?=$category['category_id']?>" selected><?=$category['name']?></option>
                                <?php else: ?>
                                    <option value="<?=$category['category_id']?>"><?=$category['name']?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if( isset($_GET['category']) ):?>
                <a href="/admin/noticias" class="button ml-3">Ver Todo</a>
            <?php endif; ?>
            <input type="submit" class="button ml-3" value="Filtrar">
        </form>
    </div>
    <?php include __DIR__."/../session-messages/success-error.phtml"?>
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
                <th><?=$data['news_id']?></th>
                <td><?=$data['images_id']?></td>
                <td ><a href="#" class="is-link"><?=$data['categoryName']?></a></td>
                <td><?=$data['title']?></td>
                <td><?=$data['creation_date']?></td>
                <td>
                    <a href="/admin/noticias/editar/<?=$data['news_id']?>" class="button is-warning">Editar</a>
                    <button class="button is-danger js-modal-trigger delete-news" data-target="modal-js-example">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="/js/modal"></script>

