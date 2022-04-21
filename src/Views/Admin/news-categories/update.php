<?php
/* @var array $allData */
?>

<div class="section p-0 has-background-white-ter">
    <div class="container is-0-widescreen rounded-corners dark-corners p-5">
        <h1 class="title has-text-centered">Agregar Categoría</h1>
        <form  class="" runat="server" method="post" action="<?=BASE_URL?>/admin/editar/categoria" enctype="multipart/form-data" >
            <input type="hidden" name="category_id" value="<?=$allData['category_id']?>">
            <div class="columns is-justify-content-center">
                <div class="column is-4">
                    <div class="field">
                        <label class="label"></label>
                        <div class="control">
                            <input class="input" type="text"  placeholder="Nombre de la categoría" name="category_name" value="<?=$allData['name']?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php include __DIR__."/../session-messages/success-error.phtml"?>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <input type="submit" class="button is-dark" value="Editar">
                </div>
                <div class="control">
                    <a href="<?=BASE_URL?>/admin/categorias" class="button is-link is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

