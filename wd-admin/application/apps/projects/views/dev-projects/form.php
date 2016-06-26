<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>

<ul class="breadcrumb">
    <li><a href="<?php echo base_url() ?>">Home</a></li>
    <li><a href="<?php echo base_url_app() ?>">Projetos</a></li>
    <li class="active"><?php echo $title ?></li>
</ul>
<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?php echo $title ?></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php
                echo getErrors();
                echo form_open(null, ['class' => 'form-horizontal']);
                ?>
                <div class="tab-pane active in" id="home">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nome*</label>
                                <input type="text" name="name" id="dig_name" value="<?php echo set_value('name', $name) ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Diretório*</label>
                                <input type="text" name="dir" id="dir" value="<?php echo set_value('dir', $directory) ?>" class="form-control" <?php
                                if ($this->uri->segment('3') == 'edit') {
                                    echo 'disabled';
                                }
                                ?>>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prefixo para criação de tabelas*</label>
                                <input type="text" name="preffix" id="preffix" maxlength="6" value="<?php echo set_value('preffix', $preffix) ?>" class="form-control" <?php
                                       if ($this->uri->segment('3') == 'edit') {
                                           echo 'disabled';
                                       }
                                ?>>
                            </div>
                            <?php if ($main == 1) { ?><strong>Obs: Projeto principal não possui prefixo.</strong><?php } ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status*</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?php echo set_select('status', '1', ($status == '1')) ?>>Ativado</option>
                                    <option value="0" <?php echo set_select('status', '0', ($status == '0')) ?>>Desativado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($this->uri->segment('3') == 'create') {
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label><input type="checkbox" value="1" name="extract_ci"> Criar pasta com a estrutura do Codeigniter?</label><br>
                            <label><input type="checkbox" value="1" name="main"> Projeto principal?</label>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

                    <div class="form-group text-right">
                        <input class="btn btn-primary" value="Salvar" name="send" type="submit">
                    </div>
                </div>
<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
